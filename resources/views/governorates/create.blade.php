@extends('layouts.app')
@inject('model','App\Models\Governorate')
@section('page_title')
   إضافة محافظة
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-body">
                {!! Form::model($model,[
                    'action' => 'GovernorateController@store'
                ]) !!}
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
