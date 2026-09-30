<?php

namespace Database\Seeders;

use App\Models\Acupoint\MeridianLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeridianLineSeeder extends Seeder
{
    /**
     * 填充经脉线数据
     * 数据来源：renti/data-source/site-data.json -> lines（26 条，精确坐标）
     * 注意：本 seeder 需在 MeridianSeeder 之后执行
     */
    public function run(): void
    {
        DB::table('meridian_lines')->truncate();

        $codeToId = DB::table('meridians')->pluck('id', 'code')->all();

        $lines = [
[
    'meridian' => 'CV',
    'side' => 'C',
    'color' => '#d84a5a',
    'obj' => 'female/meshes/C_CV__833.obj',
    'pos' => [0.0185, 120.0473, 3.3975],
  ],
[
    'meridian' => 'GV',
    'side' => 'C',
    'color' => '#3f9bff',
    'obj' => 'female/meshes/C_GV__828.obj',
    'pos' => [0.0712, 131.4701, 0.6875],
  ],
[
    'meridian' => 'BL',
    'side' => 'L',
    'color' => '#5a9aba',
    'obj' => 'female/meshes/L_BL__769.obj',
    'pos' => [-11.6069, 87.4489, -5.8331],
  ],
[
    'meridian' => 'GB',
    'side' => 'L',
    'color' => '#7cb86b',
    'obj' => 'female/meshes/L_GB__808.obj',
    'pos' => [-12.1624, 87.3959, -3.7577],
  ],
[
    'meridian' => 'HT',
    'side' => 'L',
    'color' => '#d84a5a',
    'obj' => 'female/meshes/L_HT__744.obj',
    'pos' => [-23.1577, 110.807, 8.3685],
  ],
[
    'meridian' => 'KI',
    'side' => 'L',
    'color' => '#6b6b9a',
    'obj' => 'female/meshes/L_KI__779.obj',
    'pos' => [-9.3182, 71.6307, -4.8185],
  ],
[
    'meridian' => 'LI',
    'side' => 'L',
    'color' => '#8a7fb8',
    'obj' => 'female/meshes/L_LI__696.obj',
    'pos' => [-19.9118, 122.6685, 7.7326],
  ],
[
    'meridian' => 'LR',
    'side' => 'L',
    'color' => '#b86b4a',
    'obj' => 'female/meshes/L_LR__818.obj',
    'pos' => [-10.3869, 61.5299, -4.3407],
  ],
[
    'meridian' => 'LU',
    'side' => 'L',
    'color' => '#7fbbe8',
    'obj' => 'female/meshes/L_LU__675.obj',
    'pos' => [-26.1968, 119.2565, 9.6088],
  ],
[
    'meridian' => 'PC',
    'side' => 'L',
    'color' => '#b88f5a',
    'obj' => 'female/meshes/L_PC__789.obj',
    'pos' => [-24.3232, 111.92, 11.196],
  ],
[
    'meridian' => 'SI',
    'side' => 'L',
    'color' => '#b86a9a',
    'obj' => 'female/meshes/L_SI__758.obj',
    'pos' => [-17.8711, 122.144, 4.4564],
  ],
[
    'meridian' => 'SP',
    'side' => 'L',
    'color' => '#d6b05c',
    'obj' => 'female/meshes/L_SP__729.obj',
    'pos' => [-10.7032, 70.3608, -4.7996],
  ],
[
    'meridian' => 'ST',
    'side' => 'L',
    'color' => '#c88f6b',
    'obj' => 'female/meshes/L_ST__713.obj',
    'pos' => [-10.3538, 85.3074, -1.6953],
  ],
[
    'meridian' => 'TE',
    'side' => 'L',
    'color' => '#8fb88f',
    'obj' => 'female/meshes/L_TE__798.obj',
    'pos' => [-19.7676, 123.4563, 5.7843],
  ],
[
    'meridian' => 'BL',
    'side' => 'R',
    'color' => '#5a9aba',
    'obj' => 'female/meshes/R_BL__774.obj',
    'pos' => [10.0723, 87.6122, 6.6331],
  ],
[
    'meridian' => 'GB',
    'side' => 'R',
    'color' => '#7cb86b',
    'obj' => 'female/meshes/R_GB__813.obj',
    'pos' => [10.6234, 87.6288, 9.6747],
  ],
[
    'meridian' => 'HT',
    'side' => 'R',
    'color' => '#d84a5a',
    'obj' => 'female/meshes/R_HT__751.obj',
    'pos' => [22.1206, 109.8858, -10.8207],
  ],
[
    'meridian' => 'KI',
    'side' => 'R',
    'color' => '#6b6b9a',
    'obj' => 'female/meshes/R_KI__784.obj',
    'pos' => [7.9019, 71.7314, 9.6034],
  ],
[
    'meridian' => 'LI',
    'side' => 'R',
    'color' => '#8a7fb8',
    'obj' => 'female/meshes/R_LI__705.obj',
    'pos' => [18.9202, 120.9305, -4.7075],
  ],
[
    'meridian' => 'LR',
    'side' => 'R',
    'color' => '#b86b4a',
    'obj' => 'female/meshes/R_LR__823.obj',
    'pos' => [8.6388, 61.8999, 15.5369],
  ],
[
    'meridian' => 'LU',
    'side' => 'R',
    'color' => '#7fbbe8',
    'obj' => 'female/meshes/R_LU__686.obj',
    'pos' => [25.3413, 116.6436, -6.9323],
  ],
[
    'meridian' => 'PC',
    'side' => 'R',
    'color' => '#b88f5a',
    'obj' => 'female/meshes/R_PC__671.obj',
    'pos' => [23.5816, 110.1233, -5.5576],
  ],
[
    'meridian' => 'SI',
    'side' => 'R',
    'color' => '#b86a9a',
    'obj' => 'female/meshes/R_SI__764.obj',
    'pos' => [16.701, 121.1519, -6.3751],
  ],
[
    'meridian' => 'SP',
    'side' => 'R',
    'color' => '#d6b05c',
    'obj' => 'female/meshes/R_SP__737.obj',
    'pos' => [9.459, 70.724, 15.2185],
  ],
[
    'meridian' => 'ST',
    'side' => 'R',
    'color' => '#c88f6b',
    'obj' => 'female/meshes/R_ST__721.obj',
    'pos' => [8.6668, 85.6392, 14.6411],
  ],
[
    'meridian' => 'TE',
    'side' => 'R',
    'color' => '#8fb88f',
    'obj' => 'female/meshes/R_TE__803.obj',
    'pos' => [18.6542, 122.1859, -6.6564],
  ]
        ];

        $count = 0;
        foreach ($lines as $index => $data) {
            $code = $data['meridian'];
            if (!isset($codeToId[$code])) {
                $this->command->warn("经脉代码 {$code} 未找到，跳过该经脉线");
                continue;
            }
            MeridianLine::create([
                'meridian_id' => $codeToId[$code],
                'meridian_code' => $code,
                'side' => $data['side'],
                'color' => $data['color'],
                'obj' => $data['obj'],
                'pos_x' => $data['pos'][0],
                'pos_y' => $data['pos'][1],
                'pos_z' => $data['pos'][2],
                'sort' => $index + 1,
                'status' => 1,
            ]);
            $count++;
        }

        $this->command->info("经脉线数据填充完成（共 {$count} 条）");
    }
}
