<?php

namespace App\Http\Controllers;

use App\Models\{FlowsAnswersMessage, FlowsAnswer};
use Illuminate\Http\Request;
use DB, Session, Redirect;

class FlowsAnswersMessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $flowsAnswersMessages = FlowsAnswersMessage
            ::selectRaw('flows_answers_messages.*, flows_answers.answer_title, flows.name')
            ->leftJoin('flows_answers', 'flows_answers_messages.answer_id', '=', 'flows_answers.id')
            ->leftJoin('flows', 'flows.id', '=', 'flows_answers.flow_id')
            ->get();

        return view('flows/answers-messages-list', ['flowsAnswersMessages' => $flowsAnswersMessages]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $flowsAnswers = FlowsAnswer::all();

        return view('flows/answers-messages-create',
            [
                'flowsAnswers' => $flowsAnswers
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $flowAnswersMessage = new FlowsAnswersMessage();
        $flowAnswersMessage->answer_id = $request->input('answer_id');
        $flowAnswersMessage->message_text = $request->input('message_text');
        $flowAnswersMessage->message_method = ($request->has('message_method') && !empty($request->input('message_method'))) ? $request->input('message_method') : null;
        $flowAnswersMessage->message_order = $request->input('message_order');
        $flowAnswersMessage->save();

        Session::flash('message', 'Successfully created a message');
        return Redirect::to('flows-answers-messages');
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
        $flowAnswersMessage = FlowsAnswersMessage::find($id);

        if (is_null($flowAnswersMessage)) {
            Session::flash('message', 'Message #' . $id . ' not found');
            return Redirect::to('flows-answers-messages');
        }

        $flowsAnswers = FlowsAnswer::all();

        return view('flows/answers-messages-edit',
            [
                'flowsAnswers' => $flowsAnswers,
                'flowAnswersMessage' => $flowAnswersMessage
            ]);
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
        $flowAnswersMessage = FlowsAnswersMessage::find($id);
        $flowAnswersMessage->answer_id = $request->input('answer_id');
        $flowAnswersMessage->message_text = $request->input('message_text');
        $flowAnswersMessage->message_method = ($request->has('message_method') && !empty($request->input('message_method'))) ? $request->input('message_method') : null;
        $flowAnswersMessage->message_order = $request->input('message_order');
        $flowAnswersMessage->save();

        Session::flash('message', 'Successfully updated a message');
        return Redirect::to('flows-answers-messages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $flowAnswersMessage = FlowsAnswersMessage::find($id);
        $flowAnswersMessage->delete();

        Session::flash('message', 'Successfully deleted message');
        return Redirect::to('flows-answers-messages');
    }
}
