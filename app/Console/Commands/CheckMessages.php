<?php

namespace App\Console\Commands;

use App\Helpers\MessagesHelper;
use App\Models\{UsersFb, ScheduledMessage};
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use DB;

class CheckMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check scheduled messages for required conditions';

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
        $messages = DB::table('scheduled_messages')
            ->selectRaw('scheduled_messages.*, scheduled_messages_conditions.condition_class, customers_groups.return_method')
            ->leftJoin('scheduled_messages_conditions', 'scheduled_messages_conditions.id', '=', 'scheduled_messages.condition_id')
            ->leftJoin('customers_groups', 'customers_groups.id', '=', 'scheduled_messages.customers_group_id')
            ->where('scheduled_messages.is_active', 1)
            ->get();

        $telegram = new Api(config('app.TELEGRAM_TOKEN'));
        //$telegram = new Api(config('app.telegram_preview_token')); 
        foreach ($messages as $message) {
            $class = '\App\Cryptomaker\Messages\\' . $message->condition_class;
            $condition = new $class();
            $checkCondition = $condition->checkCondition(json_decode($message->message_data));
            if ($checkCondition === true) {
                $text = MessagesHelper::formatMessageText($message->message_text);

                $customers = UsersFb::{$message->return_method}();
                foreach ($customers as $customer) {
                    if (!is_null($customer->telegram_chat_id)) {
                        try {
                            $telegram->sendMessage(['chat_id' => $customer->telegram_chat_id, 'text' => $text, 'parse_mode' => 'HTML']);
                        } catch (\Exception $e) {
                            if ($e->getCode() === 403 || $e->getCode() === 400) {
                                $thisCustomer = UsersFb::where('telegram_chat_id', $customer->telegram_chat_id)->first();
                                $thisCustomer->is_blocked = 1;
                                $thisCustomer->save();
                            }
                        }
                        //$telegram->sendMessage(['chat_id' => config('app.telegram_preview_chat_id'), 'text' => $text, 'parse_mode' => 'HTML']);
                    }
                }

                if ($message->is_public_channel === 1) {
                    $telegram->sendMessage(['chat_id' => config('app.telegram_public_chat_id'), 'text' => $text, 'parse_mode' => 'HTML']);
                }

                $thisMessage = ScheduledMessage::find($message->id);
                $thisMessage->is_active = 0;
                $thisMessage->save();
            }
        }
    }
}
