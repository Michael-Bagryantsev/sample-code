@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Scheduled Messages
        <a href="/scheduled-messages/create" class="btn btn-primary pull-right">Add New Message</a></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    @if (Session::has('message'))
                        <div class="alert alert-info">{{ Session::get('message') }}</div>
                    @endif
                    <table class="table table-bordered" id="scheduled-messages-table">
                        <thead>
                        <tr>
                            <th class="col-md-1">Id</th>
                            <th>Title</th>
                            <th class="col-md-2">Group</th>
                            <th class="col-md-2">Condition</th>
                            <th class="col-md-1">Active</th>
                            <th class="col-md-2">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($scheduledMessages as $message) {
                        ?>
                        <tr>
                            <td><?php echo $message->id; ?></td>
                            <td><?php echo $message->message_name; ?></td>
                            <td><?php echo $message->group_name; ?></td>
                            <td><?php echo $message->condition_name; ?></td>
                            <td><?php echo ($message->is_active === 1) ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="/scheduled-messages/{{ $message->id }}/preview" class="btn btn-success btn-preview" title="Send to test channel">
                                    <i class="fa fa-play"></i></a>
                                <a href="/scheduled-messages/{{ $message->id }}/edit" class="btn btn-warning" title="Edit"><i class="fa fa-pencil"></i></a>
                                {!! Form::open(['method' => 'DELETE',
                                    'class' => 'btn-form',
                                    'route' => ['scheduled-messages.destroy', $message->id],
                                    'id' => 'form-delete-' . $message->id]) !!}
                                <a href="/scheduled-messages" class="btn btn-danger btn-delete" title="Delete" data-id="{{ $message->id }}"><i class="fa fa-trash"></i></a>
                                {!! Form::close() !!}

                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Group</th>
                            <th>Condition</th>
                            <th>Active</th>
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
            $('#scheduled-messages-table').DataTable(
                {
                    'order': [[0, 'desc']],
                    'pageLength': 25
                }
            );

            $('.btn-delete').on('click', function () {
                if (!confirm('Are you sure you want to delete?')) return false;
                $('#form-delete-' + $(this).data('id')).submit();
                return false;
            });

            $('.btn-preview').on('click', function () {
                $.get($(this).attr('href'), function(response) {
                    if (response === 'ok') {
                        alert('Message sent');
                    } else {
                        alert('Message not sent. Error occurred.');
                    }
                });
                return false;
            });
        });
    </script>
@endpush
