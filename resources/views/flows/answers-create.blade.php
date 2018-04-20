@extends('adminlte::page')

@section('title', 'CryptoMaker')

@section('content_header')
    <h1>Add Answer
        <a href="/flows-answers" class="btn btn-default pull-right">Back</a></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <form id="frm-edit-message" method="POST" action="/flows-answers">
                    {{ csrf_field() }}
                    <div class="box-body pad">
                        <div class="form-group">
                            <input type="text" class="form-control" name="answer_title" placeholder="Answer Title(for you only)" required />
                        </div>
                        <div class="form-group">
                            <select name="flow_id" class="form-control" required>
                                <option value="">Flow</option>
                                <?php
                                foreach ($flows as $item) {
                                ?>
                                <option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
                                <?php
                                }
                                ?>
                            </select>
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

