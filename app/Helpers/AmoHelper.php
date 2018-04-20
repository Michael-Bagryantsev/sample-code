<?php

namespace App\Helpers;

use stdClass;
use App\Models\AnyLog;

class AmoHelper
{
    public static function getPostOrdersIds()
    {
        $ids = [];

        $orders = [];
        if (isset($_POST['leads']['status'])) {
            $orders = $_POST['leads']['status'];
        }
        if (isset($_POST['leads']['add'])) {
            $orders = $_POST['leads']['add'];
        }

        foreach($orders as $order) {
            if (isset($order['id']) && !empty($order['id'])) {
                $ids[] = $order['id'];
            }
        }

        return $ids;
    }

    public static function getAmoLeadsByIds($amo, $ids, $attempts = 50)
    {
        $i = 0;
        $leadList = [];
        while (empty($leadList) && $i < $attempts) {
            $leadList = $amo->lead->apiList([
                'id' => $ids,
            ]);
            $i++;
            sleep(1);
        }

        if (empty($contact)) {
            AnyLog::insert(['log' => 'Amo leads  #' . json_encode($ids) . ' search failed', 'date_added' => time()]);
        }

        return $leadList;
    }

    public static function getAmoLeadsByQuery($amo, $query, $attempts = 100)
    {
        $i = 0;
        $leadList = [];
        while (empty($leadList) && $i < $attempts) {
            $leadList = $amo->lead->apiList([
                'query' => $query,
            ]);
            $i++;
            sleep(1);
        }

        if (empty($contact)) {
            AnyLog::insert(['log' => 'Amo leads by "' . json_encode($query) . '" search failed', 'date_added' => time()]);
        }

        return $leadList;
    }

    public static function getAmoContactsByQuery($amo, $query, $attempts = 100)
    {
        $i = 0;
        $leadList = [];
        while (empty($leadList) && $i < $attempts) {
            $leadList = $amo->contact->apiList([
                'query' => $query,
            ]);
            $i++;
            sleep(1);
        }

        if (empty($contact)) {
            AnyLog::insert(['log' => 'Amo leads by "' . json_encode($query) . '" search failed', 'date_added' => time()]);
        }

        return $leadList;
    }

    public static function getAmoContactById($amo, $id, $attempts = 50)
    {
        $i = 0;
        $contact = [];
        while (empty($contact) && $i < $attempts) {
            $contact = $amo->contact->apiList([
                'id' => [$id],
            ]);
            $i++;
            sleep(1);
        }

        if (empty($contact)) {
            AnyLog::insert(['log' => 'Amo contact  #' . $id . ' search failed', 'date_added' => time()]);
        }

        return $contact;
    }

    public static function updateLeadStatus($amo, $leadId, $statusId, $attempts = 100)
    {
        $leadUpdateResult = false;
        $i = 0;
        while ($leadUpdateResult === false && $i < $attempts) {
            sleep(1);
            $lead = $amo->lead;
            $lead['status_id'] = $statusId;
            $leadUpdateResult = $lead->apiUpdate((int)$leadId, 'now');
            $i++;
        }

        if ($leadUpdateResult === false) {
            AnyLog::insert(['log' => 'Amo lead #' . $leadId . ' status update failed', 'date_added' => time()]);
        }

        return $leadUpdateResult;
    }

    public static function updateLead($amo, $leadData, $attempts = 100)
    {
        $leadUpdateResult = false;
        $i = 0;
        while ($leadUpdateResult === false && $i < $attempts) {
            sleep(1);
            $lead = $amo->lead;
            if (isset($leadData['status_id'])) {
                $lead['status_id'] = $leadData['status_id'];
            }
            if (isset($leadData['tags'])) {
                $lead['tags'] = $leadData['tags'];
            }
            $leadUpdateResult = $lead->apiUpdate((int)$leadData['id'], 'now');
            $i++;
        }

        if ($leadUpdateResult === false) {
            AnyLog::insert(['log' => 'Amo lead #' . $leadData['id'] . ' update failed', 'date_added' => time()]);
        }

        return $leadUpdateResult;
    }

    public static function getAmoContactByTelegramUsername($amo, $userName, $attempts = 1)
    {
        $i = 0;
        $contactSearchResult = [];
        $amoContact = false;
        while (empty($contactSearchResult) && $i < $attempts) {
            $contactSearchResult = $amo->contact->apiList([
                'query' => $userName,
            ]);
            if (!empty($contactSearchResult)) {
                foreach ($contactSearchResult as $contact) {
                    if (isset($contact['custom_fields'])) {
                        foreach ($contact['custom_fields'] as $field) {
                            if ($field['name'] === 'telegram_username' && $field['values'][0]['value'] === $userName) {
                                $amoContact = $contact;
                            }
                        }
                    }
                }
            }
            $i++;
            sleep(1);
        }

        return $amoContact;
    }

    public static function getCustomerDetailsFromLead($lead)
    {
        $customerDetails = new stdClass();

        if (isset($lead) && isset($lead['custom_fields'])) {
            foreach ($lead['custom_fields'] as $field) {
                switch ($field['id']) {
                    case '408869'://Тарифный план
                        $customerDetails->billing_plan_id = config('app.AMO_PLANS')[$field['values'][0]['enum']]['billing_plan_id'];
                        break;
                    default:
                        break;
                }
            }
        }

        return $customerDetails;
    }

    public static function getCustomerDetailsFromContact($contact)
    {
        $customerDetails = new stdClass();
        $customerDetails->customerFrom = null;
        $customerDetails->telegram_username = null;
        $customerDetails->telegram_chat_id = null;
        $customerDetails->name = null;

        if (isset($contact[0]) && isset($contact[0]['name'])) {
            $customerDetails->name = $contact[0]['name'];
        }
        if (isset($contact[0]) && isset($contact[0]['custom_fields'])) {
            foreach ($contact[0]['custom_fields'] as $field) {
                switch ($field['name']) {
                    case 'ID ТРАНЗАКЦИИ':
                        $customerDetails->transaction_id = $field['values'][0]['value'];
                        break;
                    case 'telegram_username':
                        $customerDetails->telegram_username = $field['values'][0]['value'];
                        $customerDetails->customerFrom = 'telegram';
                        break;
                    case 'telegram_chat_id':
                        $customerDetails->customerFrom = 'telegram';
                        $customerDetails->telegram_chat_id = $field['values'][0]['value'];
                        break;
                    case 'fb_psid':
                        $customerDetails->customerFrom = 'facebook';
                        break;
                    default:
                        break;
                }
            }
        }

        if (isset($contact[0]) && isset($contact[0]['profiles']) && (isset($contact[0]['profiles']['Telegram']))) {
            $customerDetails->customerFrom = 'telegram';
        }

        if (isset($contact[0]) && isset($contact[0]['profiles']) && (isset($contact[0]['profiles']['Facebook']))) {
            $customerDetails->customerFrom = 'facebook';
        }


        return $customerDetails;
    }

    public static function getAccountId($subdomain, $login, $hash)
    {
        $cookieFile = tempnam(sys_get_temp_dir(), 'amo_');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://" . $subdomain . ".amocrm.ru/private/api/v2/json/accounts/current?amojo=Y&USER_LOGIN=" . $login . "&USER_HASH=" . $hash,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_COOKIEFILE => $cookieFile,
            CURLOPT_COOKIEJAR => $cookieFile,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return false;
        } else {
            $accountId = json_decode($response, true)['response']['account']['amojo_id'];

            if (!empty($accountId)) {
                return $accountId;
            } else {
                return false;
            }
        }
    }

    public static function getScopeId($account_id, $channelId, $secret = '')
    {

        $url = 'https://amojo.amocrm.ru/v2/origin/custom/' . $channelId . '/connect';

        $body = json_encode([
            'account_id' => $account_id
        ]);

        $result = self::sendRequest($url, $body, $secret);

        if ($result->err) {
            return false;
        } else {
            $resultObj = json_decode($result->response);
            if (isset($resultObj->scope_id)) {
                return $resultObj->scope_id;
            } else {
                return false;
            }
        }

    }

    public static function sendRequest($url, $body, $secret = '')
    {

        $signature = hash_hmac('sha1', $body, $secret);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "x-signature: " . $signature
            ),
        ));
        $response = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);

        $result = new \stdClass();
        $result->err = $err;
        $result->response = $response;

        return $result;
    }

    public static function parseTelegramName($name) {

        $nameParts = explode('@', $name);

        if (sizeof($nameParts) === 3) {
            return [
                'username' => $nameParts[1],
                'chat_id' => $nameParts[2]
            ];
        } else {
            return false;
        }
    }

    public static function parseTagsArray($amoTags) {

        $tags = [];

        foreach ($amoTags as $tag) {
            if (isset($tag['name'])) {
                $tags[] = $tag['name'];
            }
        }

        return $tags;
    }
}