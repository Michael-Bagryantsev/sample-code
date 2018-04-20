@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Preview Flow "<?php echo $flow->name; ?>"
        <a href="/flows" class="btn btn-default pull-right">Back</a></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header flow-controls btn-toolbar">
                    <!--
                    <input type="button" class="btn btn-default btn-add-answer pull-left" value="Add answer" />
                    <input type="button" class="btn btn-default btn-add-dif-flow-answer pull-left" value="Import different flow answer" />
                    <input type="button" class="btn btn-danger btn-selected-link btn-delete-link pull-right" value="Remove selected link(answer button)" disabled />
                    <input type="button" class="btn btn-danger btn-selected-answer btn-delete-answer pull-right" value="Remove selected answer" disabled />
                    <input type="button" class="btn btn-default btn-selected-answer btn-edit-answer pull-right" value="Edit selected answer" disabled />
                    <div class="clear"></div>
                    <input type="button" class="btn btn-default btn-add-action pull-left" value="Add action" />
                    <input type="button" class="btn btn-danger btn-selected-action btn-delete-action pull-right" value="Remove selected action" disabled />
                    <input type="button" class="btn btn-default btn-selected-action btn-edit-action pull-right" value="Edit selected action" disabled />
                    -->
                    <!-- Single button -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Add Item <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" class="tn-add-answer">Answer</a></li>
                            <li><a href="#" class="btn-add-dif-flow-answer">Different flow answer</a></li>
                            <li><a href="#" class="btn-add-action">Action</a></li>
                        </ul>
                    </div>
                    <input type="button" class="btn btn-danger btn-selected btn-remove-selected pull-right" value="Remove selected" disabled />
                    <input type="button" class="btn btn-default btn-selected btn-edit-selected pull-right" value="Edit selected" disabled />
                </div>
                <div class="box-body">
                    <div id="flow" style="height: 1000px"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal right fade" id="modal-add-dif-flow-answer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add other flow answer</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <select id="dif_flow_id" name="dif_flow_answer_id" class="form-control" required>
                            <option value="">Select Flow</option>
                            <?php
                            foreach ($flows as $item) {
                                if ($item->id !== $flow->id) {
                                    ?>
                            <option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <select id="dif_flow_answer_id" name="dif_flow_answer_id" class="form-control" required>
                            <option value="" data-flow-id="">Select Answer</option>
                            <?php
                            foreach ($flowsAnswers as $item) {
                                if ($item->flow_id !== $flow->id) {
                                    ?>
                                    <option value="<?php echo $item->id; ?>" data-flow-id="<?php echo $item->flow_id; ?>"><?php echo $item->answer_title; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="button" class="btn btn-primary btn-add-diff-flow-answer pull-right" value="Add" />
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal right fade modal-wide" id="modal-edit-answer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Answer</h4>
                </div>
                <div class="modal-body">
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal right fade modal-wide" id="modal-add-answer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Answer</h4>
                </div>
                <div class="modal-body">
                    <form action="" name="modal-add-answer-form" method="post">
                        <div class="alert hidden"></div>
                        <table class="table table-bordered">
                            <tr>
                                <td>Answer Title</td>
                                <td><input name="answer_title" class="form-control" type="text" value="" /></td>
                            </tr>
                            <tr>
                                <td colspan="2"><b>Messages</b></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="button" class="btn btn-default btn-add-message pull-right" value="Add message" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><b>Buttons</b></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="button" class="btn btn-default btn-add-button pull-right" value="Add Button" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="submit" class="btn btn-primary pull-right" value="Save answer" />
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@stop

@push('css')
    <link href="{{ Asset::get('vendor/jquery.flowchart-master/jquery.flowchart.min.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ Asset::get('vendor/adminlte/plugins/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>
    <script src="{{ Asset::get('vendor/jquery.flowchart-master/jquery.flowchart.min.js') }}"></script>
    <script>
        $(function() {
            var $flowchart = $('#flow');

            $flowchart.flowchart({
                data: <?php echo json_encode($data); ?>,
                defaultSelectedLinkColor: '#dd4b39',
                linkWidth: 5,
                multipleLinksOnInput: true,
                onOperatorMoved: function(operatorId, position) {
                    $.post({
                        url: '/flows/<?php echo $flow->id; ?>/preview/save-position/' + operatorId,
                        data: 'pos_x=' + position.left + '&pos_y=' + position.top
                    });
                },
                onOperatorSelect: function(operatorId) {
                    if (this.data.operators[operatorId].external !== true) {
                        $('.flow-controls .btn-selected-answer').each(function (i, obj) {
                            $(obj).data('answerId', operatorId);
                        });
                        $('.flow-controls .btn-selected-answer').prop('disabled', false);
                    }
                    return true;
                },
                onOperatorUnselect: function() {
                    $('.flow-controls .btn-selected-answer').prop('disabled', true);
                    return true;
                },
                onLinkSelect: function(linkId) {
                    $('.flow-controls .btn-selected-link').each(function(i, obj) {
                        $(obj).data('linkId', linkId);
                    });
                    $('.flow-controls .btn-selected-link').prop('disabled', false);
                    return true;
                },
                onLinkUnselect: function() {
                    $('.flow-controls .btn-selected-link').prop('disabled', true);
                    return true;
                },
                onLinkCreate: function (linkId, linkData) {
                    $.post({
                        url: '/flows/<?php echo $flow->id; ?>/preview/add-button-target/' + linkData.fromConnector + '/' + linkData.toOperator
                    });
                    return true;
                },
                onLinkDelete: function (linkId, forced) {
                    $.post({
                        url: '/flows/<?php echo $flow->id; ?>/preview/remove-button-target/' + this.data.links[linkId].fromConnector
                    });
                    return true;
                }
            });

            /*
             * Nav Buttons
             */
            var message_count = 0;
            $('.btn-add-answer').on('click', function() {
                message_count = 0;
                $('.answer-new-row').remove();
                $('form[name="modal-add-answer-form"]')[0].reset();
                $('#modal-add-answer').modal();
            });

            $('.btn-edit-answer').on('click', function() {
                message_count = 0;
                $('.answer-new-row').remove();

                $.post({
                    url: '/flows/<?php echo $flow->id; ?>/preview/edit-answer-form/' + $(this).data('answerId'),
                    success: function(data)
                    {
                        $('#modal-edit-answer .modal-body').html(data);

                        $('#modal-edit-answer .modal-body textarea').each(function(i, obj) {
                            CKEDITOR.replace($(obj).attr('id'));
                        });
                    }
                });

                $('#modal-edit-answer').modal();
            });

            $('.btn-delete-answer').on('click', function() {
                if (!confirm('Are you sure you want to delete this answer?')) return false;

                $.post({
                    url: '/flows/<?php echo $flow->id; ?>/preview/remove-answer/' + $(this).data('answerId')
                });
                $flowchart.flowchart('deleteSelected');

                return true;
            });

            $('.btn-delete-link').on('click', function() {
                if (!confirm('Are you sure you want to delete this link(answer button)?')) return false;
                $flowchart.flowchart('deleteSelected');

                return true;
            });

            $('.btn-add-dif-flow-answer').on('click', function() {
                $('#modal-add-dif-flow-answer').modal();
            });
            /*
             * Nav Buttons end
             */


            /*
             * Modals add/edit
             */
            //messages
            $('.modal').on('click', '.btn-add-message', function() {
                message_count++;
                $(this).parents('tr:first').before(
                    '<tr class="answer-new-row">' +
                        '<td colspan="2">' +
                            '<div class="form-group">' +
                                '<textarea id="message_text_' + message_count + '" name="message_text[]" placeholder="Place some message here" required></textarea>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<input name="message_method[]" class="form-control" type="text" value="" placeholder="Message method" />' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<input name="message_order[]" class="form-control" type="number" value="" placeholder="Message order" />' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<input type="button" class="btn btn-danger btn-remove-message pull-right" value="Remove" />' +
                            '</div>' +
                        '</td>' +
                    '</tr>');
                CKEDITOR.replace('message_text_' + message_count);
            });
            $('.modal').on('click', '.btn-remove-message', function() {
                $(this).parents('tr:first').remove();
            });

            //buttons
            $('.modal').on('click', '.btn-add-button', function() {
                $(this).parents('tr:first').before(
                    '<tr class="answer-new-row">' +
                        '<td colspan="2">' +
                            '<div class="form-group">' +
                                '<input name="button_text[]" class="form-control" type="text" value="" placeholder="Button text" />' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<input name="button_order[]" class="form-control" type="number" value="" placeholder="Button order" />' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<input type="button" class="btn btn-danger btn-remove-button pull-right" value="Remove" />' +
                            '</div>' +
                        '</td>' +
                    '</tr>');
            });
            $('.modal').on('click', '.btn-remove-button', function() {
                $(this).parents('tr:first').remove();
            });

            $('form[name="modal-add-answer-form"]').on('submit', function () {

                $.post({
                    url: '/flows/<?php echo $flow->id; ?>/preview/add-answer',
                    data: $(this).serialize(),
                    success: function(data)
                    {
                        if (typeof data.operatorId !== 'undefined' && typeof data.operatorData !== 'undefined') {
                            $flowchart.flowchart('createOperator', data.operatorId, data.operatorData);
                            $('#modal-add-answer').modal('hide');
                        }
                    }
                });

                return false;
            });

            $('#modal-edit-answer').on('submit', 'form[name="modal-edit-answer-form"]', function () {

                $.post({
                    url: '/flows/<?php echo $flow->id; ?>/preview/update-answer',
                    data: $(this).serialize(),
                    success: function(data)
                    {
                        if (typeof data.operatorId !== 'undefined' && typeof data.operatorData !== 'undefined') {
                            $flowchart.flowchart('setOperatorData', data.operatorId, data.operatorData);
                            $('#modal-edit-answer').modal('hide');
                        }
                    }
                });

                return false;
            });
            /*
             * Modals add/edit end
             */

            /*
             * Modal add diff flow
             */
            updateDifFlowAnswersList($('#dif_flow_id option:selected').val());

            $('#dif_flow_id').on('change', function() {
                updateDifFlowAnswersList($(this).val());
            });

            $('.btn-add-diff-flow-answer').on('click', function() {
                var operatorData = {
                    top: 200,
                    left: 200,
                    properties: {
                        title: $('#dif_flow_id option:selected').text() + ': ' + $('#dif_flow_answer_id option:selected').text(),
                    }
                };
                var inputId = $('#dif_flow_answer_id option:selected').val();
                operatorData.external = true;
                operatorData.properties.inputs = {};
                operatorData.properties.inputs[inputId] = {};
                operatorData.properties.inputs[inputId].label = '';

                var operatorId = $('#dif_flow_answer_id option:selected').val();

                $flowchart.flowchart('createOperator', operatorId, operatorData);

                $('#modal-add-dif-flow-answer').modal('hide');
            });
            /*
             * Modal add diff flow end
             */
        });

        function updateDifFlowAnswersList(flowId) {
            $('#dif_flow_answer_id option').each(function(i, obj) {
                if ($(obj).data('flowId') == flowId || $(obj).data('flowId') === '') {
                    $(obj).show();
                } else {
                    $(obj).hide();
                }
            });
            $('#dif_flow_answer_id').val('');
        }
    </script>
@endpush

