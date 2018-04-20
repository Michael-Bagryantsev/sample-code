<?php

namespace App\Helpers;

use App\Models\{
    BillingCurrency, BillingCustomer, BillingOrder, BillingPlan, SubscriptionPlan, UsersFb
};

class BotHelper
{
    public static function showSubscriptions($params)
    {

        $generatedMessage = '';
        $items = BillingPlan::where('is_public', 1)->orderBy('plan_order')->get();
        foreach ($items as $item) {
            if (!empty($item->plan_name)) {
                $generatedMessage .= $item->plan_name . ": " . $item->plan_description . "\n\n";
            }
        }

        $result = preg_replace('/:subscriptionsList/msi', $generatedMessage, $params->predefinedMessage);

        return $result;
    }

    public static function showSelectedSubscription($params)
    {

        $result = $params->predefinedMessage;

        $subscription = BillingPlan::where('button_id', $params->button_id)->first();
        if (!is_null($subscription)) {

            $customer = UsersFb::where('id', $params->customer_id)->first();
            if (!is_null($customer)) {
                $customer->plan_id = $subscription->id;
                $customer->save();
            }

            $result = preg_replace('/:subscriptionName/msi', $subscription->plan_name, $result);
            $result = preg_replace('/:firstName/msi', $params->firstName, $result);
        }

        return $result;
    }

    public static function showPaymentAddress($params)
    {

        $result = $params->predefinedMessage;

        $currency = BillingCurrency::where('button_id', $params->button_id)->first();
        if (!is_null($currency)) {
            $customer = UsersFb::where('id', $params->customer_id)->first();
            if (!is_null($customer)) {
                $customer->currency_id = $currency->id;
                $customer->save();

                $plan = BillingPlan::where('id', $customer->plan_id)->first();

                $billingResponse = BillingHelper::generatePaymentAddress($plan->price, $currency->code);

                if ($billingResponse !== false) {

                    /*
                     * Add billing customer & order code here
                     */
                    $billingCustomer = new BillingCustomer();
                    $billingCustomer->date_created = date('Y-m-d');
                    $billingCustomer->first_name = $customer->first_name;
                    $billingCustomer->last_name = $customer->last_name;
                    $billingCustomer->billing_token = $billingResponse->customer->token;
                    $billingCustomer->billing_user_id = $billingResponse->customer->user_id;
                    $billingCustomer->save();

                    $billingOrder = new BillingOrder();
                    $billingOrder->status = $billingResponse->order->data->status_id;
                    $billingOrder->date_created = date('Y-m-d');
                    $billingOrder->date_expiration = date('Y-m-d', time() + config('app.BILLING_WAIT_TIME'));
                    $billingOrder->currency_id = $currency->id;
                    $billingOrder->customer_id = $billingCustomer->id;
                    $billingOrder->plan_id = $plan->id;
                    $billingOrder->amount = number_format($billingResponse->order->data->sum_to_pay, 3, '.', '');
                    $billingOrder->address = $billingResponse->order->data->address;
                    $billingOrder->save();

                    $customer->customer_chat_status = 'paying';
                    $customer->customer_id = $billingCustomer->id;
                    $customer->save();

                    if ($params->amo_lead_id > 0) {
                        AmoHelper::updateLead($params->amo, ['id' => (int)$params->amo_lead_id, 'status_id' => (int)config('app.AMO_STATUSES')[2]['key']]);
                    }

                    $result = preg_replace('/:amount/msi', $billingResponse->order->data->sum_to_pay . ' ' . $currency->code, $result);
                    $result = preg_replace('/:address/msi', $billingResponse->order->data->address, $result);
                }

            }
        }

        return $result;
    }

    public static function getBotanUrl($params)
    {

        $result = $params->predefinedMessage;

        $resultUrl = config('app.cryptomaker_url');

        $result = preg_replace('/:shortenUrl/msi', $resultUrl, $result);

        return $result;
    }
}