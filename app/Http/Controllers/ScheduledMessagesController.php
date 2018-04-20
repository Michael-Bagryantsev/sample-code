<?php

namespace App\Http\Controllers;

use App\Helpers\MessagesHelper;
use App\Models\{CustomersGroup, ScheduledMessage, ScheduledMessagesCondition};
use Illuminate\Http\Request;
use Session, Redirect, DB;
use Telegram\Bot\Api;

class ScheduledMessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $scheduledMessages = DB::table('scheduled_messages')
            ->selectRaw('scheduled_messages.*, scheduled_messages_conditions.condition_name, customers_groups.group_name')
            ->leftJoin('scheduled_messages_conditions', 'scheduled_messages_conditions.id', '=', 'scheduled_messages.condition_id')
            ->leftJoin('customers_groups', 'customers_groups.id', '=', 'scheduled_messages.customers_group_id')
            ->get();

        return view('scheduled-messages/index', ['scheduledMessages' => $scheduledMessages]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customersGroups = CustomersGroup::all();
        $conditions = ScheduledMessagesCondition::all();

        return view('scheduled-messages/create', ['customersGroups' => $customersGroups, 'conditions' => $conditions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $scheduledMessage = new ScheduledMessage();
        $scheduledMessage->customers_group_id = $request->input('customers_group_id');
        $scheduledMessage->condition_id = $request->input('condition_id');
        $scheduledMessage->message_name = $request->input('message_name');
        $scheduledMessage->message_text = $request->input('message_text');
        $scheduledMessage->is_active = (int)($request->input('is_active') === 'on');
        $scheduledMessage->is_public_channel = (int)($request->input('is_public_channel') === 'on');
        $scheduledMessage->message_data = $request->has('condition') ? json_encode($request->input('condition')) : json_encode([]);
        $scheduledMessage->save();

        Session::flash('message', 'Successfully created a message');
        return Redirect::to('scheduled-messages');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $scheduledMessage = ScheduledMessage::find($id);

        if (is_null($scheduledMessage)) {
            Session::flash('message', 'Message not #' . $id . ' found');
            return Redirect::to('scheduled-messages');
        }

        $customersGroups = CustomersGroup::all();
        $conditions = ScheduledMessagesCondition::all();

        return view('scheduled-messages/edit', ['customersGroups' => $customersGroups, 'conditions' => $conditions, 'scheduledMessage' => $scheduledMessage]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $scheduledMessage = ScheduledMessage::find($id);
        $scheduledMessage->customers_group_id = $request->input('customers_group_id');
        $scheduledMessage->condition_id = $request->input('condition_id');
        $scheduledMessage->message_name = $request->input('message_name');
        $scheduledMessage->message_text = $request->input('message_text');
        $scheduledMessage->is_active = (int)($request->input('is_active') === 'on');
        $scheduledMessage->is_public_channel = (int)($request->input('is_public_channel') === 'on');
        $scheduledMessage->message_data = $request->has('condition') ? json_encode($request->input('condition')) : json_encode([]);
        $scheduledMessage->save();

        Session::flash('message', 'Successfully updated a message');
        return Redirect::to('scheduled-messages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $scheduledMessage = ScheduledMessage::find($id);
        $scheduledMessage->delete();

        Session::flash('message', 'Successfully deleted message');
        return Redirect::to('scheduled-messages');
    }

    public function getConditionEditHtml(Request $request, int $id = 0)
    {
        $coditionInfo = ScheduledMessagesCondition::find($id);

        if ($coditionInfo) {
            $class = '\App\Cryptomaker\Messages\\' . $coditionInfo->condition_class;
            $condition = new $class();
            return $condition->getConditionHtml();
        }

        return '';
    }

    public function preview(Request $request, int $id = 0)
    {
        try {
            $scheduledMessage = ScheduledMessage::find($id);

            $text = MessagesHelper::formatMessageText($scheduledMessage->message_text);

            $telegram = new Api(config('app.telegram_preview_token'));
            $telegram->sendMessage(['chat_id' => config('app.telegram_preview_chat_id'), 'text' => $text, 'parse_mode' => 'HTML']);

            return 'ok';
        } catch (\Exception $e) {
            $telegram = new Api(config('logging.telegram_token'));
            $telegram->sendMessage(['chat_id' => config('logging.telegram_chat_id'),
                'text' => 'Send preview message error: ' . $e->getCode() . ' ' . $e->getMessage()]);

            return 'sadface.jpg';
        }
        return 'uglyface.jpg';
    }
}
