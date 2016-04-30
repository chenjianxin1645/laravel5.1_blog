<?php

namespace App\Services;

use App\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

/*
 * 建一个服务类用于创建和返回 RSS 订阅
 * */
class RssFeed
{
    /**
     * Return the content of the RSS feed
     */
    public function getRSS()
    {
      /*  $rss = $this->buildRssData();
        return $rss;*/

        /*
         * 从缓存获取订阅的数据
         * */
        if (Cache::has('rss-feed')) {
            return Cache::get('rss-feed');
        }

        /*
         * 获取rss data 并将其添加到cache中
         * */
        $rss = $this->buildRssData();
        Cache::add('rss-feed', $rss, 120);

        //直接返回rss data
        return $rss;
    }

    /**
     * Return a string with the feed data
     *
     * @return string
     */
    protected function buildRssData()
    {
        $now = Carbon::now();
        $feed = new Feed();// 建立rss feed对象 获取data
//        return $feed;

        $channel = new Channel(); //建立 channel对象
        $channel
            ->title(config('blog.title'))
            ->description(config('blog.description'))
            ->url(url())
            ->language('en')
            ->copyright('Copyright (c) '.config('blog.author'))
            ->lastBuildDate($now->timestamp)
            ->appendTo($feed);//加入到$feed对象当中

        $posts = Post::where('published_at', '<=', $now)
            ->where('is_draft', 0)
            ->orderBy('published_at', 'desc')
            ->take(config('blog.rss_size'))//允许rss的条目
            ->get();
        foreach ($posts as $post) {
            $item = new Item();//将post添加到Item当中
            $item
                ->title($post->title)
                ->description($post->subtitle)
                ->url($post->url())
                ->pubDate($post->published_at->timestamp)
                ->guid($post->url(), true)
                ->appendTo($channel);//将每条item添加到channel当中
        }

        $feed = (string)$feed; //将其转为String类型

        // Replace a couple items to make the feed more compliant
        /*
         * 替换某些字符
         * */
        $feed = str_replace(
            '<rss version="2.0">',
            '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">',
            $feed
        );

        $feed = str_replace(
            '<channel>',
            '<channel>'."\n".'<atom:link href="'.url('/rss').
            '" rel="self" type="application/rss+xml" />',
            $feed
        );

        //赶回获取的rss feed对象
        return $feed;
    }
}