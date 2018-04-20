@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Customers List <?php echo $title; ?></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <form id="frm-filter">
                        <div class="form-inline">
                            <div class="form-group">
                                <select name="is_vip" class="form-control">
                                    <option value="all" <?php echo $filters->is_vip == 'all' ? ' selected ' : ''; ?>>Vip and Not</option>
                                    <option value="1" <?php echo $filters->is_vip == '1' ? ' selected ' : ''; ?>>Vip</option>
                                    <option value="0" <?php echo $filters->is_vip == '0' ? ' selected ' : ''; ?>>Not Vip</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="is_blocked" class="form-control">
                                    <option value="all" <?php echo $filters->is_blocked == 'all' ? ' selected ' : ''; ?>>Unsubscribed and Not</option>
                                    <option value="1" <?php echo $filters->is_blocked == '1' ? ' selected ' : ''; ?>>Unsubscribed</option>
                                    <option value="0" <?php echo $filters->is_blocked == '0' ? ' selected ' : ''; ?>>Not Unsubscribed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="customer_from" class="form-control">
                                    <option value="all" <?php echo $filters->customer_from == 'all' ? ' selected ' : ''; ?>>Leads from anywhere</option>
                                    <option value="facebook" <?php echo $filters->customer_from == 'facebook' ? ' selected ' : ''; ?>>Facebook</option>
                                    <option value="telegram" <?php echo $filters->customer_from == 'telegram' ? ' selected ' : ''; ?>>Telegram</option>
                                    <option value="0" <?php echo $filters->customer_from == '0' ? ' selected ' : ''; ?>>Not recognized</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="source" class="form-control">
                                    <option value="all" <?php echo $filters->source == 'all' ? ' selected ' : ''; ?>>Any Ref</option>
                                    <?php
                                    foreach ($refs as $ref) {
                                        ?>
                                        <option value="<?php echo $ref->source; ?>" <?php echo $filters->source == $ref->source ? ' selected ' : ''; ?>><?php echo $ref->source; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
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
                    <table class="table table-bordered" id="customers-table">
                        <thead>
                        <tr>
                            <th class="col-md-1">Id</th>
                            <th class="col-md-1">Vip</th>
                            <th>Ref</th>
                            <th class="col-md-2">Created</th>
                            <th>Name</th>
                            <th class="col-md-2">Lead from</th>
                            <th class="col-md-1">Unsubscribed</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Vip</th>
                            <th>Ref</th>
                            <th>Created</th>
                            <th>Name</th>
                            <th>Lead from</th>
                            <th>Unsubscribed</th>
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

    <div class="modal right fade" id="modal-customer-details">
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
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.27/daterangepicker.min.css" rel="stylesheet" type="text/css">
@endpush

@push('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.27/moment.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.27/daterangepicker.min.js"></script>
    <script>
        $(function() {
            var opts = {
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "/customers/list-data",
                    "data": function (d) {
                            d.filters = getFiltersData();
                    },
                },
                keepConditions: true,
                'order': [[0, 'desc']],
                'pageLength': 25
            };

            var hash = window.location.hash;
            var hashDecodeded = decodeURI(hash).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"');
            if (hashDecodeded) {
                var hashObj = JSON.parse('{"' + decodeURI(hash).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') + '"}');
                if (hashObj['#customers-table']) {
                    var pageNum = 0;
                    var pageLength = 0;
                    var hashParams = hashObj['#customers-table'].split(':');
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

            var customersDataTable = $('#customers-table').DataTable(opts);

            $('#customers-table').on('click', '.lnk-customer-details', function(e) {
                $.get('/customers/get-customer-details/' + $(this).data('id'), function(response){
                    $('#modal-customer-details .modal-body').html(response);
                });
                $('#modal-customer-details .modal-title').html($(this).html());
                $('#modal-customer-details').modal();
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
                    reloadData(customersDataTable);
                    return;
                }
            );

            $('#frm-filter input, #frm-filter select').change(function() {
                reloadData(customersDataTable);
                return;
            });
        });

        function reloadData(dataTable) {
            var url = $('#frm-filter').serialize();

            history.pushState(null, null, '/customers/list?' + url + window.location.hash);
            dataTable.ajax.reload();

            return;
        }

        function getFiltersData()
        {
            var search = location.search.substring(1);
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
