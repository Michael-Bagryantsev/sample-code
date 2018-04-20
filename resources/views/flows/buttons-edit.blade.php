@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Edit Button
        <a href="/flows-buttons" class="btn btn-default pull-right">Back</a></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <form method="POST" action="/flows-buttons/<?php echo $flowButton->id; ?>">
                    {!! method_field('patch') !!}
                    {{ csrf_field() }}
                    <div class="box-body pad">
                        <div class="form-group">
                            <select id="flow_id" name="flow_id" class="form-control" required>
                                <option value="">Flow</option>
                                <?php
                                foreach ($flows as $item) {
                                ?>
                                <option value="<?php echo $item->id; ?>" <?php
                                    echo ($flowButton->flow_id === $item->id ? ' selected ' : '');
                                    ?>><?php echo $item->name; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="prev_answer_id" name="prev_answer_id" class="form-control" required>
                                <option value="" data-flow-id="">Prev Answer</option>
                                <?php
                                foreach ($flowsAnswers as $item) {
                                ?>
                                <option value="<?php echo $item->id; ?>" data-flow-id="<?php echo $item->flow_id; ?>" <?php
                                    echo ($flowButton->prev_answer_id === $item->id ? ' selected ' : '');
                                    ?>><?php echo $item->answer_title; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="next_answer_id" class="form-control">
                                <option value="">Next Answer</option>
                                <?php
                                foreach ($flowsAnswers as $item) {
                                ?>
                                <option value="<?php echo $item->id; ?>" <?php
                                    echo ($flowButton->next_answer_id === $item->id ? ' selected ' : '');
                                    ?>><?php echo $item->answer_title; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" value="<?php echo $flowButton->button_text; ?>" class="form-control" name="button_text" placeholder="Button caption" required />
                        </div>
                        <div class="form-group">
                            <input type="text" value="<?php echo $flowButton->button_order; ?>" class="form-control" name="button_order" placeholder="Button order (if a message has several buttons)" />
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
    <script>
        $(function() {
            updateMessagesView($('#flow_id option:selected').val());

            $('#flow_id').on('change', function() {
                updateMessagesView($(this).val());
            });
        });

        function updateMessagesView(flowId) {
            $('#prev_answer_id option').each(function(i, obj) {
                if ($(obj).data('flowId') == flowId || $(obj).data('flowId') === '') {
                    $(obj).show();
                } else {
                    $(obj).hide();
                }
            });
        }
    </script>
@endpush

