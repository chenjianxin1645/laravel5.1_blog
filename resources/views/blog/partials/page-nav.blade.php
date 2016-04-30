{{-- Navigation blog的导航 --}}
<nav class="navbar navbar-default navbar-custom navbar-fixed-top">
    <div class="container-fluid">
        {{-- Brand and toggle get grouped for better mobile display --}}
        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#navbar-main">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">{{ config('blog.name') }}</a>
        </div>

        {{-- Collect the nav links, forms, and other content for toggling --}}
        <div class="collapse navbar-collapse" id="navbar-main">
            <ul class="nav navbar-nav">
                <li>
                    <a href="/">Home</a>
                </li>
                <li>
                    <a href="/admin">Admin</a>
                </li>
            </ul>
           {{-- <ul class="nav navbar-nav navbar-right list-inline text-center" >
                <li>
                    <a href="{{ url('rss') }}" data-toggle="tooltip"
                       title="RSS feed">
                      <span class="fa-stack fa-lg">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-rss fa-stack-1x fa-inverse"></i>
                      </span>
                    </a>
                </li>
                <li>
                    <a href="/contact">Contact</a>
                </li>
            </ul>--}}
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{{ url('rss') }}" title="RSS feed">Rss-Feed</a>
                </li>
                <li>
                    <a href="{{ url('sitemap.xml') }}" title="Site Map">Site-Map</a>
                </li>
                <li>
                    <a href="/contact">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>