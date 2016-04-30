@extends('blog.layouts.master')

@section('page-header')
    <header class="intro-header"
            {{--当前的url地址为http://laravel51_blog.com/ 所以可以直接显示我们的图片--}}
            style="background-image: url('{{ page_image($page_image) }}')">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <div class="site-heading">
                        <h1>{{ $title }}</h1>
                        <hr class="small">
                        <h2 class="subheading">{{ $subtitle }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </header>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">

                {{-- 文章列表 --}}
                @foreach ($posts as $post)
                    <div class="post-preview">
                        {{-- url()方法链接到指定文章详情--}}
                        <a href="{{ $post->url($tag) }}">
                            <h2 class="post-title">{{ $post->title }}</h2>
                            @if ($post->subtitle)
                                <h3 class="post-subtitle">{{ $post->subtitle }}</h3>
                            @endif
                        </a>
                        <p class="post-meta">
                            Posted on {{ $post->published_at->format('F j, Y') }}
                            @if ($post->tags->count())
                                &nbsp; &nbsp;tag in [
                                {{--tagLinks() 方法返回一个链接数组，每个链接都会指向首页并带上标签参数--}}
                                {{--join — 别名 implode() 将tag标签数组连接成字符串 用，隔开--}}
                                {!! join(', ', $post->tagLinks()) !!}]
                            @endif
                        </p>
                    </div>
                    <hr>
                @endforeach

                {{-- 分页 --}}
                <ul class="pager">

                    {{-- Reverse direction 按照发布时间升序排序--}}
                    @if ($reverse_direction)
                        {{--有tag标签进行文章的过滤筛选--}}
                        @if ($posts->currentPage() > 1)
                            {{--生成上一页的链接--}}
                            <li class="previous">
                                <a href="{!! $posts->url($posts->currentPage() - 1) !!}">
                                    <i class="fa fa-long-arrow-left fa-lg"></i>
                                    Previous {{ $tag->tag }} Posts
                                </a>
                            </li>
                        @endif
                        {{--若有多页的 生成下一页的链接--}}
                        @if ($posts->hasMorePages())
                            <li class="next">
                                <a href="{!! $posts->nextPageUrl() !!}">
                                    Next {{ $tag->tag }} Posts
                                    <i class="fa fa-long-arrow-right"></i>
                                </a>
                            </li>
                        @endif
                    @else{{--按照发布时间降序排序--}}
                        @if ($posts->currentPage() > 1)
                            {{--生成上一页的链接--}}
                            <li class="previous">
                                <a href="{!! $posts->url($posts->currentPage() - 1) !!}">
                                    <i class="fa fa-long-arrow-left fa-lg"></i>
                                    Newer {{ $tag ? $tag->tag : '' }} Posts
                                </a>
                            </li>
                        @endif
                        @if ($posts->hasMorePages())
                            <li class="next">
                                <a href="{!! $posts->nextPageUrl() !!}">
                                    Older {{ $tag ? $tag->tag : '' }} Posts
                                    <i class="fa fa-long-arrow-right"></i>
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>

        </div>
    </div>
@stop