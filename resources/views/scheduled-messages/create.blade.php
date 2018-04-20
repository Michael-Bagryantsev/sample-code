@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Add Message
        <a href="/scheduled-messages" class="btn btn-default pull-right">Back</a></h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <form id="frm-edit-message" method="POST" action="/scheduled-messages">
                {{ csrf_field() }}
                <div class="box-body pad">
                    <div class="form-group">
                        <select name="customers_group_id" class="form-control" required>
                            <option value="">Customers group</option>
                            <?php
                            foreach ($customersGroups as $customersGroup) {
                                ?>
                                <option value="<?php echo $customersGroup->id; ?>"><?php echo $customersGroup->group_name; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="is_public_channel" checked /> Send to public channel
                        </label>
                    </div>
                    <div class="form-group">
                        <select name="condition_id" class="form-control" required>
                            <option value="">Condition</option>
                            <?php
                            foreach ($conditions as $condition) {
                            ?>
                            <option value="<?php echo $condition->id; ?>"><?php echo $condition->condition_name; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div id="condition-details"></div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="message_name" placeholder="Message Title(for you only)" required />
                    </div>
                    <div class="form-group">
                        <textarea id="message_text" name="message_text" placeholder="Place some message here" required></textarea>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="is_active" /> Active
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
                $.get('/scheduled-messages/get-condition-edit-html/' + $(this).val(), function(response){
                    $('#condition-details').html(response);
                    $('#condition-details .datepicker').each(function(i, obj) {
                        $(obj).datetimepicker();
                    });
                });
            })
        });
    </script>
@endpush
