<?php

namespace App\Logging;

use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Log;
use Telegram\Bot\Api;

class TelegramHandler extends AbstractProcessingHandler
{
    private $webHookUrl;
    private $client;

    public function __construct($webHookUrl, $level = Logger::DEBUG, $bubble = true, $client = null)
    {
        parent::__construct($level, $bubble);

        $this->webHookUrl = $webHookUrl;
        $this->client     = ($client) ?: new Client();
    }

    public function write(array $record)
    {
        $telegram = new Api(config('logging.telegram_token'));
        $telegram->sendMessage(['chat_id' => config('logging.telegram_chat_id'), 'text' => 'Laravel: code #' . $record['level'] . ' message: ' . $record['message']]);
        /*
         * $channelId = -1001377421179;
        https://t.me/joinchat/AAAAAFIZx3s5Z9i8PCYv0g
        */
    }
}
