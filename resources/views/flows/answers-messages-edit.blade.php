@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Edit Message
        <a href="/flows-answers-messages" class="btn btn-default pull-right">Back</a></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <form id="frm-edit-message" method="POST" action="/flows-answers-messages/<?php echo $flowAnswersMessage->id; ?>">
                    {!! method_field('patch') !!}
                    {{ csrf_field() }}
                    <div class="box-body pad">
                        <div class="form-group">
                            <select name="answer_id" class="form-control" required>
                                <option value="">Answer</option>
                                <?php
                                foreach ($flowsAnswers as $item) {
                                ?>
                                <option value="<?php echo $item->id; ?>" <?php
                                    echo ($flowAnswersMessage->answer_id === $item->id ? ' selected ' : '');
                                    ?>><?php echo $item->answer_title; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea id="message_text" name="message_text" placeholder="Place some message here" required><?php echo $flowAnswersMessage->message_text; ?></textarea>
                        </div>
                        <div class="form-group">
                            <input type="text" value="<?php echo $flowAnswersMessage->message_method; ?>" class="form-control" name="message_method" placeholder="A function to generate text(usually leave it blank)" />
                        </div>
                        <div class="form-group">
                            <input type="text" value="<?php echo $flowAnswersMessage->message_order; ?>" class="form-control" name="message_order" placeholder="Message order (if an answer has several messages)" />
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script src="{{ Asset::get('vendor/adminlte/plugins/ckeditor/ckeditor.js') }}"></script>
    <script>
        $(function() {
            CKEDITOR.replace('message_text');
        });
    </script>
@endpush

