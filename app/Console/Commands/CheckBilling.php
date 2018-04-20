<?php

namespace App\Console\Commands;

use App\Models\{
    BillingCustomer, BillingOrder, BillingPlan, Order, SubscriptionPlan, CustomersChat, AnyLog, UsersFb
};
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use AmoCRM\Client as AmoClient;
use App\Helpers\{BillingHelper, AmoHelper};
use DB;

class CheckBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check orders with \'new\' status throw billing system';

    protected $expiredMessage = 'The address lifetime expired.';
    protected $successMessage = 'Your payment received!';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = DB::table('billing_orders')
            ->where('billing_orders.status', '<>', config('app.BILLING_STATUSES')['PAID'])
            ->where('billing_orders.status', '<>', config('app.BILLING_STATUSES')['EXPIRED'])
            ->join('users_fb', 'users_fb.customer_id', '=', 'billing_orders.customer_id')
            ->where('users_fb.customer_from', '=', "'telegram'")->get();

        if ($orders->count()) {
            $telegram = new Api(config('app.TELEGRAM_TOKEN'));

            foreach ($orders as $order) {
                $customer = DB::table('billing_customers')->where('customer_id', $order->customer_id)->first();
                $billingCustomer = BillingCustomer::find($order->customer_id);

                $body = [
                    'user_id' => $billingCustomer->billing_user_id,
                ];

                $result = BillingHelper::sendRequest(config('app.BILLING_BASE_URL') . '/order/index/get_orders?token=' . $billingCustomer->billing_token, http_build_query($body));
                $response = json_decode($result->response);

                $statusChanged = false;

                if (isset($response->data) && isset($response->data[0]) && isset($response->data[0]->data)) {
                    if ($response->data[0]->data->paid_sum >= $response->data[0]->data->sum) {
                        $thisOrder = BillingOrder::find($order->id);
                        $thisOrder->status = config('app.BILLING_STATUSES')['PAID'];
                        $thisOrder->save();

                        $plan = BillingPlan::find($order->plan_id);
                        if ($plan && $customer) {
                            $billingCustomer->date_paid_till = $plan->getProlongTime();
                            $billingCustomer->save();

                            $customer->is_vip = 1;
                            $customer->save();

                            $telegram->sendMessage(['chat_id' => $customer->telegram_chat_id, 'text' => $this->successMessage]);

                            $customersChat = new CustomersChat();
                            $customersChat->message_from = 'bot';
                            $customersChat->message_from_name = null;
                            $customersChat->message_time = time();
                            $customersChat->message_text = $this->successMessage;
                            $customersChat->customer_id = $customer->id;
                            $customersChat->save();

                            $statusChanged = true;

                            if ($customer->amocrm_lead_id) {
                                $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));
                                AmoHelper::updateLead($amo, ['id' => (int)$customer->amocrm_lead_id, 'status_id' => (int)config('app.AMO_STATUSES')[4]['key']]);
                            }

                            $scope_id = false;
                            $AmoAccountId = AmoHelper::getAccountId(config('app.AMO_CHAT_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));
                            if ($AmoAccountId !== false) {
                                $scope_id = AmoHelper::getScopeId($AmoAccountId, config('app.AMO_CHAT_CHANNEL_ID'), config('app.AMO_CHAT_SECRET'));
                            }
                            $body = json_encode([
                                'event_type' => 'new_message',
                                'payload' => [
                                    'timestamp' => time(),
                                    'msgid' => uniqid(),
                                    'conversation_id' => 'telegram' . $customer->telegram_chat_id,
                                    'sender' => [
                                        'id' => $customer->getAmoChatSenderId(),
                                        'name' => $customer->getName(),
                                    ],
                                    'message' => [
                                        'type' => 'text',
                                        'text' => '[BOT]: ' . $this->successMessage
                                    ]
                                ]
                            ]);
                            AmoHelper::sendRequest(config('app.AMOJO_BASE_URL') . $scope_id, $body, config('app.AMO_CHAT_SECRET'));
                        }
                    }
                }

                if ($statusChanged === false && (date('Y-m-d', time() + config('app.BILLING_WAIT_TIME')) > $order->date_expiration)) {
                    $thisOrder = BillingOrder::find($order->id);
                    $thisOrder->status = config('app.BILLING_STATUSES')['EXPIRED'];
                    $thisOrder->save();

                    if ($customer) {
                        $telegram->sendMessage(['chat_id' => $customer->telegram_chat_id, 'text' => $this->expiredMessage]);

                        $customersChat = new CustomersChat();
                        $customersChat->message_from = 'bot';
                        $customersChat->message_from_name = null;
                        $customersChat->message_time = time();
                        $customersChat->message_text = $this->expiredMessage;
                        $customersChat->customer_id = $customer->id;
                        $customersChat->save();

                        if ($customer->amo_lead_id) {
                            $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));
                            AmoHelper::updateLead($amo,
                                [
                                    'id' => (int)$customer->amocrm_lead_id,
                                    'status_id' => (int)config('app.AMO_STATUSES')[6]['key'],
                                    'tags' => ['telegram', 'НеОплатил']
                                ]);
                        }

                        $scope_id = false;
                        $AmoAccountId = AmoHelper::getAccountId(config('app.AMO_CHAT_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));
                        if ($AmoAccountId !== false) {
                            $scope_id = AmoHelper::getScopeId($AmoAccountId, config('app.AMO_CHAT_CHANNEL_ID'), config('app.AMO_CHAT_SECRET'));
                        }
                        $body = json_encode([
                            'event_type' => 'new_message',
                            'payload' => [
                                'timestamp' => time(),
                                'msgid' => uniqid(),
                                'conversation_id' => 'telegram' . $customer->telegram_chat_id,
                                'sender' => [
                                    'id' => $customer->getAmoChatSenderId(),
                                    'name' => $customer->getName(),
                                ],
                                'message' => [
                                    'type' => 'text',
                                    'text' => '[BOT]: ' . $this->expiredMessage
                                ]
                            ]
                        ]);
                        AmoHelper::sendRequest(config('app.AMOJO_BASE_URL') . $scope_id, $body, config('app.AMO_CHAT_SECRET'));
                    }
                }
            }
        }
    }
}
