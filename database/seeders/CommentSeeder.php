<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommentSeeder extends Seeder
{
    /**
     * 填充评论数据
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 获取文章列表，按分类分组
        $articles = DB::table('content_articles')->select('id', 'title', 'category_id')->get();
        if ($articles->isEmpty()) {
            $this->command->warn('请先运行 ArticleSeeder 生成文章数据');
            return;
        }

        $articleIds = $articles->pluck('id')->toArray();

        $comments = [
            // ========== 智能数据分析平台V3.0 (article 对应 category_id=8) ==========
            [
                'article_id'  => $articleIds[8] ?? 1,
                'content'     => 'V3.0的AI智能洞察功能太强了，我们公司用了之后数据分析效率提升了不少！',
                'author_name' => '张经理',
                'author_email'=> 'zhang@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[8] ?? 1,
                'content'     => '自然语言交互确实方便了很多，业务人员不用学SQL也能做分析了。',
                'author_name' => '李分析师',
                'author_email'=> 'li@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[8] ?? 1,
                'content'     => '数据处理速度提升300%是真的吗？我们这边数据量比较大，想了解一下。',
                'author_name' => '王工',
                'author_email'=> 'wang@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[8] ?? 1,
                'content'     => '回复王工：是的，我们实测在PB级数据场景下性能提升明显，可以联系销售获取测试报告。',
                'author_name' => '产品经理',
                'author_email'=> 'pm@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[8] ?? 1,
                'content'     => '价格方面有没有优惠？我们公司预算有限。',
                'author_name' => '陈总',
                'author_email'=> 'chen@example.com',
                'parent_id'   => 0,
                'status'      => 0,
            ],

            // ========== 2026年AI行业趋势 (article 对应 category_id=7) ==========
            [
                'article_id'  => $articleIds[4] ?? 2,
                'content'     => '分析得很全面，尤其是AI Agent部分，这确实是2026年的重点方向。',
                'author_name' => '赵博士',
                'author_email'=> 'zhao@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[4] ?? 2,
                'content'     => '边缘AI计算这块我们也在关注，感觉是下一个爆发点。',
                'author_name' => '孙研究员',
                'author_email'=> 'sun@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[4] ?? 2,
                'content'     => 'AI安全治理确实很重要，希望有更多相关的深度文章。',
                'author_name' => '周工',
                'author_email'=> 'zhou@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[4] ?? 2,
                'content'     => '5000亿美元的市场规模，这个数字可靠吗？来源是什么？',
                'author_name' => '匿名用户',
                'author_email'=> null,
                'parent_id'   => 0,
                'status'      => 1,
            ],

            // ========== 智能客服系统升级 (article 对应 category_id=9) ==========
            [
                'article_id'  => $articleIds[12] ?? 3,
                'content'     => '多语言翻译功能非常实用，我们做跨境电商的正好需要！',
                'author_name' => '刘总',
                'author_email'=> 'liu@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[12] ?? 3,
                'content'     => '情感分析准确率怎么样？会不会有误判的情况？',
                'author_name' => '黄主管',
                'author_email'=> 'huang@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[12] ?? 3,
                'content'     => '我们公司已经接入了，日均处理量确实很大，系统稳定性不错。',
                'author_name' => '吴工',
                'author_email'=> 'wu@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[12] ?? 3,
                'content'     => '试用了一下，整体体验很好，就是价格有点高。',
                'author_name' => '郑经理',
                'author_email'=> 'zheng@example.com',
                'parent_id'   => 0,
                'status'      => 0,
            ],

            // ========== 智慧园区解决方案 (article 对应 category_id=3) ==========
            [
                'article_id'  => $articleIds[17] ?? 4,
                'content'     => '我们园区正好在考虑智慧化改造，这个方案看起来很适合。',
                'author_name' => '园区管理处',
                'author_email'=> 'park@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[17] ?? 4,
                'content'     => '智能停车系统太重要了，现在园区停车是个大问题。',
                'author_name' => '物业经理',
                'author_email'=> 'wy@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[17] ?? 4,
                'content'     => '能耗管理这块能节省多少成本？有具体数据吗？',
                'author_name' => '财务主管',
                'author_email'=> 'cw@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],

            // ========== 公司荣获创新企业称号 (article 对应 category_id=6) ==========
            [
                'article_id'  => $articleIds[0] ?? 5,
                'content'     => '恭喜恭喜！实至名归！',
                'author_name' => '合作伙伴A',
                'author_email'=> 'partnerA@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[0] ?? 5,
                'content'     => '祝贺！希望贵公司越来越好！',
                'author_name' => '客户代表',
                'author_email'=> 'client@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[0] ?? 5,
                'content'     => '了不起的成就，期待更多创新成果！',
                'author_name' => '行业同仁',
                'author_email'=> 'peer@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],

            // ========== 制造业数字化转型 (article 对应 category_id=3) ==========
            [
                'article_id'  => $articleIds[18] ?? 6,
                'content'     => '生产效率提升25%这个数据很诱人，我们工厂也想试试。',
                'author_name' => '工厂厂长',
                'author_email'=> 'gc@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[18] ?? 6,
                'content'     => 'MES系统对接现有的ERP系统方便吗？有没有实施案例参考？',
                'author_name' => 'IT主管',
                'author_email'=> 'it@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[18] ?? 6,
                'content'     => '预测性维护这块我们很感兴趣，设备故障率降低60%是真实数据吗？',
                'author_name' => '设备主管',
                'author_email'=> 'sb@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],

            // ========== 低代码开发平台 (article 对应 category_id=9) ==========
            [
                'article_id'  => $articleIds[15] ?? 7,
                'content'     => '开发周期从45天缩短到7天，这个效率提升太惊人了。',
                'author_name' => '开发经理',
                'author_email'=> 'dev@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[15] ?? 7,
                'content'     => '我们团队用了低代码平台，确实节省了很多重复开发工作。',
                'author_name' => '前端工程师',
                'author_email'=> 'fe@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[15] ?? 7,
                'content'     => '自定义组件扩展能力怎么样？有些特殊业务场景需要定制。',
                'author_name' => '架构师',
                'author_email'=> 'arch@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],

            // ========== 公司简介/企业文化 ==========
            [
                'article_id'  => $articleIds[19] ?? 8,
                'content'     => '公司文化很棒，在这里工作很开心！',
                'author_name' => '员工A',
                'author_email'=> 'staffA@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[19] ?? 8,
                'content'     => '核心价值观很实在，不是空话。',
                'author_name' => '员工B',
                'author_email'=> 'staffB@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],

            // ========== 金融行业智能风控 ==========
            [
                'article_id'  => $articleIds[19] ?? 9,
                'content'     => '风控方案很专业，我们银行正在评估引入。',
                'author_name' => '银行风控部',
                'author_email'=> 'bank@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[19] ?? 9,
                'content'     => '毫秒级响应速度能满足高频交易场景吗？',
                'author_name' => '量化交易员',
                'author_email'=> 'quant@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[19] ?? 9,
                'content'     => '广告垃圾信息，请删除。',
                'author_name' => null,
                'author_email'=> null,
                'parent_id'   => 0,
                'status'      => 0,
            ],

            // ========== 5G+工业互联网 ==========
            [
                'article_id'  => $articleIds[5] ?? 10,
                'content'     => '5G在工业领域的应用越来越成熟了，我们也在考虑部署。',
                'author_name' => '通信工程师',
                'author_email'=> '5g@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[5] ?? 10,
                'content'     => '机器视觉质检这块能详细讲讲吗？我们工厂正好需要。',
                'author_name' => '质检主管',
                'author_email'=> 'qc@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],

            // ========== 云计算/边缘计算 ==========
            [
                'article_id'  => $articleIds[6] ?? 11,
                'content'     => '云边协同确实是趋势，我们也在做这方面的架构调整。',
                'author_name' => '云架构师',
                'author_email'=> 'cloud@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[6] ?? 11,
                'content'     => '边缘计算在自动驾驶领域应用潜力很大，期待更多案例。',
                'author_name' => '自动驾驶研发',
                'author_email'=> 'ad@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],

            // ========== 企业级云管理平台 ==========
            [
                'article_id'  => $articleIds[9] ?? 12,
                'content'     => '多云管理真的很重要，我们公司用了三个云平台，管理起来很麻烦。',
                'author_name' => '运维总监',
                'author_email'=> 'ops@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[9] ?? 12,
                'content'     => '成本优化功能不错，能详细说说实现原理吗？',
                'author_name' => '财务总监',
                'author_email'=> 'cfo@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],

            // ========== 物联网设备管理 ==========
            [
                'article_id'  => $articleIds[11] ?? 13,
                'content'     => '智慧路灯案例很实用，年省200万电费确实很吸引人。',
                'author_name' => '市政管理',
                'author_email'=> 'gov@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
            [
                'article_id'  => $articleIds[11] ?? 13,
                'content'     => '设备故障率降低60%这个数据很有说服力。',
                'author_name' => '工厂技术员',
                'author_email'=> 'tech@example.com',
                'parent_id'   => 0,
                'status'      => 1,
            ],
        ];

        $count = 0;
        foreach ($comments as $comment) {
            $comment['created_at'] = Carbon::now()->subDays(rand(1, 30));
            $comment['updated_at'] = $comment['created_at'];
            DB::table('content_comments')->insert($comment);
            $count++;
        }

        $this->command->info("评论数据填充完成 (共 {$count} 条)");
    }
}