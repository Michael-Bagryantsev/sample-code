<?php

namespace App\Http\Controllers;

use App\Models\{Flow, FlowsAnswer};
use Illuminate\Http\Request;
use DB, Session, Redirect;

class FlowsAnswersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $flowsAnswers = DB::table('flows_answers')
            ->selectRaw('flows_answers.*, flows.name')
            ->leftJoin('flows', 'flows.id', '=', 'flows_answers.flow_id')
            ->get();

        return view('flows/answers-list', ['flowsAnswers' => $flowsAnswers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $flows = Flow::all();

        return view('flows/answers-create', ['flows' => $flows]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $flowAnswer = new FlowsAnswer();
        $flowAnswer->flow_id = $request->input('flow_id');
        $flowAnswer->answer_title = $request->input('answer_title');
        $flowAnswer->save();

        Session::flash('message', 'Successfully created an answer');
        return Redirect::to('flows-answers');
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
        $flowsAnswer = FlowsAnswer::find($id);

        if (is_null($flowsAnswer)) {
            Session::flash('message', 'Answer #' . $id . ' not found');
            return Redirect::to('flows-answers');
        }

        $flows = Flow::all();

        return view('flows/answers-edit',
            [
                'flows' => $flows,
                'flowsAnswer' => $flowsAnswer
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
        $flowAnswer = FlowsAnswer::find($id);
        $flowAnswer->flow_id = $request->input('flow_id');
        $flowAnswer->answer_title = $request->input('answer_title');
        $flowAnswer->save();

        Session::flash('message', 'Successfully updated an answer');
        return Redirect::to('flows-answers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $flowAnswer = FlowsAnswer::find($id);
        $flowAnswer->delete();

        Session::flash('message', 'Successfully deleted answer');
        return Redirect::to('flows-answers');
    }
}
