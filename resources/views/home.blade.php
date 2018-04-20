@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-telegram"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Telegram today new</span>
                <span class="info-box-number"><?php echo $stats->telegramTodayNew; ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-facebook"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Facebook today new</span>
                <span class="info-box-number"><?php echo $stats->facebookTodayNew; ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix visible-sm-block"></div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red-active"><i class="fa fa-telegram"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Telegram today completed</span>
                <span class="info-box-number"><?php echo $stats->telegramTodayCompleted; ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-blue-active"><i class="fa fa-facebook"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Facebook today completed</span>
                <span class="info-box-number"><?php echo $stats->facebookTodayCompleted; ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Overall stats</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div  class="box-body">
                <div class="input-group pull-right">
                    <button type="button" class="btn btn-default pull-right" id="btn-select-charts-range">
                        <span>
                          <i class="fa fa-calendar"></i> <?php echo date('Y-m-d', strtotime($stats->start)) . ' - ' . date('Y-m-d', strtotime($stats->end)); ?>
                        </span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                </div>
                <div class="pull-left">Correct data since: 2018-03-16</div>
                <div class="chart">
                    <canvas id="perDayNewBotLeadsChart" style="height:230px"></canvas>
                </div>
                <div class="chart">
                    <canvas id="perDayNewManagerLeadsChart" style="height:230px"></canvas>
                </div>
                <div class="chart">
                    <canvas id="perDayCompletedLeadsChart" style="height:230px"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.27/daterangepicker.min.css" rel="stylesheet" type="text/css">
@endpush

@push('js')
<script src="//cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.27/moment.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.27/daterangepicker.min.js"></script>
<script>
    (function($) {
        /**
         * New bot leads
         */
        var newBotLeads = document.getElementById("perDayNewBotLeadsChart");
        var newBotLeadsChart = new Chart(newBotLeads.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($stats->statsPerDay->dates); ?>,
                datasets: [{
                    label: 'Telegram',
                    data: <?php echo json_encode($stats->statsPerDay->new_leads_bot_telegram); ?>,
                    datalabels: {
                        align: 'end',
                        anchor: 'end',
                        offset: -5
                    },
                    backgroundColor: '#dd4b39',
                    borderColor: '#dd4b39',
                    borderWidth: 1
                },
                    {
                        label: 'Facebook',
                        data: <?php echo json_encode($stats->statsPerDay->new_leads_bot_facebook); ?>,
                        datalabels: {
                            align: 'end',
                            anchor: 'end',
                            offset: -5
                        },
                        backgroundColor: '#3c8dbc',
                        borderColor: '#3c8dbc',
                        borderWidth: 1
                    }]
            },
            options: {
                xAxes: [{
                    barThickness : 30
                }],
                plugins: {
                    datalabels: {
                        display: function(context) {
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        font: {
                            weight: 'bold'
                        },
                    }
                },
                title: {
                    display: true,
                    text: 'New clients wrote to bot per day'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                },
                hover: {
                    onHover: function(e) {
                        var point = this.getElementAtEvent(e);
                        if (point.length) e.target.style.cursor = 'pointer';
                        else e.target.style.cursor = 'default';
                    }
                }
            }
        });
        newBotLeads.onclick = function(e) {
            var slice = newBotLeadsChart.getElementAtEvent(e);
            if (slice.length) {
                var url = '/customers?from=' + slice[0]._model.datasetLabel.toLowerCase() + '&lead_created=' + slice[0]._model.label;
                document.location.href = url;
                return;
            }

            var base = newBotLeadsChart.chartArea.bottom;
            var width = newBotLeadsChart.chart.scales['x-axis-0'].width;
            var offset = $('#perDayNewBotLeadsChart').offset().top - $(window).scrollTop();
            if(e.pageY > base + offset){
                var count = newBotLeadsChart.scales['x-axis-0'].ticks.length;
                var padding_left = newBotLeadsChart.scales['x-axis-0'].paddingLeft;
                var padding_right = newBotLeadsChart.scales['x-axis-0'].paddingRight;
                var xwidth = (width-padding_left-padding_right)/count;
                var bar_index = (e.offsetX - padding_left - newBotLeadsChart.scales['y-axis-0'].width) / xwidth;
                if(bar_index > 0 & bar_index < count){
                    bar_index = Math.floor(bar_index);
                    var url = '/customers?lead_created=' + newBotLeadsChart.config.data.labels[bar_index];
                    document.location.href = url;
                    return;
                }
            }
        }

        /**
         * Manager inbox leads
         */
        var newManagerLeads = document.getElementById("perDayNewManagerLeadsChart");
        var newManagerLeadsChart = new Chart(newManagerLeads.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($stats->statsPerDay->dates); ?>,
                datasets: [{
                    label: 'Telegram',
                    data: <?php echo json_encode($stats->statsPerDay->new_leads_manager_telegram); ?>,
                    datalabels: {
                        align: 'end',
                        anchor: 'end',
                        offset: -5
                    },
                    backgroundColor: '#dd4b39',
                    borderColor: '#dd4b39',
                    borderWidth: 1
                },
                    {
                    label: 'Facebook',
                    data: <?php echo json_encode($stats->statsPerDay->new_leads_manager_facebook); ?>,
                    datalabels: {
                        align: 'end',
                        anchor: 'end',
                        offset: -5
                    },
                    backgroundColor: '#3c8dbc',
                    borderColor: '#3c8dbc',
                    borderWidth: 1
                }]
            },
            options: {
                xAxes: [{
                    barThickness : 30
                }],
                plugins: {
                    datalabels: {
                        display: function(context) {
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        font: {
                            weight: 'bold'
                        },
                    }
                },
                title: {
                    display: true,
                    text: 'New clients in manager inbox per day'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                },
                hover: {
                    onHover: function(e) {
                        var point = this.getElementAtEvent(e);
                        if (point.length) e.target.style.cursor = 'pointer';
                        else e.target.style.cursor = 'default';
                    }
                }
            }
        });
        newManagerLeads.onclick = function(e) {
            var slice = newManagerLeadsChart.getElementAtEvent(e);
            if (slice.length) {
                var url = '/customers?from=' + slice[0]._model.datasetLabel.toLowerCase() + '&lead_manager=' + slice[0]._model.label;
                document.location.href = url;
                return;
            }

            var base = newManagerLeadsChart.chartArea.bottom;
            var width = newManagerLeadsChart.chart.scales['x-axis-0'].width;
            var offset = $('#perDayNewManagerLeadsChart').offset().top - $(window).scrollTop();
            if(e.pageY > base + offset){
                var count = newManagerLeadsChart.scales['x-axis-0'].ticks.length;
                var padding_left = newManagerLeadsChart.scales['x-axis-0'].paddingLeft;
                var padding_right = newManagerLeadsChart.scales['x-axis-0'].paddingRight;
                var xwidth = (width-padding_left-padding_right)/count;
                var bar_index = (e.offsetX - padding_left - newManagerLeadsChart.scales['y-axis-0'].width) / xwidth;
                if(bar_index > 0 & bar_index < count){
                    bar_index = Math.floor(bar_index);
                    var url = '/customers?lead_manager=' + newManagerLeadsChart.config.data.labels[bar_index];
                    document.location.href = url;
                    return;
                }
            }
        }


        /**
         * Completed leads
         */
        var completedLeads = document.getElementById("perDayCompletedLeadsChart");
        var completedLeadsChart = new Chart(completedLeads.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($stats->statsPerDay->dates); ?>,
                datasets: [{
                    label: 'Telegram',
                    data: <?php echo json_encode($stats->statsPerDay->leads_completed_telegram); ?>,
                    datalabels: {
                        align: 'end',
                        anchor: 'end',
                        offset: -5
                    },
                    backgroundColor: '#dd4b39',
                    borderColor: '#dd4b39',
                    borderWidth: 1
                },
                    {
                    label: 'Facebook',
                    data: <?php echo json_encode($stats->statsPerDay->leads_completed_facebook); ?>,
                        datalabels: {
                            align: 'end',
                            anchor: 'end',
                            offset: -5
                        },
                    backgroundColor: '#3c8dbc',
                    borderColor: '#3c8dbc',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        display: function(context) {
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        font: {
                            weight: 'bold'
                        },
                    }
                },
                title: {
                    display: true,
                    text: 'Completed orders per day'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                },
                hover: {
                    onHover: function(e) {
                        var point = this.getElementAtEvent(e);
                        if (point.length) e.target.style.cursor = 'pointer';
                        else e.target.style.cursor = 'default';
                    }
                }
            }
        });
        completedLeads.onclick = function(e) {
            var slice = completedLeadsChart.getElementAtEvent(e);
            if (slice.length) {
                var url = '/customers?from=' + slice[0]._model.datasetLabel.toLowerCase() + '&lead_completed=' + slice[0]._model.label;
                document.location.href = url;
                return;
            }

            var base = completedLeadsChart.chartArea.bottom;
            var width = completedLeadsChart.chart.scales['x-axis-0'].width;
            var offset = $('#perDayCompletedLeadsChart').offset().top - $(window).scrollTop();
            if(e.pageY > base + offset){
                var count = completedLeadsChart.scales['x-axis-0'].ticks.length;
                var padding_left = completedLeadsChart.scales['x-axis-0'].paddingLeft;
                var padding_right = completedLeadsChart.scales['x-axis-0'].paddingRight;
                var xwidth = (width-padding_left-padding_right)/count;
                var bar_index = (e.offsetX - padding_left - completedLeadsChart.scales['y-axis-0'].width) / xwidth;
                if(bar_index > 0 & bar_index < count){
                    bar_index = Math.floor(bar_index);
                    var url = '/customers?lead_completed=' + completedLeadsChart.config.data.labels[bar_index];
                    document.location.href = url;
                    return;
                }
            }
        }

        $('#btn-select-charts-range').daterangepicker(
            {
                ranges   : {
                    'Today'       : [moment(), moment()],
                    'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                    'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment(<?php echo strtotime($stats->start)*1000; ?>),
                endDate  : moment(<?php echo strtotime($stats->end)*1000; ?>)
            },
            function (start, end) {
                var url = '/home?stats_start=' + start.format('YYYY-MM-DD') + '&stats_end=' + end.format('YYYY-MM-DD');
                document.location.href = url;
                return;
            }
        );
    })(jQuery);
</script>
@endpush