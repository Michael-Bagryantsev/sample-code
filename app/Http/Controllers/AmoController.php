<?php

namespace App\Http\Controllers;

use App\Helpers\{AmoHelper, TelegramHelper};
use Illuminate\Http\Request;
use App\Models\{
    AccessLog, AnyLog, CustomersChat, UsersFb, UsersVip, BillingPlan
};
use AmoCRM\Client as AmoClient;
use Telegram\Bot\Api;
use Sheets;
use Google;
use DB;
use DateTime;

class AmoController extends Controller
{
    public function newLeadManager(Request $request)
    {
        AccessLog::logRequest('amo');

        $ids = AmoHelper::getPostOrdersIds();
        if (!empty($ids)) {
            $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));
            $leadList = AmoHelper::getAmoLeadsByIds($amo, $ids);

            try {
                Sheets::setService(Google::make('sheets'));
                Sheets::spreadsheet(config('google.new_leads.spreadsheet_id'));
                $values = Sheets::sheetById(config('google.new_leads.sheet_id'))->all();
            } catch (\Google_Service_Exception $e) {
                $values = [];
            }

            foreach ($leadList as $lead) {
                if (!empty($lead['main_contact_id'])) {
                    $contact = AmoHelper::getAmoContactById($amo, $lead['main_contact_id']);
                    $customerDetails = AmoHelper::getCustomerDetailsFromContact($contact);

                    $customer = UsersFb::where('amocrm_contact_id', $lead['main_contact_id'])->first();
                    if (is_null($customer) && $customerDetails->customerFrom === 'telegram') {
                        $customer = new UsersFb();
                        $customer->amocrm_contact_id = $lead['main_contact_id'];
                        $customer->amocrm_lead_id = $lead['id'];
                        $customer->telegram_chat_id = $customerDetails->telegram_chat_id;
                        $customer->telegram_username = $customerDetails->telegram_username;
                        $customer->first_name = $customerDetails->name;
                        $customer->date_registered = date('Y-m-d H:i:s');
                    }
                    if (!is_null($customer)) {
                        $customer->lead_manager = date('Y-m-d H:i:s');
                        $customer->customer_from = $customerDetails->customerFrom;
                        $customer->customer_chat_status = 'chat_manager';
                        $customer->save();
                    } else {
                        $telegram = new Api(config('logging.telegram_token'));
                        $telegram->sendMessage(['chat_id' => config('logging.telegram_chat_id'),
                            'text' => 'Hook "New Lead Manager" failed search amocrm_contact_id #' . $lead['main_contact_id']]);
                    }

                    $rowNum = false;
                    $date = date('d.m.Y', $lead['last_modified']);
                    foreach ($values as $key => $value) {
                        if ($value[0] === $date) {
                            $rowNum = $key;
                        }
                    }

                    if (!in_array($customerDetails->telegram_username, config('app.TELEGRAM_DEBUG_ACCOUNTS'))) {
                        $dbDate = date('Y-m-d', $lead['last_modified']);
                        switch ($customerDetails->customerFrom) {
                            case 'telegram':
                                DB::statement("INSERT INTO stats_per_day (day, new_leads_manager_telegram) VALUES (?, ?) " .
                                    " ON DUPLICATE KEY UPDATE new_leads_manager_telegram = new_leads_manager_telegram + 1", ([$dbDate, 1]));
                                $colNum = 2;
                                $colLetter = 'C';
                                break;
                            case 'facebook':
                                DB::statement("INSERT INTO stats_per_day (day, new_leads_manager_facebook) VALUES (?, ?) " .
                                    " ON DUPLICATE KEY UPDATE new_leads_manager_facebook = new_leads_manager_facebook + 1", ([$dbDate, 1]));
                                $colNum = 1;
                                $colLetter = 'B';
                                break;
                        }

                        if ($rowNum !== false && isset($colNum)) {
                            $values[$rowNum][$colNum] = (int)$values[$rowNum][$colNum] + 1;
                            try {
                                Sheets::sheetById(config('google.new_leads.sheet_id'))->range($colLetter . ($rowNum + 1))->update([[$values[$rowNum][$colNum]]]);
                            } catch (\Google_Service_Exception $e) {
                                AnyLog::insert(['log' => 'Google update error. '.$colLetter. ($rowNum + 1) . ' Data: ' . $values[$rowNum][$colNum] . ' Response: ' . json_encode($e->getMessage()), 'date_added' => time()]);
                            }
                        }
                    }
                }

            }
        }

        return;
    }

    public function newLeadBot(Request $request)
    {
        AccessLog::logRequest('amo');

        $ids = AmoHelper::getPostOrdersIds();
        if (!empty($ids)) {
            $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));
            $leadList = AmoHelper::getAmoLeadsByIds($amo, $ids);
            foreach ($leadList as $lead) {
                if (!empty($lead['main_contact_id'])) {
                    $contact = AmoHelper::getAmoContactById($amo, $lead['main_contact_id']);
                    $customerDetails = AmoHelper::getCustomerDetailsFromContact($contact);

                    $customer = UsersFb::where('amocrm_contact_id', $lead['main_contact_id'])->first();
                    if (is_null($customer) && $customerDetails->customerFrom === 'telegram') {
                        $customer = new UsersFb();
                        $customer->amocrm_contact_id = $lead['main_contact_id'];
                        $customer->amocrm_lead_id = $lead['id'];
                        $customer->telegram_chat_id = $customerDetails->telegram_chat_id;
                        $customer->telegram_username = $customerDetails->telegram_username;
                        $customer->first_name = $customerDetails->name;
                        $customer->date_registered = date('Y-m-d H:i:s');
                    }
                    if (!is_null($customer)) {
                        $customer->lead_created = date('Y-m-d H:i:s');
                        $customer->customer_from = $customerDetails->customerFrom;
                        $customer->customer_chat_status = 'chat_bot';
                        $customer->save();
                    }


                    if (!in_array($customerDetails->telegram_username, config('app.TELEGRAM_DEBUG_ACCOUNTS'))) {
                        $dbDate = date('Y-m-d', $lead['last_modified']);
                        switch ($customerDetails->customerFrom) {
                            case 'telegram':
                                DB::statement("INSERT INTO stats_per_day (day, new_leads_bot_telegram) VALUES (?, ?) " .
                                    " ON DUPLICATE KEY UPDATE new_leads_bot_telegram = new_leads_bot_telegram + 1", ([$dbDate, 1]));
                                break;
                            case 'facebook':
                                DB::statement("INSERT INTO stats_per_day (day, new_leads_bot_facebook) VALUES (?, ?) " .
                                    " ON DUPLICATE KEY UPDATE new_leads_bot_facebook = new_leads_bot_facebook + 1", ([$dbDate, 1]));
                                break;
                        }
                    }
                }
            }
        }

        return;
    }

    public function completedLead()
    {
        AccessLog::logRequest('amo');

        $ids = AmoHelper::getPostOrdersIds();
        if (!empty($ids)) {
            $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));
            $leadList = AmoHelper::getAmoLeadsByIds($amo, $ids);
            foreach ($leadList as $lead) {
                if (!empty($lead['main_contact_id'])) {
                    $contact = AmoHelper::getAmoContactById($amo, $lead['main_contact_id']);
                    $customerDetails = AmoHelper::getCustomerDetailsFromContact($contact);

                    $customer = UsersFb::where('amocrm_contact_id', $lead['main_contact_id'])->first();
                    if (is_null($customer) && $customerDetails->customerFrom === 'telegram') {
                        $customer = new UsersFb();
                        $customer->amocrm_contact_id = $lead['main_contact_id'];
                        $customer->amocrm_lead_id = $lead['id'];
                        $customer->telegram_chat_id = $customerDetails->telegram_chat_id;
                        $customer->telegram_username = $customerDetails->telegram_username;
                        $customer->first_name = $customerDetails->name;
                        $customer->date_registered = date('Y-m-d H:i:s');
                    }
                    if (!is_null($customer)) {
                        $customer->lead_completed = date('Y-m-d H:i:s');
                        $customer->customer_from = $customerDetails->customerFrom;
                        $customer->customer_chat_status = 'paid';
                        $customer->save();

                        $vip = new UsersVip();
                        $vip->users_fb_id = $customer->id;
                        $vip->amocrm_lead_id = $customer->amocrm_lead_id;
                        $vip->amocrm_contact_id = $customer->amocrm_contact_id;
                        $vip->first_name = $customer->first_name;
                        $vip->last_name = $customer->last_name;
                        $vip->telegram_username = $customer->telegram_username;
                        $vip->telegram_chat_id = $customer->telegram_chat_id;
                        $vip->vip_from = time();
                        $vip->customer_from = isset($customer->customer_from) ? $customer->customer_from : null;
                        if (isset($lead['custom_fields']) && sizeof($lead['custom_fields']) > 0) {
                            foreach ($lead['custom_fields'] as $field) {
                                switch ($field['id']) {
                                    case '408869'://Тарифный план
                                        $vip->billing_plan_id = config('app.AMO_PLANS')[$field['values'][0]['enum']]['billing_plan_id'];
                                        break;
                                    case '415267'://ВАЛЮТА ОПЛАТЫ
                                        switch ($field['values'][0]['value']) {
                                            case 'BTC':
                                                $vip->currency_id = 1;
                                                break;
                                            case 'ETH':
                                                $vip->currency_id = 2;
                                                break;
                                        }
                                        break;
                                    case '415265'://ID ТРАНЗАКЦИИ
                                        $vip->transaction_id = $field['values'][0]['value'];
                                        break;
                                }
                            }
                        }
                        if (isset($vip->billing_plan_id) && $vip->billing_plan_id > 0) {
                            $plan = BillingPlan::find($vip->billing_plan_id);
                            if (!is_null($plan)) {
                                $date = DateTime::createFromFormat('U', $vip->vip_from);
                                $date->add(new DateInterval('P' . intval($plan->prolong_years) . 'Y' . intval($plan->prolong_months) . 'M' . intval($plan->prolong_days) . 'DT' . intval($plan->prolong_hours) . 'H'));
                                $vip->vip_till  = $date->format('Y-m-d');
                            }
                        }
                        $vip->save();
                    } else {
                        $telegram = new Api(config('logging.telegram_token'));
                        $telegram->sendMessage(['chat_id' => config('logging.telegram_chat_id'),
                            'text' => 'Hook "Lead Completed" failed search amocrm_contact_id #' . $lead['main_contact_id']]);
                    }


                    if (!in_array($customerDetails->telegram_username, config('app.TELEGRAM_DEBUG_ACCOUNTS'))) {
                        $dbDate = date('Y-m-d', $lead['last_modified']);
                        switch ($customerDetails->customerFrom) {
                            case 'telegram':
                                DB::statement("INSERT INTO stats_per_day (day, leads_completed_telegram) VALUES (?, ?) " .
                                    " ON DUPLICATE KEY UPDATE leads_completed_telegram = leads_completed_telegram + 1", ([$dbDate, 1]));
                                break;
                            case 'facebook':
                                DB::statement("INSERT INTO stats_per_day (day, leads_completed_facebook) VALUES (?, ?) " .
                                    " ON DUPLICATE KEY UPDATE leads_completed_facebook = leads_completed_facebook + 1", ([$dbDate, 1]));
                                break;
                        }
                    }
                }
            }
        }

        return;
    }

    public function receive(Request $request)
    {
        AccessLog::logRequest('amo');

        $inputJson = file_get_contents('php://input');

        if  (!empty($inputJson)) {
            $data = json_decode($inputJson);

            if (isset($data->conversation_id) && substr($data->conversation_id, 0, 8) === 'telegram'
                && isset($data->type)) {
                $telegram = new Api(config('app.TELEGRAM_TOKEN'));
                $chatId = substr($data->conversation_id, 8);

                $params = [
                    'telegram' => $telegram,
                    'from' => 'manager',
                    'chat_id' => $chatId,
                    'type' => $data->type
                ];

                switch ($data->type) {
                    case 'text':
                        $params['text'] = $data->text;
                        break;
                    case 'picture':
                        $params['photo'] = $data->media;
                        break;
                    default:
                        break;
                }

                TelegramHelper::sendMessage($params);

                $customer = UsersFb::where('telegram_chat_id', $chatId)->first();
                if ($customer && $customer->customer_chat_status === 'chat_bot') {
                    $customer->customer_chat_status = 'chat_manager';
                    $customer->save();
                }
            }
        }

        return;
    }
}
