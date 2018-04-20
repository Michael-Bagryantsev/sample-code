@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Add Flow
        <a href="/flows" class="btn btn-default pull-right">Back</a></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <form id="frm-edit-message" method="POST" action="/flows">
                    {{ csrf_field() }}
                    <div class="box-body pad">
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="Flow Name(for you only)" required />
                        </div>
                        <div class="form-group">
                            <select name="condition_id" class="form-control" required>
                                <option value="">Condition</option>
                                <?php
                                foreach ($conditions as $item) {
                                ?>
                                <option value="<?php echo $item->id; ?>"><?php echo $item->condition_name; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control" name="percent" placeholder="Percentage (use it to make several flows work on one condition, total sum for all should be - 100)" default="100" required />
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

