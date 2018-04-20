<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 01/04/2018
 * Time: 08:08
 */

namespace App\Helpers;

use Telegram\Bot\Api;
use App\Models\{CustomersChat, UsersFb};

class TelegramHelper
{

    public static function sendMessage(array $params)
    {
        try {
            $customersChat = new CustomersChat();
            $customersChat->message_from = isset($params['from']) ? $params['from'] : null;
            $customersChat->message_from_name = isset($params['from_name']) ? $params['from_name'] : null;
            $customersChat->message_time = isset($params['time']) ? $params['time'] : time();

            switch ($params['type']) {
                case 'text':
                    if (isset($params['reply_markup'])) {
                        $params['telegram']->sendMessage(['chat_id' => $params['chat_id'], 'text' => $params['text'], 'reply_markup' => $params['reply_markup']]);
                    } else {
                        $params['telegram']->sendMessage(['chat_id' => $params['chat_id'], 'text' => $params['text']]);
                    }
                    $customersChat->message_text = $params['text'];
                    break;
                case 'picture':
                    $params['telegram']->setAsyncRequest(true)->sendPhoto(['chat_id' => $params['chat_id'], 'photo' => $params['photo']]);
                    $customersChat->message_text = $params['photo'];
                    break;
                default:
                    break;
            }

            $customer = UsersFb::where('telegram_chat_id', $params['chat_id'])->first();
            if ($customer) {
                $customersChat->customer_id = $customer->id;
            }
            $customersChat->save();
        } catch (\Exception $e) {
            if ($e->getCode() === 403 || $e->getCode() === 400) {
                $customer = UsersFb::where('telegram_chat_id', $params['chat_id'])->first();
                if (!is_null($customer)) {
                    $customer->is_blocked = 1;
                    $customer->save();
                }
            }
        }
    }
}