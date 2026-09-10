<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ColumnSeeder extends Seeder
{
    /**
     * 填充栏目数据
     */
    public function run(): void
    {
        $now = Carbon::now();

        $columns = [
            [
                'name'        => '首页',
                'slug'        => 'home',
                'description' => '网站首页，展示公司核心业务和最新动态',
                'keywords'    => '首页,公司,企业官网',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 1,
                'status'      => 1,
            ],
            [
                'name'        => '关于我们',
                'slug'        => 'about',
                'description' => '了解公司简介、企业文化、发展历程和团队实力',
                'keywords'    => '关于我们,公司简介,企业文化,发展历程',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 2,
                'status'      => 1,
            ],
            [
                'name'        => '公司简介',
                'slug'        => 'company-intro',
                'description' => '公司基本信息、成立背景、核心优势介绍',
                'keywords'    => '公司简介,企业介绍,核心优势',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 1,
                'status'      => 1,
            ],
            [
                'name'        => '企业文化',
                'slug'        => 'culture',
                'description' => '企业使命、愿景、核心价值观和行为准则',
                'keywords'    => '企业文化,使命,愿景,价值观',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 2,
                'status'      => 1,
            ],
            [
                'name'        => '发展历程',
                'slug'        => 'history',
                'description' => '公司自成立以来的重要里程碑和发展节点',
                'keywords'    => '发展历程,里程碑,大事记',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 3,
                'status'      => 1,
            ],
            [
                'name'        => '产品中心',
                'slug'        => 'products',
                'description' => '展示公司核心产品线和解决方案，帮助客户快速了解产品功能',
                'keywords'    => '产品中心,产品线,解决方案',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 3,
                'status'      => 1,
            ],
            [
                'name'        => '产品A系列',
                'slug'        => 'product-a',
                'description' => '智能数据分析平台、云管理平台、物联网设备管理平台',
                'keywords'    => '数据分析,云管理,物联网,产品A',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 1,
                'status'      => 1,
            ],
            [
                'name'        => '产品B系列',
                'slug'        => 'product-b',
                'description' => '智能客服系统、协同办公套件、低代码开发平台',
                'keywords'    => '智能客服,协同办公,低代码,产品B',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 2,
                'status'      => 1,
            ],
            [
                'name'        => '解决方案',
                'slug'        => 'solutions',
                'description' => '针对不同行业的数字化转型解决方案，助力企业提质增效',
                'keywords'    => '解决方案,数字化转型,行业方案',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 4,
                'status'      => 1,
            ],
            [
                'name'        => '智慧园区',
                'slug'        => 'smart-park',
                'description' => '智慧园区整体解决方案，涵盖安防、停车、能耗、物业管理',
                'keywords'    => '智慧园区,智能安防,智慧停车,能耗管理',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 1,
                'status'      => 1,
            ],
            [
                'name'        => '制造业转型',
                'slug'        => 'manufacturing',
                'description' => '制造业数字化转型方案，覆盖MES、WMS、设备管理全链路',
                'keywords'    => '制造业,数字化转型,MES,WMS,智能制造',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 2,
                'status'      => 1,
            ],
            [
                'name'        => '金融风控',
                'slug'        => 'finance-risk',
                'description' => '金融行业智能风控解决方案，反欺诈、信用评估、合规监测',
                'keywords'    => '金融风控,反欺诈,信用评估,合规监测',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 3,
                'status'      => 1,
            ],
            [
                'name'        => '新闻动态',
                'slug'        => 'news',
                'description' => '公司新闻、行业动态、产品发布等最新资讯',
                'keywords'    => '新闻动态,公司新闻,行业资讯',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 5,
                'status'      => 1,
            ],
            [
                'name'        => '公司新闻',
                'slug'        => 'company-news',
                'description' => '公司最新动态、获奖荣誉、合作签约等新闻资讯',
                'keywords'    => '公司新闻,企业动态,获奖荣誉',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 1,
                'status'      => 1,
            ],
            [
                'name'        => '行业动态',
                'slug'        => 'industry-news',
                'description' => '人工智能、云计算、物联网等行业前沿资讯和趋势分析',
                'keywords'    => '行业动态,AI资讯,云计算,物联网',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 2,
                'status'      => 1,
            ],
            [
                'name'        => '联系我们',
                'slug'        => 'contact',
                'description' => '公司联系方式、商务合作、人才招聘等信息',
                'keywords'    => '联系我们,联系方式,商务合作,招聘',
                'nav_id'      => 1,
                'cover'       => '',
                'sort'        => 6,
                'status'      => 1,
            ],
            // 底部导航
            [
                'name'        => '隐私政策',
                'slug'        => 'privacy',
                'description' => '用户隐私政策说明',
                'keywords'    => '隐私政策,用户隐私',
                'nav_id'      => 2,
                'cover'       => '',
                'sort'        => 1,
                'status'      => 1,
            ],
            [
                'name'        => '服务条款',
                'slug'        => 'terms',
                'description' => '网站服务条款和使用协议',
                'keywords'    => '服务条款,使用协议',
                'nav_id'      => 2,
                'cover'       => '',
                'sort'        => 2,
                'status'      => 1,
            ],
            [
                'name'        => '网站地图',
                'slug'        => 'sitemap',
                'description' => '网站整体结构导航',
                'keywords'    => '网站地图,站点导航',
                'nav_id'      => 2,
                'cover'       => '',
                'sort'        => 3,
                'status'      => 1,
            ],
        ];

        $count = 0;
        foreach ($columns as $column) {
            $column['created_at'] = $now;
            $column['updated_at'] = $now;
            DB::table('content_columns')->insert($column);
            $count++;
        }

        $this->command->info("栏目数据填充完成 (共 {$count} 条)");
    }
}