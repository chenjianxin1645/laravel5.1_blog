<?php

namespace App\Services;

use App\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/*
 * 为博客生成站点地图以利于SEO。实现思路和 RSS 订阅一样：
 * */
class SiteMap
{
    /**
     * Return the content of the Site Map
     */
    public function getSiteMap()
    {
        /*
         * 首先从cache当中获取 因为数据不经常更新
         * */
        if (Cache::has('sitemap')) {
//            return 'sadasd';
            return Cache::get('sitemap');
        }

        /*
         * 生成sute map 并添加到cache
         * */
        $siteMap = $this->buildSiteMap();
        Cache::add('sitemap', $siteMap, 120);
        return $siteMap;
    }

    /**
     * Build the Site Map
     */
    protected function buildSiteMap()
    {
        //获取文章的相关信息
        $postsInfo = $this->getPostsInfo();
//        dd($postsInfo);
        // 只获取数组的值
        $dates = array_values($postsInfo); // updated_at
//        $dates1 = array_keys($postsInfo);
//        dd($dates);
        //进行自然排序
        sort($dates);
        //last 函数返回指定数组的最后一个元素
        $lastmod = last($dates);
//        dd($lastmod);
        //格式化url
        $url = trim(url(), '/') . '/';

        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?'.'>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $xml[] = '  <url>';
        $xml[] = "    <loc>$url</loc>";
        $xml[] = "    <lastmod>$lastmod</lastmod>";
        $xml[] = '    <changefreq>daily</changefreq>';
        $xml[] = '    <priority>0.8</priority>';
        $xml[] = '  </url>';

        foreach ($postsInfo as $slug => $lastmod) {
            $xml[] = '  <url>';
            $xml[] = "    <loc>{$url}blog/$slug</loc>";
            $xml[] = "    <lastmod>$lastmod</lastmod>";
            $xml[] = "  </url>";
        }

        $xml[] = '</urlset>';

        return join("\n", $xml);
    }

    /**
     * Return all the posts as $url => $date
     */
    protected function getPostsInfo()
    {
        /*
         * 返回所有的posts
         * */
        return Post::where('published_at', '<=', Carbon::now())
            ->where('is_draft', 0)
            ->orderBy('published_at', 'desc')
            ->lists('updated_at', 'slug')//返回的数组中指定自定义的键值字段：
            ->all();
    }
}