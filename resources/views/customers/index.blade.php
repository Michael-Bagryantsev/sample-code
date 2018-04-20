@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
<h1>Customers Dates + Amo Links <?php echo $title; ?></h1>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-bordered" id="customers-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>From</th>
                            <th>Amo Contact ID</th>
                            <th>Amo Lead ID</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>To manager</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>From</th>
                            <th>Amo Contact ID</th>
                            <th>Amo Lead ID</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>To manager</th>
                            <th>Completed</th>
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
@stop

@push('js')
<script>
    $(function() {
        $('#customers-table').DataTable({
            data: <?php echo json_encode($data) ?>,
            keepConditions: true,
            'order': [[0, 'desc']],
            'pageLength': 25
        });
    });
</script>
@endpush
