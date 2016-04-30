@if(isset($slug) && $slug)
    <hr>
    <div class="container">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
            {{--$slug 变量作为 Disqus 的标识符以便于聚合该文章下的所有评论。--}}
            @include('blog.partials.disqus')
        </div>
    </div>
@endif
<hr>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                {{--添加rss feed链接--}}
                <ul class="list-inline text-center">
                    <li>
                        <a href="{{ url('rss') }}" data-toggle="tooltip"
                           title="RSS feed">
                          <span class="fa-stack fa-lg">
                            <i class="fa fa-circle fa-stack-2x"></i>
                            <i class="fa fa-rss fa-stack-1x fa-inverse"></i>
                          </span>
                        </a>
                    </li>
                </ul>
                <p class="copyright">Copyright © {{ config('blog.author') }}</p>
            </div>
        </div>
    </div>
</footer>