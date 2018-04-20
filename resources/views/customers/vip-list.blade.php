@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Vip Users</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <form id="frm-filter">
                        <div class="form-inline">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default <?php echo ($filters->show_ok === true ? ' active' : ''); ?>">
                                    <input type="checkbox" name="show_ok" <?php echo ($filters->show_ok === true ? ' checked' : ''); ?>  value="on" /> Vip ok
                                </label>
                                <label class="btn btn-default <?php echo ($filters->show_expired === true ? ' active' : ''); ?>">
                                    <input type="checkbox" name="show_expired" <?php echo ($filters->show_expired === true ? ' checked' : ''); ?> value="on" /> Vip expired
                                </label>
                                <label class="btn btn-default <?php echo ($filters->show_admins === true ? ' active' : ''); ?>">
                                    <input type="checkbox" name="show_admins" <?php echo ($filters->show_admins === true ? ' checked' : ''); ?>  value="on" /> Admins
                                </label>
                                <label class="btn btn-default <?php echo ($filters->show_banned === true ? ' active' : ''); ?>">
                                    <input type="checkbox" name="show_banned" <?php echo ($filters->show_banned === true ? ' checked' : ''); ?>  value="on" /> Banned
                                </label>
                                <label class="btn btn-default <?php echo ($filters->show_unknown === true ? ' active' : ''); ?>">
                                    <input type="checkbox" name="show_unknown" <?php echo ($filters->show_unknown === true ? ' checked' : ''); ?>  value="on" /> Unknown
                                </label>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="start" value="" />
                                <input type="hidden" name="end" value="" />
                                <button type="button" class="btn btn-default pull-right" id="btn-select-range">
                                <span class="capt-range">
                                  <i class="fa fa-calendar"></i> <?php echo date('Y-m-d', strtotime($filters->start)) . ' - ' . date('Y-m-d', strtotime($filters->end)); ?>
                                </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    @if (Session::has('message'))
                        <div class="alert alert-info">{{ Session::get('message') }}</div>
                    @endif
                    <table class="table table-bordered" id="vip-table">
                        <thead>
                        <tr>
                            <th class="col-md-1">Id</th>
                            <th>Name</th>
                            <th class="col-md-1">Role</th>
                            <th class="col-md-1">Vip From</th>
                            <th class="col-md-1">Vip Till</th>
                            <th class="col-md-2">Amo Contact/Lead</th>
                            <th class="col-md-1">Payment</th>
                            <th class="col-md-1">Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="col-md-1">Id</th>
                            <th>Name</th>
                            <th class="col-md-1">Role</th>
                            <th class="col-md-1">Vip From</th>
                            <th class="col-md-1">Vip Till</th>
                            <th class="col-md-2">Amo Contact/Lead</th>
                            <th class="col-md-1">Payment</th>
                            <th class="col-md-1">Actions</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="modal right fade" id="modal-vip-details">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@stop

@push('css')
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.27/daterangepicker.min.css" rel="stylesheet" type="text/css">
@endpush

@push('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.27/moment.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.27/daterangepicker.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(function() {
            var opts = {
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "/customers/vip/list-data",
                    "data": function (d) {
                        d.filters = getFiltersData();
                    },
                },
                keepConditions: true,
                'order': [[0, 'desc']],
                'pageLength': 25,
                "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                    var today = moment().format("YYYY-MM-DD");
                    if ('canceled' == aData[2]) {
                        $(nRow).addClass('warning');
                    } else if (today > aData[4] && aData[4] !== '') {
                        $(nRow).addClass('error');
                    } else if (today <= aData[4]) {
                        $(nRow).addClass('success');
                    } else if ('banned' == aData[2]) {
                        $(nRow).addClass('grey');
                    }
                    return nRow;
                }
            };

            var hash = window.location.hash;
            var hashDecodeded = decodeURI(hash).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"');
            if (hashDecodeded) {
                var hashObj = JSON.parse('{"' + decodeURI(hash).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') + '"}');
                if (hashObj['#vip-table']) {
                    var pageNum = 0;
                    var pageLength = 0;
                    var hashParams = hashObj['#vip-table'].split(':');
                    for (var i = 0; i < hashParams.length; i++) {
                        switch (hashParams[i][0]) {
                            case 'f': //search str
                                opts.search = {
                                    "search": hashParams[i].substring(1)
                                };
                                break;
                            case 'l': //length
                                pageLength = opts.pageLength = hashParams[i].substring(1);
                                break;
                            case 'p': //page
                                pageNum = hashParams[i].substring(1);
                                break;
                            case 'o': //direction
                                var direction = 'asc';
                                if (hashParams[i].substring(1, 2) === 'd') {
                                    direction = 'desc';
                                }
                                opts.order = [[hashParams[i].substring(2), direction]];
                                break;
                        }
                    }
                    opts.displayStart = pageNum * pageLength;
                }
            }

            var vipDataTable = $('#vip-table').DataTable(opts);

            $('#vip-table').on('click', '.lnk-vip-details', function(e) {
                $.get('/customers/get-vip-details/' + $(this).data('id'), function(response){
                    $('#modal-vip-details .modal-body').html(response).promise().done(function(){
                        $('#modal-vip-details .datepicker').datepicker({
                            format: "yyyy-mm-dd"
                        });
                    });
                });
                $('#modal-vip-details .modal-title').html($(this).data('name'));
                $('#modal-vip-details').modal();

                return false;
            });

            $('#btn-select-range').daterangepicker(
                {
                    ranges   : {
                        'Today'       : [moment(), moment()],
                        'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                        'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment(<?php echo strtotime($filters->start)*1000; ?>),
                    endDate  : moment(<?php echo strtotime($filters->end)*1000; ?>)
                },
                function (start, end) {
                    $("#frm-filter input[name='start']").val(start.format('YYYY-MM-DD'));
                    $("#frm-filter input[name='end']").val(end.format('YYYY-MM-DD'));
                    $('#btn-select-range .capt-range').html('<i class="fa fa-calendar"></i> ' + start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                    reloadData(vipDataTable);
                    return;
                }
            );

            $('#frm-filter').on('change', ':checkbox', function() {
                reloadData(vipDataTable);
                return;
            });

            $('#modal-vip-details').on('submit', 'form', function() {
                $.post({
                    url: '/customers/save-vip',
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('#modal-vip-details .alert').removeClass('alert-error');
                        $('#modal-vip-details .alert').removeClass('alert-success');
                        $('#modal-vip-details .alert').addClass('hidden');
                    },
                    success: function(data)
                    {
                        if (data.result === true) {
                            $('#modal-vip-details .alert').addClass('alert-success');
                        } else {
                            $('#modal-vip-details .alert').addClass('alert-error');
                        }
                        $('#modal-vip-details .alert').html(data.message);
                        $('#modal-vip-details .alert').removeClass('hidden');
                    },
                    error: function(data)
                    {
                        $('#modal-vip-details .alert').addClass('alert-error');
                        $('#modal-vip-details .alert').html('Unknown error');
                        $('#modal-vip-details .alert').removeClass('hidden');
                    }
                });

                return false;
            });

            $('#modal-vip-details').on('click', '.btn-set-vip-till', function() {
                var vip_from = $('#modal-vip-details input[name="vip_from"]').val();
                var billing_plan_id = $('#modal-vip-details select[name="billing_plan_id"] option:selected').val();
                if (vip_from == '' || billing_plan_id == '') {
                    alert('Vip from and billing plan required.');
                    return;
                }

                $.post({
                    url: '/customers/get-vip-till',
                    data: 'vip_from=' + vip_from + '&billing_plan_id=' + billing_plan_id,
                    success: function(data)
                    {
                        $('#modal-vip-details input[name="vip_till"]').val(data.result);
                    },
                });
            });

            $('#modal-vip-details').on('click', '.btn-cancel-vip', function() {
                var id = $(this).data('id');
                if (confirm('Cancel users vip?')) {
                    $.post({
                        url: '/customers/cancel-vip/' + id,
                        success: function(data)
                        {
                            $('#modal-vip-details select[name="telegram_role"]').val('canceled');
                        },
                    });
                }
            });

        });

        function reloadData(dataTable) {

            var url = $('#frm-filter').serialize();

            history.pushState(null, null, '/customers/vip?' + url + window.location.hash);
            dataTable.ajax.reload();

            return;
        }

        function getFiltersData()
        {
            var search = location.search.substring(1) || $('#frm-filter').serialize();

            if (search) {
                return JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function (key, value) {
                    return key === "" ? value : decodeURIComponent(value)
                });
            } else {
                return;
            }
        }
    </script>
@endpush