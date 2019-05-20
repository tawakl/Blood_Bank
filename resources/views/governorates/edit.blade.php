@extends('layouts.app')
@section('page_title')
تعديل المحافظة
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
            </div>
            <div class="box-body">
                {!! Form::model($model,[
                    'action' => ['GovernorateController@update',$model->id],
                    'method' => 'put'
                ]) !!}
                @include('flash::message')
                @include('partials.validation_errors')
                @include('governorates.form')
                {!! Form::close() !!}
            </div>
            <!-- /.box-footer-->
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection
