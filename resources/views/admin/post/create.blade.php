@extends('admin.layout')

@section('styles')
    <link href="{{asset('public/assets/pickadate/themes/default.css')}}" rel="stylesheet">
    <link href="{{asset('public/assets/pickadate/themes/default.date.css')}}" rel="stylesheet">
    <link href="{{asset('public/assets/pickadate/themes/default.time.css')}}" rel="stylesheet">
    <link href="{{asset('public/assets/selectize/css/selectize.css')}}" rel="stylesheet">
    <link href="{{asset('public/assets/selectize/css/selectize.bootstrap3.css')}}" rel="stylesheet">
@stop

@section('content')
    <div class="container-fluid">
        <div class="row page-title-row">
            <div class="col-md-12">
                <h3>Posts <small>» Add New Post</small></h3>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">New Post Form</h3>
                    </div>
                    <div class="panel-body">

                        @include('admin.partials.errors')

                        <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.post.store') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            {{--文章的请求表单 独立出来 方便维护--}}
                            @include('admin.post._form')

                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="col-md-10 col-md-offset-2">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fa fa-disk-o"></i>
                                            Save New Post
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    {{--时间的选择插件--}}
    <script src="{{asset('public/assets/pickadate/picker.js')}}"></script>
    <script src="{{asset('public/assets/pickadate/picker.date.js')}}"></script>
    <script src="{{asset('public/assets/pickadate/picker.time.js')}}"></script>
    {{--选择ui的插件--}}
    <script src="{{asset('public/assets/selectize/selectize.min.js')}}"></script>
    <script>
        $(function() {
            $("#publish_date").pickadate({
                //日期格式
                format: "mmm-d-yyyy"
            });
            $("#publish_time").pickatime({
                //时间格式
                format: "h:i A"
            });
            $("#tags").selectize({
                create: true
            });
        });
    </script>
@stop