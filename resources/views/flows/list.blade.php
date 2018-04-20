@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Flows
        <a href="/flows/create" class="btn btn-primary pull-right">Add Flow</a></h1>
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
                            <th>Name</th>
                            <th class="col-md-2">Condition</th>
                            <th class="col-md-1">Percent</th>
                            <th class="col-md-1">Active</th>
                            <th class="col-md-2">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($flows as $flows) {
                        ?>
                        <tr>
                            <td><?php echo $flows->id; ?></td>
                            <td><?php echo $flows->name; ?></td>
                            <td><?php echo $flows->condition_name; ?></td>
                            <td><?php echo $flows->percent; ?>%</td>
                            <td><?php echo ($flows->is_active === 1) ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="/flows/{{ $flows->id }}/preview" class="btn btn-primary" title="Preview"><i class="fa fa-eye"></i></a>
                                <a href="/flows/{{ $flows->id }}/edit" class="btn btn-warning" title="Edit"><i class="fa fa-pencil"></i></a>
                                {!! Form::open(['method' => 'DELETE',
                                    'class' => 'btn-form',
                                    'route' => ['flows.destroy', $flows->id],
                                    'id' => 'form-delete-' . $flows->id]) !!}
                                <a href="/flows" class="btn btn-danger btn-delete" title="Delete" data-id="{{ $flows->id }}"><i class="fa fa-trash"></i></a>
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
                            <th>Name</th>
                            <th class="col-md-2">Condition</th>
                            <th class="col-md-1">Percent</th>
                            <th class="col-md-1">Active</th>
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
