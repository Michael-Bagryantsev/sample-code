@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Flows Answers
        <a href="/flows-answers/create" class="btn btn-primary pull-right">Add Answer</a></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    @if (Session::has('message'))
                        <div class="alert alert-info">{{ Session::get('message') }}</div>
                    @endif
                    <table class="table table-bordered" id="items-table">
                        <thead>
                        <tr>
                            <th class="col-md-1">Id</th>
                            <th class="col-md-2">Flow</th>
                            <th>Answer</th>
                            <th class="col-md-2">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($flowsAnswers as $item) {
                        ?>
                        <tr>
                            <td><?php echo $item->id; ?></td>
                            <td><?php echo $item->name; ?></td>
                            <td><?php echo $item->answer_title; ?></td>
                            <td>
                                <a href="/flows-answers/{{ $item->id }}/edit" class="btn btn-warning" title="Edit"><i class="fa fa-pencil"></i></a>
                                {!! Form::open(['method' => 'DELETE',
                                    'class' => 'btn-form',
                                    'route' => ['flows.destroy', $item->id],
                                    'id' => 'form-delete-' . $item->id]) !!}
                                <a href="/flows-answers" class="btn btn-danger btn-delete" title="Delete" data-id="{{ $item->id }}"><i class="fa fa-trash"></i></a>
                                {!! Form::close() !!}

                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th class="col-md-1">Id</th>
                            <th class="col-md-2">Flow</th>
                            <th>Answer</th>
                            <th class="col-md-2">Actions</th>
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
            $('#items-table').DataTable(
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
        });
    </script>
@endpush
