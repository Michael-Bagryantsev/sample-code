@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Edit Message
        <a href="/scheduled-messages" class="btn btn-default pull-right">Back</a></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <form id="frm-edit-message" method="POST" action="/scheduled-messages/<?php echo $scheduledMessage->id; ?>">
                    {!! method_field('patch') !!}
                    {{ csrf_field() }}
                    <div class="box-body pad">
                        <div class="form-group">
                            <select name="customers_group_id" class="form-control" required>
                                <option value="">Customers group</option>
                                <?php
                                foreach ($customersGroups as $customersGroup) {
                                ?>
                                <option value="<?php echo $customersGroup->id; ?>" <?php
                                    echo ($scheduledMessage->customers_group_id === $customersGroup->id ? ' selected ' : '');
                                    ?>><?php echo $customersGroup->group_name; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_public_channel" <?php
                                    echo ((int)$scheduledMessage->is_public_channel === 1 ? ' checked ' : '');
                                    ?>/> Send to public channel
                            </label>
                        </div>
                        <div class="form-group">
                            <select name="condition_id" class="form-control" required>
                                <option value="">Condition</option>
                                <?php
                                foreach ($conditions as $condition) {
                                ?>
                                <option value="<?php echo $condition->id; ?>" <?php
                                    echo ($scheduledMessage->condition_id === $condition->id ? ' selected ' : '');
                                    ?>><?php echo $condition->condition_name; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div id="condition-details"></div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="message_name" placeholder="Message Title(for you only)" value="<?php echo $scheduledMessage->message_name; ?>" required />
                        </div>
                        <div class="form-group">
                            <textarea id="message_text" name="message_text" placeholder="Place some message here" required><?php echo $scheduledMessage->message_text; ?></textarea>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" <?php
                                    echo ((int)$scheduledMessage->is_active === 1 ? ' checked ' : '');
                                    ?>/> Active
                            </label>
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

@push('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endpush

@push('js')
    <script src="{{ Asset::get('vendor/adminlte/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.27/moment.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
        $(function() {
            CKEDITOR.replace('message_text');

            $('#frm-edit-message select[name="condition_id"]').on('change', function() {
                loadConditionHtml($(this).val());
            });
            loadConditionHtml($('#frm-edit-message select[name="condition_id"] option:selected').val());
        });

        var message_data = JSON.parse('<?php echo $scheduledMessage->message_data; ?>');

        function loadConditionHtml(conditionId) {
            $.get('/scheduled-messages/get-condition-edit-html/' + conditionId, function(response){
                $('#condition-details').html(response);

                $('#condition-details input, #condition-details select').each(function(i, obj) {
                    $.each(message_data, function (key, data) {
                        if ($(obj).attr('name') === 'condition[' + key + ']') {
                            $(obj).val(data);
                        }
                    })
                });

                $('#condition-details .datepicker').each(function(i, obj) {
                    $(obj).datetimepicker();
                });
            });
        }
    </script>
@endpush
