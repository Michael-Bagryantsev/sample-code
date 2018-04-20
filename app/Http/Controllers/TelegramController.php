<?php

namespace App\Http\Controllers;

use App\Models\{
    BotAnswersButton, BotAnswersMessage, Customer, CustomersChat, AccessLog, FlowsAnswer, FlowsAnswersMessage, FlowsButton, RequestsLog, AnyLog, UsersFb
};
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use App\Helpers\{AmoHelper, BotHelper, AnalyticsHelper, TelegramHelper, MessagesHelper};
use stdClass;
use DB;
use AmoCRM\Client as AmoClient;
use App\Cryptomaker\Flows\DefaultFlow;

class TelegramController extends Controller
{
    public function receive(Request $request)
    {
        AccessLog::logRequest('telegram');

        fastcgi_finish_request();

        $telegram = new Api(config('app.TELEGRAM_TOKEN'));
        $result = $telegram->getWebhookUpdates();
        $message = $result->getMessage();

        if (!is_null($message)) {

            $chatId = $message->getChat()->getId();
            $telegramUserId = $message->getFrom()->getId();
            $firstName = $message->getFrom()->getFirstName();
            $lastName = $message->getFrom()->getLastName();
            $messageText = $message->getText();

            $leadUpdateId = 0;
            $senderId = 'U2-' . $chatId;

            $userName = $message->getFrom()->getUsername();
            if (empty($userName)) {
                $userName = 'noname' . $chatId;
            }

            $analyticsInfo = null;
            if (!in_array($userName, config('app.TELEGRAM_DEBUG_ACCOUNTS'))) {
                if (strlen($messageText) >= 6 && substr($messageText, 0, 6) === '/start') {
                    $analyticsInfo = AnalyticsHelper::logStartRequest(['client_id' => $chatId, 'page' => $messageText]);
                } else {
                    AnalyticsHelper::logRequest(['client_id' => $chatId, 'page' => $messageText]);
                }
            }

            $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));

            $contactName = $firstName . ' ' . $lastName . ' (telegram@' . $userName . ')';
            $amoContact = AmoHelper::getAmoContactByTelegramUsername($amo, $userName);
            if ($amoContact !== false) {
                $contactName = $firstName . " " . $lastName;
            }

            $customer = UsersFb::where('telegram_chat_id', $chatId)->first();
            if (is_null($customer)) {
                $customer = new UsersFb();
                $customer->customer_from = 'telegram';
                $customer->telegram_chat_id = $chatId;
                $customer->first_name = $firstName;
                $customer->last_name = $lastName;
                $customer->telegram_username = $userName;
                $customer->date_registered = date('Y-m-d H:i:s');
                $customer->date_first_message = date('Y-m-d H:i:s');
                if (!is_null($analyticsInfo)) {
                    if (isset($analyticsInfo->source)) {
                        $customer->utm_source = $analyticsInfo->source;
                    }
                    if (isset($analyticsInfo->ref)) {
                        $customer->utm_channel = $analyticsInfo->ref;
                    }
                    if (isset($analyticsInfo->ohid)) {
                        $customer->ohid = $analyticsInfo->ohid;
                    }
                    if (isset($analyticsInfo->plan)) {
                        $customer->utm_plan = $analyticsInfo->plan;
                    }
                }

                if ($amoContact !== false) {
                    $customer->amocrm_lead_id = end($amoContact['linked_leads_id']);
                    $customer->amocrm_contact_id = $amoContact['id'];
                }
            }

            if (empty($customer->sequence_id)) {
                $flow = DefaultFlow::getFlow();
                $customer->sequence_id = $flow->id;
            }

            $customer->date_last_message = date('Y-m-d H:i:s');
            $customer->save();


            $message = new CustomersChat();
            $message->customer_id = $customer->id;
            $message->message_from = 'customer';
            $message->message_from_name = $firstName . ' ' . $lastName;
            $message->message_text = $messageText;
            $message->message_time = time();
            $message->save();

            $scope_id = false;
            $AmoAccountId = AmoHelper::getAccountId(config('app.AMO_CHAT_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));
            if ($AmoAccountId !== false) {
                $scope_id = AmoHelper::getScopeId($AmoAccountId, config('app.AMO_CHAT_CHANNEL_ID'), config('app.AMO_CHAT_SECRET'));
            }

            switch (substr($messageText, 0, 6)) {
                case '/start':
                    $answerInfo = FlowsAnswer::where('flow_id', $customer->sequence_id)->orderBy('id', 'asc')->first();
                    $answerId = $answerInfo->id;
                    break;
                default:
                    $buttonInfo = FlowsButton::where([['button_text', $messageText],['flow_id', $customer->sequence_id]])->first();
                    $answerId = 0;
                    if (!is_null($buttonInfo)) {
                        $answerId = $buttonInfo->next_answer_id;
                    }
                    break;
            }

            //new contact
            if ($amoContact === false) {

                $body = json_encode([
                    'event_type' => 'new_message',
                    'payload' => [
                        'timestamp' => time(),
                        'msgid' => uniqid(),
                        'conversation_id' => 'telegram' . $chatId,
                        'sender' => [
                            'id' => $senderId,
                            'name' => $contactName,
                        ],
                        'message' => [
                            'type' => 'text',
                            'text' => "Новый клиент в телеграмме: " . $firstName . " " . $lastName . " (@" . $userName . ")."
                        ]
                    ]
                ]);
                AmoHelper::sendRequest(config('app.AMOJO_BASE_URL') . $scope_id, $body, config('app.AMO_CHAT_SECRET'));

                sleep(2); //иначе клиент и сделка не успевают создаться в ЦРМ

                //update created contact and lead
                $result = AmoHelper::getAmoContactsByQuery($amo, 'telegram@' . $userName);

                if (!empty($result) && isset($result[0]) && isset($result[0]['linked_leads_id'])) {
                    $leadUpdateId = end($result[0]['linked_leads_id']);

                    $tmpCustomer = UsersFb::where('amocrm_contact_id', $result[0]['id'])->first();
                    if ($tmpCustomer) {
                        $tmpCustomer->amocrm_contact_id = null;
                        $tmpCustomer->amocrm_lead_id = null;
                        $tmpCustomer->save();
                    }

                    $customer->amocrm_lead_id = $leadUpdateId;
                    $customer->amocrm_contact_id = $result[0]['id'];
                    $customer->save();

                    $contact = $amo->contact;
                    $contact['name'] = $firstName . ' ' . $lastName;
                    $contact['tags'] = ['telegram'];
                    $contact->addCustomField(config('app.AMO_CUSTOM_TELEGRAM_CHAT_ID'), $chatId);
                    $contact->addCustomField(config('app.AMO_CUSTOM_TELEGRAM_USERNAME_ID'), $userName);
                    $result = $contact->apiUpdate((int)$customer->amocrm_contact_id, 'now');

                    AnyLog::insert(['log' => 'Amo update contact: ' . $userName . ' Response: ' . json_encode($result), 'date_added' => time()]);
                }
            }
            //existing contact
            $body = json_encode([
                'event_type' => 'new_message',
                'payload' => [
                    'timestamp' => time(),
                    'msgid' => uniqid(),
                    'conversation_id' => 'telegram' . $chatId,
                    'sender' => [
                        'id' => $senderId,
                        'name' => $firstName . ' ' . $lastName,
                    ],
                    'message' => [
                        'type' => 'text',
                        'text' => $messageText
                    ]
                ]
            ]);
            AmoHelper::sendRequest( config('app.AMOJO_BASE_URL') . $scope_id, $body, config('app.AMO_CHAT_SECRET'));

            $reply = FlowsAnswersMessage::where('answer_id', $answerId)->get();
            if ($reply->count()) { //bot has an answer
                if (!isset($customer->customer_chat_status) || empty($customer->customer_chat_status) || $customer->customer_chat_status === 'chat_bot') {

                    $buttons = FlowsButton::where('prev_answer_id', $answerId)->get();

                    if ($buttons->count()) {
                        $buttonsList = [];
                        foreach ($buttons as $button) {
                            $buttonsList[] = $button->button_text;
                        }

                        $replyMarkup = $telegram->replyKeyboardMarkup([
                            'keyboard' => [$buttonsList],
                            'resize_keyboard' => true,
                            'one_time_keyboard' => true
                        ]);
                    } else {
                        $replyMarkup = false;
                    }

                    foreach ($reply as $replyMessage) {
                        sleep(1);
                        //if reply message is generated
                        if (!is_null($replyMessage->message_method)) {
                            $params = new stdClass();
                            $params->predefinedMessage = $replyMessage->message_text;
                            $params->customer_id = $customer->id;
                            $params->userName = $userName;
                            $params->firstName = $firstName;
                            $params->lastName = $lastName;
                            $params->telegramUserId = $telegramUserId;

                            $buttonInfo = FlowsButton::where('button_text', $messageText)->first();
                            if ($buttonInfo) {
                                $params->button_id = $buttonInfo->id;
                            } else {
                                $params->button_id = 1;
                            }

                            $params->amo = $amo;
                            $params->amo_lead_id = isset($customer->amocrm_lead_id) ? (int)$customer->amocrm_lead_id : 0;

                            $replyMessage->message_text = BotHelper::{$replyMessage->message_method}($params);
                        }

                        //send reply to amo
                        $body = json_encode([
                            'event_type' => 'new_message',
                            'payload' => [
                                'timestamp' => time(),
                                'msgid' => uniqid(),
                                'conversation_id' => 'telegram' . $chatId,
                                'sender' => [
                                    'id' => $senderId,
                                    'name' => $contactName,
                                ],
                                'message' => [
                                    'type' => 'text',
                                    'text' => '[BOT]: ' . $replyMessage->message_text
                                ]
                            ]
                        ]);
                        AmoHelper::sendRequest(config('app.AMOJO_BASE_URL') . $scope_id, $body, config('app.AMO_CHAT_SECRET'));

                        //send reply to telegram
                        $params = [
                            'telegram' => $telegram,
                            'from' => 'bot',
                            'chat_id' => $chatId,
                            'type' => 'text',
                            'text' => MessagesHelper::formatMessageText($replyMessage->message_text)
                        ];
                        if ($replyMarkup !== false) {
                            $params['reply_markup'] = $replyMarkup;
                        }
                        TelegramHelper::sendMessage($params);
                    }
                }
            } else { //bot don't know what to answer
                $i = 0;
                $leadList = [];
                while (empty($leadList) && $i < 50) {
                    $leadList = $amo->lead->apiList([
                        'query' => $userName,
                    ]);
                    $i++;
                    sleep(1);
                }
                if (!empty($leadList)) {
                    foreach ($leadList as $existingLead) {
                        if ((int)$existingLead['status_id'] === (int)config('app.AMO_STATUSES')[1]['key']) {
                            $leadUpdated = AmoHelper::updateLead($amo,
                                    ['id' => (int)$existingLead['id'],
                                    'status_id' => (int)config('app.AMO_STATUSES')[6]['key'],
                                    'tags' => array_merge(AmoHelper::parseTagsArray($existingLead['tags']), ['НеПрошелFlow'])
                                    ]);
                            if ($leadUpdated !== false) {
                                $leadUpdateId = 0;
                            }
                        }
                    }
                }
            }

            if ((int)$leadUpdateId <> 0) {
                AmoHelper::updateLead($amo, ['id' => (int)$leadUpdateId, 'status_id' => (int)config('app.AMO_STATUSES')[1]['key'], 'tags' => ['telegram']]);
            }
        }

        return response()->json(['success' => 'success'], 200);
    }
}
