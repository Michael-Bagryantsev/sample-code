<?php

namespace App\Http\Controllers;

use AmoCRM\Exception;
use App\Models\{
    Flow, FlowsAction, FlowsAnswer, FlowsButton, FlowsCondition, FlowsAnswersMessage
};
use Illuminate\Http\Request;
use DB, Session, Redirect, stdClass;

class FlowsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $flows = DB::table('flows')
            ->selectRaw('flows.*, flows_conditions.condition_name')
            ->leftJoin('flows_conditions', 'flows_conditions.id', '=', 'flows.condition_id')->get();

        return view('flows/list', ['flows' => $flows]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $conditions = FlowsCondition::all();

        return view('flows/create', ['conditions' => $conditions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $flow = new Flow();
        $flow->condition_id = $request->input('condition_id');
        $flow->name = $request->input('name');
        $flow->percent = $request->input('percent');
        $flow->is_active = (int)($request->input('is_active') === 'on');
        $flow->save();

        Session::flash('message', 'Successfully created a flow');
        return Redirect::to('flows');
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
        $flow = Flow::find($id);

        if (is_null($flow)) {
            Session::flash('message', 'Flow #' . $id . ' not found');
            return Redirect::to('flows');
        }

        $conditions = FlowsCondition::all();

        return view('flows/edit', ['conditions' => $conditions, 'flow' => $flow]);
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
        $flow = Flow::find($id);
        $flow->condition_id = $request->input('condition_id');
        $flow->name = $request->input('name');
        $flow->percent = $request->input('percent');
        $flow->is_active = (int)($request->input('is_active') === 'on');
        $flow->save();

        Session::flash('message', 'Successfully updated a flow');
        return Redirect::to('flows');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $flow = Flow::find($id);
        $flow->delete();

        $answers = FlowsAnswer::where('flow_id', $id)->get();
        foreach  ($answers as $answer) {
            $messages = FlowsAnswersMessage::where('answer_id', $answer->id)->get();
            foreach ($messages as $message) {
                $message->delete();
            }
            $buttons = FlowsButton::where('prev_answer_id', $answer->id)->get();
            foreach ($buttons as $button) {
                $button->delete();
            }
            $answer->delete();
        }

        Session::flash('message', 'Successfully deleted flow');
        return Redirect::to('flows');
    }

    public function previewFlow(Request $request, int $id)
    {
        $flow = Flow::find($id);
        $flowAnswers = FlowsAnswer::where('flow_id', $id)->get();
        $flowButtons = FlowsButton::where('flow_id', $id)->get();

        $data = new stdClass();
        $data->operators = new stdClass();
        $i = 0;
        foreach ($flowAnswers as $answer) {
            if (!is_null($answer->pos_data)) {
                $answer->pos_data = json_decode($answer->pos_data);
            } else {
                $answer->pos_data = new stdClass();
            }
            $data->operators->{$answer->id} = new stdClass();
            if (!isset($answer->pos_data->{$id}) || !isset($answer->pos_data->{$id}->pos_y)) {
                $data->operators->{$answer->id}->top = floor(($i * 250) / 1250) * 250 + 20;
            } else {
                $data->operators->{$answer->id}->top = $answer->pos_data->{$id}->pos_y;
            }
            if (!isset($answer->pos_data->{$id}) || !isset($answer->pos_data->{$id}->pos_x)) {
                $data->operators->{$answer->id}->left = ($i * 250) % 1250 + 20;
            } else {
                $data->operators->{$answer->id}->left = $answer->pos_data->{$id}->pos_x;
            }
            $data->operators->{$answer->id}->properties = new stdClass();
            $data->operators->{$answer->id}->properties->title = $answer->answer_title;
            $data->operators->{$answer->id}->properties->inputs = new stdClass();
            $data->operators->{$answer->id}->properties->outputs = new stdClass();

            foreach ($flowButtons as $button) {
                if ($button->prev_answer_id === $answer->id) {
                    $data->operators->{$answer->id}->properties->outputs->{$button->id} = new stdClass();
                    $data->operators->{$answer->id}->properties->outputs->{$button->id}->label = (string)$button->button_text;
                }
            }

            $data->operators->{$answer->id}->properties->inputs->{'input'} = new stdClass();
            $data->operators->{$answer->id}->properties->inputs->{'input'}->label = '';

            $i++;
        }
        $data->links = new stdClass();
        foreach ($flowButtons as $button) {
            $internalButton = false;
            foreach ($flowAnswers as $answer) {
                if ($answer->id === $button->next_answer_id) {
                    $internalButton = true;
                }
            }
            if ($internalButton === true) {
                $data->links->{$button->id} = new stdClass();
                $data->links->{$button->id}->fromOperator = $button->prev_answer_id;
                $data->links->{$button->id}->fromConnector = $button->id;
                $data->links->{$button->id}->toOperator = $button->next_answer_id;
                $data->links->{$button->id}->toConnector = 'input';
            } else {
                $externalAnswer = FlowsAnswer::
                    selectRaw('flows_answers.*, flows.name')
                    ->join('flows', 'flows.id', '=', 'flows_answers.flow_id')
                    ->where('flows_answers.id', $button->next_answer_id)->first();
                if ($externalAnswer) {
                    $data->operators->{$externalAnswer->id} = new stdClass();
                    $data->operators->{$externalAnswer->id}->external = true;
                    $data->operators->{$externalAnswer->id}->properties = new stdClass();
                    $data->operators->{$externalAnswer->id}->properties->title = $externalAnswer->name . ': ' . $externalAnswer->answer_title;
                    $data->operators->{$externalAnswer->id}->properties->inputs = new stdClass();
                    $data->operators->{$externalAnswer->id}->properties->outputs = new stdClass();

                    $data->operators->{$externalAnswer->id}->properties->inputs->{'input'} = new stdClass();
                    $data->operators->{$externalAnswer->id}->properties->inputs->{'input'}->label = '';

                    if (!is_null($externalAnswer->pos_data)) {
                        $externalAnswer->pos_data = json_decode($externalAnswer->pos_data);
                        if (isset($externalAnswer->pos_data->{$id}) && isset($externalAnswer->pos_data->{$id}->pos_y)) {
                            $data->operators->{$externalAnswer->id}->top = $externalAnswer->pos_data->{$id}->pos_y;
                        }
                        if (isset($externalAnswer->pos_data->{$id}) && isset($externalAnswer->pos_data->{$id}->pos_x)) {
                            $data->operators->{$externalAnswer->id}->left = $externalAnswer->pos_data->{$id}->pos_x;
                        }
                    }

                    $data->links->{$button->id} = new stdClass();
                    $data->links->{$button->id}->fromOperator = $button->prev_answer_id;
                    $data->links->{$button->id}->fromConnector = $button->id;
                    $data->links->{$button->id}->toOperator = $button->next_answer_id;
                    $data->links->{$button->id}->toConnector = 'input';
                }
            }
        }

        $flows = Flow::all();
        $flowsAnswers = FlowsAnswer::all();
        $actions = FlowsAction::all();

        return view('flows/preview',
        [
            'flow' => $flow,
            'data' => $data,
            'flows' => $flows,
            'flowsAnswers' => $flowsAnswers,
            'actions' => $actions
        ]);
    }

    public function previewSavePosition(Request $request, int $id, int $answerId)
    {
        $answer = FlowsAnswer::find($answerId);
        if (!is_null($answer->pos_data)) {
            $pos_data = json_decode($answer->pos_data);
        } else {
            $pos_data = new stdClass();
        }
        if (!isset($pos_data->{$id})) {
            $pos_data->{$id} = new stdClass();
        }
        $pos_data->{$id}->pos_x = $request->input('pos_x');
        $pos_data->{$id}->pos_y = $request->input('pos_y');
        $answer->pos_data = json_encode($pos_data);
        $answer->save();

        return response()->json(['success' => 'success'], 200);
    }

    public function previewRemoveButtonTarget(Request $request, int $id, int $buttonId)
    {
        $button = FlowsButton::find($buttonId);
        $button->next_answer_id = null;
        $button->save();

        return response()->json(['success' => 'success'], 200);
    }

    public function previewAddButtonTarget(Request $request, int $id, int $buttonId, int $answerId)
    {
        $button = FlowsButton::find($buttonId);
        $button->next_answer_id = $answerId;
        $button->save();

        return response()->json(['success' => 'success'], 200);
    }

    public function previewAddAnswer(Request $request, int $id)
    {
        $answer =  new FlowsAnswer();
        $answer->answer_title = $request->input('answer_title');
        $answer->flow_id = $id;
        $answer->save();

        $response = new stdClass();
        $response->operatorId = $answer->id;
        $response->operatorData = new stdClass();
        $response->operatorData->top = 200;
        $response->operatorData->left = 200;
        $response->operatorData->properties = new stdClass();
        $response->operatorData->properties->title = $answer->answer_title;
        $response->operatorData->properties->inputs = new stdClass();
        $response->operatorData->properties->outputs = new stdClass();
        $response->operatorData->properties->inputs->{'input'} = new stdClass();
        $response->operatorData->properties->inputs->{'input'}->label = '';

        if (!is_null($request->input('button_text'))) {
            foreach ($request->input('button_text') as $key => $val) {
                $button = new FlowsButton();
                $button->flow_id = $id;
                $button->prev_answer_id = $answer->id;
                $button->next_answer_id = null;
                $button->button_text = $val;
                $button->button_order = $request->input('button_order')[$key];
                $button->save();

                $response->operatorData->properties->outputs->{$button->id} = new stdClass();
                $response->operatorData->properties->outputs->{$button->id}->label = $button->button_text;
            }
        }

        if (!is_null($request->input('message_text'))) {
            foreach ($request->input('message_text') as $key => $val) {
                $message = new FlowsAnswersMessage();
                $message->answer_id = $answer->id;
                $message->message_text = $val;
                $message->message_method = ($request->has('message_method')[$key] && !empty($request->input('message_method')[$key])) ? $request->input('message_method')[$key] : null;
                $message->message_order = $request->input('message_order')[$key];
                $message->save();
            }
        }

        return response()->json($response);
    }

    public function previewRemoveAnswer(Request $request, int $id, int $answerId)
    {
        $answer = FlowsAnswer::find($answerId);
        $answer->delete();

        $messages = FlowsAnswersMessage::where('answer_id', $answerId)->get();
        foreach ($messages as $message) {
            $message->delete();
        }

        $buttons = FlowsButton::where('prev_answer_id', $answerId)->get();
        foreach ($buttons as $button) {
            $button->delete();
        }

        return response()->json(['success' => 'success'], 200);
    }

    public function previewEditAnswerForm(Request $request, int $id, int $answerId)
    {
        $answer = FlowsAnswer::find($answerId);

        if ($answer->flow_id !== $id) {
            return 'Can not edit another flow answer.';
        }

        $buttons = FlowsButton::where('prev_answer_id', $answerId)->get();
        $messages = FlowsAnswersMessage::where('answer_id', $answerId)->get();
        $actions = FlowsAction::all();

        return view('flows/edit-answer-form',
            [
                'answer' => $answer,
                'buttons' => $buttons,
                'messages' => $messages,
                'actions' => $actions
            ]);
    }

    public function previewUpdateAnswer(Request $request, int $id)
    {
        $answer = FlowsAnswer::find($request->input('id'));
        $answer->answer_title = $request->input('answer_title');
        $answer->save();

        $response = new stdClass();
        $response->operatorId = $answer->id;
        $response->operatorData = new stdClass();
        if (!is_null($answer->pos_data)) {
            $pos_data = json_decode($answer->pos_data);
        } else {
            $pos_data = new stdClass();
        }
        if (!isset($pos_data->{$id}) || !isset($pos_data->{$id}->pos_y)) {
            $response->operatorData->top = 200;
        } else {
            $response->operatorData->top = $pos_data->{$id}->pos_y;
        }
        if (!isset($pos_data->{$id}) || !isset($pos_data->{$id}->pos_x)) {
            $response->operatorData->left = 200;
        } else {
            $response->operatorData->left = $pos_data->{$id}->pos_x;
        }
        $response->operatorData->properties = new stdClass();
        $response->operatorData->properties->title = $answer->answer_title;
        $response->operatorData->properties->inputs = new stdClass();
        $response->operatorData->properties->outputs = new stdClass();
        $response->operatorData->properties->inputs->{'input'} = new stdClass();
        $response->operatorData->properties->inputs->{'input'}->label = '';

        if (!is_null($request->input('button_text'))) {
            foreach ($request->input('button_text') as $key => $val) {
                $button = FlowsButton::firstOrNew(['id' => $key], ['prev_answer_id' => $answer->id]);
                $button->flow_id = $id;
                $button->prev_answer_id = $answer->id;
                $button->next_answer_id = null;
                $button->button_text = $val;
                $button->button_order = $request->input('button_order')[$key];
                $button->save();

                $response->operatorData->properties->outputs->{$button->id} = new stdClass();
                $response->operatorData->properties->outputs->{$button->id}->label = $button->button_text;
            }
        }

        if (!is_null($request->input('message_text'))) {
            foreach ($request->input('message_text') as $key => $val) {
                $message = FlowsAnswersMessage::firstOrNew(['id' => $key], ['answer_id' => $answer->id]);
                $message->answer_id = $answer->id;
                $message->message_text = $val;
                $message->message_method = ($request->has('message_method')[$key] && !empty($request->input('message_method')[$key])) ? $request->input('message_method')[$key] : null;
                $message->message_order = $request->input('message_order')[$key];
                $message->save();
            }
        }

        return response()->json($response);
    }

}
