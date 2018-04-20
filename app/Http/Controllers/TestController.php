<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sheets;
use Google;
use DB;

use App\Models\{
    Customer, Order, SubscriptionPlan, CustomersChat, AnyLog, UsersFb, UsersVip
};
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use AmoCRM\Client as AmoClient;
use App\Helpers\{BillingHelper, AmoHelper};
use Illuminate\Support\Facades\Log;

use App\Cryptomaker\Flows\DefaultFlow;

class TestController extends Controller
{

    public function test()
    {

        $flow = DefaultFlow::getFlow();

        echo '<pre>';
        var_dump($flow);

        /*
        $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));

        $leadList = $amo->lead->apiList([
            'id' => [9989775],
        ]);

        var_dump($leadList);
*/
        /*
        $res = DB::select('select distinct v.* from users_vip v inner join users_fb f on f.first_name=v.first_name and f.last_name=v.last_name where v.vip_till is null;
');

        foreach ($res as $k =>$r) {
            echo ($k+1) . ' ' . $r->first_name . ' ' . $r->last_name . ' @' . $r->telegram_username . ' Started:' . date('Y-m-d', $r->vip_from) .
                ' <a href="https://topico.amocrm.ru/leads/detail/' . $r->amocrm_lead_id . '" target="blank">' . $r->amocrm_lead_id . '</a>' .
                '<br />';
        }
*/
        return;
    }

    /**
     * link vip from telega channel to amo leads
     */
    public function linkTelegramVipLeads20180409()
    {
        echo '<pre>';

        $users = UsersVip::all();

        $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));

        foreach ($users as $user) {
            if (!is_null($user->amocrm_lead_id)) {
                sleep(1);
                $leads = $amo->lead->apiList([
                    'id' => [$user->amocrm_lead_id]
                ]);

                if  (sizeof($leads) === 1) {
                    if (isset($leads[0]['custom_fields']) && sizeof($leads[0]['custom_fields']) > 0) {

                        foreach ($leads[0]['custom_fields'] as $field) {
                            switch ($field['id']) {
                                case '408869'://Тарифный план
                                    $user->billing_plan_id = config('app.AMO_PLANS')[$field['values'][0]['enum']]['billing_plan_id'];
                                    break;
                                case '415267'://ВАЛЮТА ОПЛАТЫ
                                    switch ($field['values'][0]['value']) {
                                        case 'BTC':
                                            $user->currency_id = 1;
                                            break;
                                        case 'ETH':
                                            $user->currency_id = 2;
                                            break;
                                    }
                                    break;
                                case '415265'://ID ТРАНЗАКЦИИ
                                    $user->transaction_id = $field['values'][0]['value'];
                                    break;

                            }
                        }

                        $user->save();

                    }
                }

            }
        }

        echo 'done';

        return;
    }

    /**
     * link vip from telega channel to amo contacts
     */
    public function linkTelegramVipContacts20180409()
    {
        echo '<pre>';

        $users = UsersVip::all();

        $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));

        foreach ($users as $user) {
            if (!is_null($user->telegram_username)) {
                sleep(1);
                $contacts = $amo->contact->apiList([
                    'query' => $user->telegram_username,
                ]);

                if  (sizeof($contacts) === 1) {
                    if (isset($contacts[0]['custom_fields']) && sizeof($contacts[0]['custom_fields']) > 0) {
                        $contactGood = false;
                        foreach ($contacts[0]['custom_fields'] as $field) {
                            if ($field['name'] === 'telegram_username' && $field['values'][0]['value'] === $user->telegram_username) {
                                $contactGood = true;
                            }
                        }

                        if ($contactGood === true) {

                            foreach ($contacts[0]['custom_fields'] as $field) {
                                switch ($field['name']) {
                                    case 'telegram_chat_id':
                                            if (isset($field['values']) && isset($field['values'][0]) &&
                                                isset($field['values'][0]['value']) && !empty($field['values'][0]['value'])) {
                                                $user->telegram_chat_id = $field['values'][0]['value'];
                                            }
                                        break;

                                }
                            }

                            $user->amocrm_contact_id = $contacts[0]['id'];

                            if (sizeof($contacts[0]['linked_leads_id']) > 0) {
                                $user->amocrm_lead_id = end($contacts[0]['linked_leads_id']);
                            }

                            $user->save();
                        }
                    }
                }

            }
        }

        echo 'done';

        return;
    }

    /**
     * lost compleated leads
     */
    public function migration20180403()
    {
        echo '<pre>';

        $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));


        $leadList = $amo->lead->apiList([
            'status' => '142',
        ]);

        $notFound = 0;
        $found = 0;
        foreach ($leadList as $lead) {
            echo '<b>' . $lead['id'] . '</b> - ';

            $user = UsersFb::where('amocrm_lead_id', $lead['id'])->first();
            if (!is_null($user)) {
                echo 'DB: ' . $user->id . ' Vip: ' . ($user->is_vip == '1' ? 'Yes' : 'No') . ' From: ' . $user->customer_from;
                $found++;
            } else {
                $notFound++;
            }

            if ($lead['main_contact_id'] === false) {
                echo 'No Contact ';
            } else {
                $contact = $amo->contact->apiList([
                    'id' => [$lead['main_contact_id']]
                ]);

                if (!empty($contact)) {
                    $contactDetails = AmoHelper::getCustomerDetailsFromLead($lead);
                    var_dump($contactDetails);

                    $contactDetails = AmoHelper::getCustomerDetailsFromContact($contact);
                    var_dump($contactDetails);

                    var_dump($lead, $contact);
                }
                return;

            }

            echo '<br/>';

        }

        echo '<br />';
        echo 'Total: ' . sizeof($leadList) . '<br />';
        echo 'Not found: ' . $notFound . '<br />';
        echo 'Found: ' . $found . '<br />';

        return;
    }

    /**
     * combine admins
     */
    public function migration20180328()
    {
        /*
        $plansIds = [
            1 => 2,
            2 => 4,
            3 => 1
        ];

        echo '<pre>';


        DB::connection('mysql')->getPdo()->query("update users_fb set customer_from='facebook';");

        $customers = DB::connection('mysql_2')->select("select * from customers where customer_from='telegram';");

        foreach ($customers as $customer) {

            if (strpos($customer->customer_name, ' ') > 0) {
                $firstname = trim(substr($customer->customer_name, 0, strpos($customer->customer_name, ' ')));
                $lastname = trim(substr($customer->customer_name, strpos($customer->customer_name, ' ')));
            } else {
                $firstname = $customer->customer_name;
                $lastname = '';
            }
            $firstname = DB::connection()->getPdo()->quote($firstname);
            $lastname = DB::connection()->getPdo()->quote($lastname);

            $date_registered = date('Y-m-d H:i:s', ((int)$customer->added_time > 0 ? $customer->added_time : time()));
            $date_registered = "'" . $date_registered . "'";

            $firstMessage = DB::connection('mysql_2')->select("select min(message_time) as mintime from customers_chats where customer_id='" . $customer->id . "';")[0]->mintime;
            $lastMessage = DB::connection('mysql_2')->select("select max(message_time) as maxtime from customers_chats where customer_id='" . $customer->id . "';")[0]->maxtime;
            $date_first_message  = !is_null($firstMessage) ? "'" . date('Y-m-d H:i:s', $firstMessage) . "'" : 'NULL';
            $date_last_message  = !is_null($firstMessage) ? "'" . date('Y-m-d H:i:s', $lastMessage) . "'" : 'NULL';

            $lead_created  = !is_null($customer->lead_created) ? "'" . date('Y-m-d H:i:s', $customer->lead_created) . "'" : 'NULL';
            $lead_manager  = !is_null($customer->lead_manager) ? "'" . date('Y-m-d H:i:s', $customer->lead_manager) . "'" : 'NULL';
            $lead_completed  = !is_null($customer->lead_completed) ? "'" . date('Y-m-d H:i:s', $customer->lead_completed) . "'" : 'NULL';

            $plan_id = !is_null($customer->plan_id) ? $plansIds[$customer->plan_id] : '0';

            if (!is_null($customer->billing_user_id) || !is_null($customer->billing_token)) {
                $order = DB::connection('mysql_2')->select("select * from orders where customer_id='" . $customer->id . "';");
                if (sizeof($order) > 0) {
                    $order = end($order);

                    $c_date_created = !is_null($order->order_time) ? "'" . date('Y-m-d H:i:s', $order->order_time) . "'" : 'NULL';

                    $date_paid_till = (int)$customer->vip_till > 0 ? "'" . date('Y-m-d H:i:s', $customer->vip_till) . "'" : 'NULL';

                    $billing_user_id = !is_null($customer->billing_user_id) ? $customer->billing_user_id : 'NULL';

                    DB::connection('mysql')->getPdo()->query("insert into billing_customers (" .
                        "date_created, first_name, last_name, billing_token, billing_user_id, date_paid_till) values (" .
                         $c_date_created . ", " . $firstname . ", " . $lastname . ", " .
                        "'" . $customer->billing_token . "', '" . $billing_user_id . "', " . $date_paid_till . ");");

                    $billingCustomerId = DB::connection('mysql')->getPdo()->lastInsertId();


                    $o_date_expiration = !is_null($order->order_time) ? "'" . date('Y-m-d H:i:s', $order->order_time + 3600) . "'" : 'NULL';

                    $status = $order->order_status === 'paid' ? 4 : 6;

                    DB::connection('mysql')->getPdo()->query("insert into billing_orders (" .
                        "date_created, date_expiration, status, amount, address, sync_id, " .
                        "currency_id, customer_id, plan_id) values (" .
                        $c_date_created . ", " . $o_date_expiration . ", '" . $status . "', '" .
                        $order->amount . "', '" . $order->wallet . "', '" . $billing_user_id . "', " .
                        " '". (!is_null($customer->currency_id) ? $customer->currency_id : 0) . "', '" . $billingCustomerId ."', '" . $plan_id . "');");
                }
            } else {
                $billingCustomerId = 'NULL';
            }

            $is_vip = (int)$customer->vip_till > time() ? 1 : 0;

            $customer->amo_id = !is_null($customer->amo_id) ? $customer->amo_id : 'NULL';
            $customer->amo_lead_id = !is_null($customer->amo_lead_id) ? $customer->amo_lead_id : 'NULL';
            $customer->telegram_chat_id = !is_null($customer->telegram_chat_id) ? DB::connection()->getPdo()->quote($customer->telegram_chat_id) : 'NULL';
            $customer->telegram_username = !is_null($customer->telegram_username) ? DB::connection()->getPdo()->quote($customer->telegram_username) : 'NULL';
            $customer->customer_chat_status = !is_null($customer->customer_chat_status) ? $customer->customer_chat_status : 'NULL';
            $customer->currency_id = !is_null($customer->currency_id) ? $customer->currency_id : 'NULL';

            DB::connection('mysql')->getPdo()->query("insert into users_fb (first_name, last_name, date_registered, amocrm_contact_id, amocrm_lead_id, " .
                " date_first_message, date_last_message, customer_from, telegram_chat_id, telegram_username, " .
                " customer_chat_status, lead_created, lead_manager, lead_completed, customer_id, " .
                " is_vip, plan_id, currency_id) values " .
            "(" . $firstname . ", " . $lastname . ", " . $date_registered . ", " . $customer->amo_id . ", " . $customer->amo_lead_id . ", " .
                " " . $date_first_message . ", " . $date_last_message . ", 'telegram', " . $customer->telegram_chat_id . ", " . $customer->telegram_username . ", " .
                " '" . $customer->customer_chat_status . "', " . $lead_created .", " . $lead_manager .", " . $lead_completed .", " . $billingCustomerId . ", " .
                " '" . $is_vip . "', '" . $plan_id . "', " . $customer->currency_id . ");");

        }

*/
        return 'ok';
    }
}
