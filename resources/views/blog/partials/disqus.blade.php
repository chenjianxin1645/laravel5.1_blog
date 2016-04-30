{{--创建 Disqus 评论局部视图--}}
<div id="disqus_thread"></div>
<script>
    /**
     * 将 Disqus 中的通用评论代码拷贝过来，并取消 disqus_config 变量的注释，
     * 然后修改 this.page.url 和 this.page.identifier 的值。
     */
     var disqus_config = function () {
        //我们将传递到视图的 $slug 变量作为 Disqus 的标识符以便于聚合该文章下的所有评论。
     this.page.url = 'http://laravel51_blog.com/blog/{{ $slug }}'; // Replace PAGE_URL with your page's canonical URL variable
     this.page.identifier = 'blog-{{ $slug }}'; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
     };

    (function () { // DON'T EDIT BELOW THIS LINE
        var d = document, s = d.createElement('script');

        s.src = '//zs-cjx-blog.disqus.com/embed.js';

        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
</script>
<noscript>Please enable JavaScript to view the
    <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a>
</noscript>