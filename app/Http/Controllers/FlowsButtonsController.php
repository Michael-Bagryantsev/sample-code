<?php

namespace App\Http\Controllers;

use App\Models\{Flow, FlowsAnswer, FlowsAnswersMessage, FlowsButton};
use Illuminate\Http\Request;
use DB, Session, Redirect;

class FlowsButtonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $flowsButtons = FlowsButton
            ::selectRaw('flows_buttons.*, a1.answer_title as next_answer_title, a2.answer_title as prev_answer_title')
            ->leftJoin('flows_answers as a1', 'flows_buttons.next_answer_id', '=', 'a1.id')
            ->leftJoin('flows_answers as a2', 'flows_buttons.prev_answer_id', '=', 'a2.id')
            ->get();

        return view('flows/buttons-list', ['flowsButtons' => $flowsButtons]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $flows = Flow::all();
        $flowsAnswers = FlowsAnswer::all();

        return view('flows/buttons-create',
            [
                'flows' => $flows,
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
        $flowButton = new FlowsButton();
        $flowButton->flow_id = $request->input('flow_id');
        $flowButton->prev_answer_id = $request->input('prev_answer_id');
        $flowButton->next_answer_id = ($request->has('next_answer_id') && !empty($request->input('next_answer_id'))) ? $request->input('next_answer_id') : null;
        $flowButton->button_text = $request->input('button_text');
        $flowButton->button_order = $request->input('button_order');
        $flowButton->save();

        Session::flash('message', 'Successfully created a button');
        return Redirect::to('flows-buttons');
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
        $flowButton = FlowsButton::find($id);

        if (is_null($flowButton)) {
            Session::flash('message', 'Button #' . $id . ' not found');
            return Redirect::to('flows-buttons');
        }

        $flows = Flow::all();
        $flowsAnswers = FlowsAnswer::all();

        return view('flows/buttons-edit',
            [
                'flows' => $flows,
                'flowsAnswers' => $flowsAnswers,
                'flowButton' => $flowButton
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
        $flowButton = FlowsButton::find($id);
        $flowButton->flow_id = $request->input('flow_id');
        $flowButton->prev_answer_id = $request->input('prev_answer_id');
        $flowButton->next_answer_id = ($request->has('next_answer_id') && !empty($request->input('next_answer_id'))) ? $request->input('next_answer_id') : null;
        $flowButton->button_text = $request->input('button_text');
        $flowButton->button_order = $request->input('button_order');
        $flowButton->save();

        Session::flash('message', 'Successfully updated a button');
        return Redirect::to('flows-buttons');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $flowButton = FlowsButton::find($id);
        $flowButton->delete();

        Session::flash('message', 'Successfully deleted button');
        return Redirect::to('flows-buttons');
    }
}
