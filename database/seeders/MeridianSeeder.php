<?php

namespace Database\Seeders;

use App\Models\Acupoint\Meridian;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeridianSeeder extends Seeder
{
    /**
     * 填充十四经脉数据
     * 数据来源：renti/public/data/site-data.json -> meridians
     */
    public function run(): void
    {
        // 清空旧数据
        DB::table('meridians')->truncate();

        $meridians = [
            [
                'code' => 'LU',
                'cn' => '手太阴肺经',
                'pinyin' => 'shou tai yin fei jing',
                'type' => 'yin',
                'color' => '#7fbbe8',
                'desc' => '起于中焦，下络大肠，还循胃口，上膈属肺，从肺系横出腋下，沿上肢内侧前缘下行，止于拇指桡侧端（少商）。主治胸肺咽喉疾病及经脉循行部位病症。',
            ],
            [
                'code' => 'LI',
                'cn' => '手阳明大肠经',
                'pinyin' => 'shou yang ming da chang jing',
                'type' => 'yang',
                'color' => '#8a7fb8',
                'desc' => '起于食指桡侧端（商阳），沿上肢外侧前缘上行，经肩、颈、面，止于对侧鼻旁（迎香）。主治头面五官、肠胃及经脉循行部位病症。',
            ],
            [
                'code' => 'ST',
                'cn' => '足阳明胃经',
                'pinyin' => 'zu yang ming wei jing',
                'type' => 'yang',
                'color' => '#c88f6b',
                'desc' => '起于鼻旁，循面、胸腹至下肢外侧前缘，止于足次趾外侧（厉兑）。主治胃肠疾病、头面五官病及经脉循行部位病症。',
            ],
            [
                'code' => 'SP',
                'cn' => '足太阴脾经',
                'pinyin' => 'zu tai yin pi jing',
                'type' => 'yin',
                'color' => '#d6b05c',
                'desc' => '起于足大趾内侧端（隐白），沿下肢内侧前缘、腹胸上行，止于胸胁（大包）。主治脾胃疾病、妇科病及经脉循行部位病症。',
            ],
            [
                'code' => 'HT',
                'cn' => '手少阴心经',
                'pinyin' => 'shou shao yin xin jing',
                'type' => 'yin',
                'color' => '#d84a5a',
                'desc' => '起于心中，下络小肠，上出腋下，沿上肢内侧后缘下行，止于小指桡侧端（少冲）。主治心胸疾病、神志病及经脉循行部位病症。',
            ],
            [
                'code' => 'SI',
                'cn' => '手太阳小肠经',
                'pinyin' => 'shou tai yang xiao chang jing',
                'type' => 'yang',
                'color' => '#b86a9a',
                'desc' => '起于小指尺侧端（少泽），沿上肢外侧后缘上行，经肩胛至面、耳，止于耳前（听宫）。主治头项、耳目、神志及经脉循行部位病症。',
            ],
            [
                'code' => 'BL',
                'cn' => '足太阳膀胱经',
                'pinyin' => 'zu tai yang pang guang jing',
                'type' => 'yang',
                'color' => '#5a9aba',
                'desc' => '起于目内眦（睛明），上额交巅，沿项、背腰臀、下肢后侧下行，止于足小趾外侧（至阴）。主治头项腰背、脏腑病及经脉循行部位病症。',
            ],
            [
                'code' => 'KI',
                'cn' => '足少阴肾经',
                'pinyin' => 'zu shao yin shen jing',
                'type' => 'yin',
                'color' => '#6b6b9a',
                'desc' => '起于足小趾下，斜走足心（涌泉），沿下肢内侧后缘、腹胸上行，止于锁骨下缘（俞府）。主治肾、肺及神志病、妇科病及循行部位病症。',
            ],
            [
                'code' => 'PC',
                'cn' => '手厥阴心包经',
                'pinyin' => 'shou jue yin xin bao jing',
                'type' => 'yin',
                'color' => '#b88f5a',
                'desc' => '起于胸中，属心包络，下膈络三焦，沿上肢内侧中线下行，止于中指端（中冲）。主治心胸、神志病及经脉循行部位病症。',
            ],
            [
                'code' => 'TE',
                'cn' => '手少阳三焦经',
                'pinyin' => 'shou shao yang san jiao jing',
                'type' => 'yang',
                'color' => '#8fb88f',
                'desc' => '起于无名指尺侧端（关冲），沿上肢外侧中线、肩颈至面、耳，止于眉梢（丝竹空）。主治头面耳目、胁肋及经脉循行部位病症。',
            ],
            [
                'code' => 'GB',
                'cn' => '足少阳胆经',
                'pinyin' => 'zu shao yang dan jing',
                'type' => 'yang',
                'color' => '#7cb86b',
                'desc' => '起于目外眦（瞳子髎），绕耳前后，循头侧、胁肋、下肢外侧中线下行，止于足四趾外侧（足窍阴）。主治头面耳目、肝胆及经脉循行部位病症。',
            ],
            [
                'code' => 'LR',
                'cn' => '足厥阴肝经',
                'pinyin' => 'zu jue yin gan jing',
                'type' => 'yin',
                'color' => '#b86b4a',
                'desc' => '起于足大趾外侧端（大敦），沿下肢内侧中线、腹胸上行，止于胁下（期门）。主治肝胆病、妇科病及经脉循行部位病症。',
            ],
            [
                'code' => 'CV',
                'cn' => '任脉',
                'pinyin' => 'ren mai',
                'type' => 'ren',
                'color' => '#d84a5a',
                'desc' => '奇经八脉之一，起于胞中，下出会阴，沿腹胸正中线向上，循咽喉至下唇（承浆）。为「阴脉之海」，总任一身之阴经，主治腹、胸、头面及妇科病。',
            ],
            [
                'code' => 'GV',
                'cn' => '督脉',
                'pinyin' => 'du mai',
                'type' => 'du',
                'color' => '#3f9bff',
                'desc' => '奇经八脉之一，起于胞中，下出会阴，沿脊柱正中线向上，过颈项、巅顶至龈交。为「阳脉之海」，总督一身之阳经，主治腰背、头项、神志病。',
            ],
        ];

        $count = 0;
        foreach ($meridians as $index => $data) {
            $data['sort'] = $index + 1;
            $data['status'] = 1;
            Meridian::create($data);
            $count++;
        }

        $this->command->info("经脉数据填充完成（共 {$count} 条）");
    }
}
