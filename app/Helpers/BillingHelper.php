<?php
/**
 * Created by IntelliJ IDEA.
 * User: michael
 * Date: 05/03/2018
 * Time: 11:16
 */

namespace App\Helpers;

use stdClass;

class BillingHelper
{
    public static function generatePaymentAddress($amount, $currency)
    {
        $billingResult = new stdClass();

        $result = self::sendRequest(config('app.BILLING_BASE_URL') . '/users/index/create');
        $billingResult->customer = json_decode($result->response);

        if ($billingResult->customer->status === 'success') {

            $body = [
                'currency' => $currency,
                'sum' => $amount
            ];

            $result = self::sendRequest(config('app.BILLING_BASE_URL') . '/order/index/create?token=' . $billingResult->customer->token, http_build_query($body));
            $billingResult->order = json_decode($result->response);

            if ($billingResult->order->status === 'success') {
                $body = [];
                self::sendRequest(config('app.BILLING_BASE_URL') . '/order/index/mark_paid?token=' . $billingResult->customer->token, http_build_query($body));

                return $billingResult;
            }
        }

        return false;
    }

    public static function getStatusByCode($statusCode = -1)
    {
        foreach (config('app.BILLING_STATUSES') as $name => $code) {
            if ($statusCode == $code) {
                return ucwords(strtolower($name));
            }
        }

        return '';
    }

    public static function sendRequest($url, $body = '')
    {
        $payloadObj = new stdClass();
        $payloadObj->client_id = config('app.BILLING_CLIENT_ID');
        $payload = json_encode($payloadObj);
        $signature = hash_hmac('sha256', $payload, config('app.BILLING_SECRET_KEY'));
        $token = base64_encode($payload) . '.' . base64_encode($signature);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Authorization: " . $token
            ),
        ));
        $response = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);

        $result = new stdClass();
        $result->err = $err;
        $result->response = $response;

        return $result;
    }

}