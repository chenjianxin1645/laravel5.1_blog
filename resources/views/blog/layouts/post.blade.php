{{--{{dd($post)}}--}}
@extends('blog.layouts.master', [
  'title' => $post->title,
  'meta_description' => $post->meta_description ?: config('blog.description'),
])

@section('page-header')
    <header class="intro-header"
            {{--当前的url地址为http://laravel51_blog.com/blog/ 所以要返回上一级目录下--}}
            style="background-image: url('../{{ page_image($post->page_image) }}')">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <div class="post-heading">
                        <h1>{{ $post->title }}</h1>
                        <h2 class="subheading">{{ $post->subtitle }}</h2>
            <span class="meta">
              Posted on {{ $post->published_at->format('F j, Y') }}
                @if ($post->tags->count())
                    &nbsp;&nbsp;tag in[{{--获取该文章的所有的tag标签--}}
                    {!! join(', ', $post->tagLinks()) !!}]
                @endif
            </span>
                    </div>
                </div>
            </div>
        </div>
    </header>
@stop

@section('content')

    {{-- The Post --}}
    <article>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    {{--这里不显示文章的初始文本 显示已经经过markdown格式过的html文本--}}
                    {!! $post->content_html !!}
                </div>
            </div>

            {{--<!-- JiaThis Button BEGIN -->--}}
            <div class="col-lg-offset-2"> JiaThis 分享
                <div class="jiathis_style ">
                    <a class="jiathis_button_qzone"></a>
                    <a class="jiathis_button_tsina"></a>
                    <a class="jiathis_button_tqq"></a>
                    <a class="jiathis_button_weixin"></a>
                    <a class="jiathis_button_renren"></a>
                    <a href="http://www.jiathis.com/share" class="jiathis jiathis_txt jtico jtico_jiathis" target="_blank"></a>
                    <a class="jiathis_counter_style"></a>
                </div>
                <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script>
            </div>
            {{--<!-- JiaThis Button END -->--}}

            <div class="bdsharebuttonbox col-lg-offset-8">百度分享
                <a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_more" data-cmd="more"></a></div>
            <script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script>
        </div>
    </article>

    {{-- The Pager 分页列表 --}}
    <div class="container">
        <div class="row">
            <ul class="pager">
                {{--文章的标签存在且该标签是按发布时间的升序排序的--}}
                @if ($tag && $tag->reverse_direction)
                    @if ($post->olderPost($tag))
                        {{--获取该标签上一篇文章的--}}
                        <li class="previous">
                            {{--生成上一篇文章的url链接--}}
                            <a href="{!! $post->olderPost($tag)->url($tag) !!}">
                                <i class="fa fa-long-arrow-left fa-lg"></i>
                                Previous {{ $tag->tag }} Post
                            </a>
                        </li>
                    @endif
                    @if ($post->newerPost($tag))
                        {{--获取该标签的下一篇文章--}}
                        <li class="next">
                            {{--生成下一篇文章的url链接--}}
                            <a href="{!! $post->newerPost($tag)->url($tag) !!}">
                                Next {{ $tag->tag }} Post
                                <i class="fa fa-long-arrow-right"></i>
                            </a>
                        </li>
                    @endif
                @else
                    {{--文章的标签存在但该标签是按发布时间是按照默认降序排序的--}}
                    {{--文章的标签是不存在的且文章的发布时间也是按照降序排序的--}}
                    @if ($post->newerPost($tag))
                        <li class="previous">
                            {{--生成下一篇文章的url链接--}}
                            <a href="{!! $post->newerPost($tag)->url($tag) !!}">
                                <i class="fa fa-long-arrow-left fa-lg"></i>
                                Previous Newer {{ $tag ? $tag->tag : '' }} Post
                            </a>
                        </li>
                    @endif
                    @if ($post->olderPost($tag))
                        <li class="next">
                            <a href="{!! $post->olderPost($tag)->url($tag) !!}">
                                Next Older {{ $tag ? $tag->tag : '' }} Post
                                <i class="fa fa-long-arrow-right"></i>
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
        </div>

    </div>
@stop