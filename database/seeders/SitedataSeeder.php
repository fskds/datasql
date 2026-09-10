<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SitedataSeeder extends Seeder
{
    /**
     * 填充站点数据菜单相关表：栏目分类、导航、标签
     */
    public function run(): void
    {
        $now = Carbon::now();

        // ==================== 栏目分类 ====================
        $categories = [
            ['name' => '新闻资讯', 'slug' => 'news', 'description' => '公司新闻与行业动态', 'pId' => 0, 'imageUrl' => '', 'sort' => 1, 'status' => 1],
            ['name' => '产品中心', 'slug' => 'products', 'description' => '产品展示与介绍', 'pId' => 0, 'imageUrl' => '', 'sort' => 2, 'status' => 1],
            ['name' => '解决方案', 'slug' => 'solutions', 'description' => '行业解决方案', 'pId' => 0, 'imageUrl' => '', 'sort' => 3, 'status' => 1],
            ['name' => '关于我们', 'slug' => 'about', 'description' => '公司介绍与文化', 'pId' => 0, 'imageUrl' => '', 'sort' => 4, 'status' => 1],
            ['name' => '联系我们', 'slug' => 'contact', 'description' => '联系方式与地图', 'pId' => 0, 'imageUrl' => '', 'sort' => 5, 'status' => 1],
            // 子分类
            ['name' => '公司新闻', 'slug' => 'company-news', 'description' => '公司内部新闻', 'pId' => 1, 'imageUrl' => '', 'sort' => 1, 'status' => 1],
            ['name' => '行业动态', 'slug' => 'industry-news', 'description' => '行业前沿资讯', 'pId' => 1, 'imageUrl' => '', 'sort' => 2, 'status' => 1],
            ['name' => '产品A系列', 'slug' => 'product-a', 'description' => 'A系列产品', 'pId' => 2, 'imageUrl' => '', 'sort' => 1, 'status' => 1],
            ['name' => '产品B系列', 'slug' => 'product-b', 'description' => 'B系列产品', 'pId' => 2, 'imageUrl' => '', 'sort' => 2, 'status' => 1],
        ];

        $categoryIds = [];
        foreach ($categories as $cat) {
            $cat['created_at'] = $now;
            $cat['updated_at'] = $now;
            $id = DB::table('content_categories')->insertGetId($cat);
            $categoryIds[$cat['slug']] = $id;
        }
        $this->command->info('栏目分类数据填充完成 (共 ' . count($categories) . ' 条)');

        // ==================== 导航 ====================
        $navs = [
            ['name' => '首页', 'pId' => 0, 'slug' => 'home', 'groupId' => 1, 'sort' => 1, 'status' => 1],
            ['name' => '新闻资讯', 'pId' => 0, 'slug' => 'news', 'groupId' => 1, 'sort' => 2, 'status' => 1],
            ['name' => '产品中心', 'pId' => 0, 'slug' => 'products', 'groupId' => 1, 'sort' => 3, 'status' => 1],
            ['name' => '解决方案', 'pId' => 0, 'slug' => 'solutions', 'groupId' => 1, 'sort' => 4, 'status' => 1],
            ['name' => '关于我们', 'pId' => 0, 'slug' => 'about', 'groupId' => 1, 'sort' => 5, 'status' => 1],
            ['name' => '联系我们', 'pId' => 0, 'slug' => 'contact', 'groupId' => 1, 'sort' => 6, 'status' => 1],
            // 子导航
            ['name' => '公司新闻', 'pId' => 2, 'slug' => 'company-news', 'groupId' => 1, 'sort' => 1, 'status' => 1],
            ['name' => '行业动态', 'pId' => 2, 'slug' => 'industry-news', 'groupId' => 1, 'sort' => 2, 'status' => 1],
            ['name' => '产品A系列', 'pId' => 3, 'slug' => 'product-a', 'groupId' => 1, 'sort' => 1, 'status' => 1],
            ['name' => '产品B系列', 'pId' => 3, 'slug' => 'product-b', 'groupId' => 1, 'sort' => 2, 'status' => 1],
            ['name' => '公司简介', 'pId' => 5, 'slug' => 'company-intro', 'groupId' => 1, 'sort' => 1, 'status' => 1],
            ['name' => '企业文化', 'pId' => 5, 'slug' => 'culture', 'groupId' => 1, 'sort' => 2, 'status' => 1],
            // 底部导航组
            ['name' => '友情链接', 'pId' => 0, 'slug' => 'links', 'groupId' => 2, 'sort' => 1, 'status' => 1],
            ['name' => '隐私政策', 'pId' => 0, 'slug' => 'privacy', 'groupId' => 2, 'sort' => 2, 'status' => 1],
            ['name' => '服务条款', 'pId' => 0, 'slug' => 'terms', 'groupId' => 2, 'sort' => 3, 'status' => 1],
        ];

        foreach ($navs as $nav) {
            $nav['created_at'] = $now;
            $nav['updated_at'] = $now;
            DB::table('content_navs')->insert($nav);
        }
        $this->command->info('导航数据填充完成 (共 ' . count($navs) . ' 条)');

        // ==================== 标签 ====================
        $tags = [
            ['name' => '热门', 'slug' => 'hot', 'description' => '热门内容标签', 'sort' => 1, 'status' => 1],
            ['name' => '推荐', 'slug' => 'recommended', 'description' => '推荐内容标签', 'sort' => 2, 'status' => 1],
            ['name' => '最新', 'slug' => 'latest', 'description' => '最新内容标签', 'sort' => 3, 'status' => 1],
            ['name' => '技术', 'slug' => 'tech', 'description' => '技术相关标签', 'sort' => 4, 'status' => 1],
            ['name' => '产品', 'slug' => 'product', 'description' => '产品相关标签', 'sort' => 5, 'status' => 1],
            ['name' => '服务', 'slug' => 'service', 'description' => '服务相关标签', 'sort' => 6, 'status' => 1],
            ['name' => '案例', 'slug' => 'case', 'description' => '案例分享标签', 'sort' => 7, 'status' => 1],
            ['name' => '活动', 'slug' => 'event', 'description' => '活动通知标签', 'sort' => 8, 'status' => 1],
        ];

        foreach ($tags as $tag) {
            $tag['created_at'] = $now;
            $tag['updated_at'] = $now;
            DB::table('content_tags')->insert($tag);
        }
        $this->command->info('标签数据填充完成 (共 ' . count($tags) . ' 条)');

        $this->command->info('SitedataSeeder 全部完成！');
    }
}