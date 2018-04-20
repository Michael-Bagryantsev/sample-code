@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
<h1>Users</h1>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-bordered" id="users-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Approved</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($users as $user) {
                            ?>
                            <tr>
                                <td><?php echo $user->id; ?></td>
                                <td><?php echo $user->name; ?></td>
                                <td><?php echo $user->email; ?></td>
                                <td><?php echo ($user->is_approved === true) ? 'Yes' : 'No'; ?></td>
                                <td><?php echo $user->created_at; ?></td>
                                <td><?php echo $user->updated_at; ?></td>
                                <td>
                                    <a href="#" class="btn btn-<?php echo ($user->is_approved === true) ? 'warning' : 'success'; ?>"
                                       title="<?php echo ($user->is_approved === true) ? 'Deactivate' : 'Activate'; ?>">
                                        <i class="fa fa-<?php echo ($user->is_approved === true) ? 'minus' : 'plus'; ?>-circle"></i></a>
                                    <a href="#" class="btn btn-warning" title="Edit"><i class="fa fa-pencil"></i></a>
                                    <a href="#" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Approved</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
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
        $('#users-table').DataTable();
    });
</script>
@endpush
