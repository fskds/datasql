<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $settings = [
            // ==================== basic 基础设置 ====================
            ['varname' => 'site_name', 'info' => '网站名称', 'groupid' => 'basic', 'type' => 'text', 'value' => '我的网站'],
            ['varname' => 'site_url', 'info' => '网站域名', 'groupid' => 'basic', 'type' => 'text', 'value' => 'https://www.example.com'],
            ['varname' => 'site_logo', 'info' => '网站 Logo', 'groupid' => 'basic', 'type' => 'image', 'value' => '/storage/images/logo.png'],
            ['varname' => 'site_icp', 'info' => 'ICP 备案号', 'groupid' => 'basic', 'type' => 'text', 'value' => '京ICP备XXXXXXXX号'],
            ['varname' => 'site_copyright', 'info' => '版权信息', 'groupid' => 'basic', 'type' => 'text', 'value' => '© 2024 我的网站 版权所有'],
            ['varname' => 'site_version', 'info' => '版本号', 'groupid' => 'basic', 'type' => 'text', 'value' => '1.0.0'],
            ['varname' => 'site_status', 'info' => '站点状态 1=开启 0=关闭', 'groupid' => 'basic', 'type' => 'select', 'value' => '1'],
            ['varname' => 'site_close_msg', 'info' => '站点关闭提示语', 'groupid' => 'basic', 'type' => 'textarea', 'value' => '网站维护中，请稍后再访问...'],

            // ==================== contact 联系方式 ====================
            ['varname' => 'company', 'info' => '公司名称', 'groupid' => 'contact', 'type' => 'text', 'value' => '北京某某科技有限公司'],
            ['varname' => 'contact_address', 'info' => '联系地址', 'groupid' => 'contact', 'type' => 'text', 'value' => '北京市朝阳区某某街道XX号'],
            ['varname' => 'contact_tel', 'info' => '固定电话', 'groupid' => 'contact', 'type' => 'text', 'value' => '010-12345678'],
            ['varname' => 'contact_mobile', 'info' => '手机号码', 'groupid' => 'contact', 'type' => 'text', 'value' => '13800138000'],
            ['varname' => 'contact_fax', 'info' => '传真号码', 'groupid' => 'contact', 'type' => 'text', 'value' => '010-87654321'],
            ['varname' => 'contact_email', 'info' => '联系邮箱', 'groupid' => 'contact', 'type' => 'text', 'value' => 'info@example.com'],
            ['varname' => 'contact_qq', 'info' => 'QQ 号码', 'groupid' => 'contact', 'type' => 'text', 'value' => '123456789'],
            ['varname' => 'contact_wechat', 'info' => '微信号', 'groupid' => 'contact', 'type' => 'text', 'value' => 'wx_demo'],
            ['varname' => 'contact_weibo', 'info' => '微博地址', 'groupid' => 'contact', 'type' => 'text', 'value' => 'https://weibo.com/demo'],
            ['varname' => 'contact_worktime', 'info' => '工作时间', 'groupid' => 'contact', 'type' => 'text', 'value' => '周一至周五 9:00-18:00'],

            // ==================== seo SEO 设置 ====================
            ['varname' => 'meta_title', 'info' => '页面标题', 'groupid' => 'seo', 'type' => 'text', 'value' => '我的网站 - 首页'],
            ['varname' => 'meta_keywords', 'info' => '关键词', 'groupid' => 'seo', 'type' => 'textarea', 'value' => '网站, 关键词1, 关键词2, 关键词3'],
            ['varname' => 'meta_description', 'info' => '页面描述', 'groupid' => 'seo', 'type' => 'textarea', 'value' => '这是网站的描述信息，用于搜索引擎展示...'],
            ['varname' => 'meta_author', 'info' => '页面作者', 'groupid' => 'seo', 'type' => 'text', 'value' => '网站管理员'],
            ['varname' => 'baidu_api', 'info' => '百度搜索资源平台 API Token', 'groupid' => 'seo', 'type' => 'text', 'value' => ''],
            ['varname' => 'baidu_verify', 'info' => '百度站长验证代码', 'groupid' => 'seo', 'type' => 'text', 'value' => ''],
            ['varname' => 'google_verify', 'info' => 'Google 站长验证代码', 'groupid' => 'seo', 'type' => 'text', 'value' => ''],
            ['varname' => 'bing_verify', 'info' => 'Bing 站长验证代码', 'groupid' => 'seo', 'type' => 'text', 'value' => ''],
            ['varname' => 'analytics_baidu', 'info' => '百度统计代码', 'groupid' => 'seo', 'type' => 'textarea', 'value' => ''],
            ['varname' => 'analytics_google', 'info' => 'Google Analytics ID', 'groupid' => 'seo', 'type' => 'text', 'value' => ''],
            ['varname' => 'seo_robots', 'info' => 'robots.txt 内容', 'groupid' => 'seo', 'type' => 'textarea', 'value' => "User-agent: *\nAllow: /\nDisallow: /admin/"],
            ['varname' => 'seo_sitemap', 'info' => '站点地图地址', 'groupid' => 'seo', 'type' => 'text', 'value' => '/sitemap.xml'],

            // ==================== email 邮箱设置 ====================
            ['varname' => 'email_driver', 'info' => '邮件驱动', 'groupid' => 'email', 'type' => 'select', 'value' => 'smtp'],
            ['varname' => 'email_host', 'info' => 'SMTP 服务器地址', 'groupid' => 'email', 'type' => 'text', 'value' => 'smtp.example.com'],
            ['varname' => 'email_port', 'info' => '端口号', 'groupid' => 'email', 'type' => 'text', 'value' => '465'],
            ['varname' => 'email_encryption', 'info' => '加密方式 ssl/tls/null', 'groupid' => 'email', 'type' => 'select', 'value' => 'ssl'],
            ['varname' => 'email_username', 'info' => '发件邮箱账号', 'groupid' => 'email', 'type' => 'text', 'value' => 'noreply@example.com'],
            ['varname' => 'email_password', 'info' => '发件邮箱授权码/密码', 'groupid' => 'email', 'type' => 'text', 'value' => ''],
            ['varname' => 'email_from_address', 'info' => '发件地址', 'groupid' => 'email', 'type' => 'text', 'value' => 'noreply@example.com'],
            ['varname' => 'email_from_name', 'info' => '发件人名称', 'groupid' => 'email', 'type' => 'text', 'value' => '我的网站'],
            ['varname' => 'email_reply_to', 'info' => '回复邮箱', 'groupid' => 'email', 'type' => 'text', 'value' => 'info@example.com'],

            // ==================== sms 短信设置 ====================
            ['varname' => 'sms_driver', 'info' => '短信服务商', 'groupid' => 'sms', 'type' => 'select', 'value' => 'aliyun'],
            ['varname' => 'sms_status', 'info' => '短信服务开关 1=开启 0=关闭', 'groupid' => 'sms', 'type' => 'select', 'value' => '0'],
            ['varname' => 'sms_aliyun_access_key_id', 'info' => '阿里云 AccessKey ID', 'groupid' => 'sms', 'type' => 'text', 'value' => ''],
            ['varname' => 'sms_aliyun_access_key_secret', 'info' => '阿里云 AccessKey Secret', 'groupid' => 'sms', 'type' => 'text', 'value' => ''],
            ['varname' => 'sms_aliyun_sign_name', 'info' => '阿里云短信签名', 'groupid' => 'sms', 'type' => 'text', 'value' => '某某科技'],
            ['varname' => 'sms_aliyun_template_code', 'info' => '阿里云短信模板CODE', 'groupid' => 'sms', 'type' => 'text', 'value' => 'SMS_123456789'],
            ['varname' => 'sms_qcloud_app_id', 'info' => '腾讯云 AppID', 'groupid' => 'sms', 'type' => 'text', 'value' => ''],
            ['varname' => 'sms_qcloud_app_key', 'info' => '腾讯云 AppKey', 'groupid' => 'sms', 'type' => 'text', 'value' => ''],
            ['varname' => 'sms_qcloud_sign', 'info' => '腾讯云短信签名', 'groupid' => 'sms', 'type' => 'text', 'value' => ''],

            // ==================== upload 上传设置 ====================
            ['varname' => 'upload_disk', 'info' => '存储驱动', 'groupid' => 'upload', 'type' => 'select', 'value' => 'local'],
            ['varname' => 'upload_path', 'info' => '上传目录', 'groupid' => 'upload', 'type' => 'text', 'value' => 'uploads'],
            ['varname' => 'upload_max_size_image', 'info' => '图片最大大小 KB', 'groupid' => 'upload', 'type' => 'text', 'value' => '5120'],
            ['varname' => 'upload_max_size_file', 'info' => '文件最大大小 KB', 'groupid' => 'upload', 'type' => 'text', 'value' => '10240'],
            ['varname' => 'upload_ext_image', 'info' => '允许上传的图片扩展名', 'groupid' => 'upload', 'type' => 'text', 'value' => 'jpg,jpeg,png,gif,webp,bmp'],
            ['varname' => 'upload_ext_file', 'info' => '允许上传的文件扩展名', 'groupid' => 'upload', 'type' => 'text', 'value' => 'doc,docx,xls,xlsx,ppt,pptx,pdf,zip,rar,7z,txt'],
            ['varname' => 'upload_auto_thumb', 'info' => '自动生成缩略图 1=是 0=否', 'groupid' => 'upload', 'type' => 'select', 'value' => '1'],
            ['varname' => 'upload_thumb_width', 'info' => '缩略图最大宽度 px', 'groupid' => 'upload', 'type' => 'text', 'value' => '300'],
            ['varname' => 'upload_thumb_height', 'info' => '缩略图最大高度 px', 'groupid' => 'upload', 'type' => 'text', 'value' => '300'],
            ['varname' => 'upload_thumb_quality', 'info' => '缩略图质量 1-100', 'groupid' => 'upload', 'type' => 'text', 'value' => '85'],

            // ==================== watermark 水印设置 ====================
            ['varname' => 'watermark_enabled', 'info' => '水印开关 1=开启 0=关闭', 'groupid' => 'watermark', 'type' => 'select', 'value' => '0'],
            ['varname' => 'watermark_type', 'info' => '水印类型 text/image', 'groupid' => 'watermark', 'type' => 'select', 'value' => 'text'],
            ['varname' => 'watermark_text', 'info' => '水印文字内容', 'groupid' => 'watermark', 'type' => 'text', 'value' => '© 我的网站'],
            ['varname' => 'watermark_font', 'info' => '水印字体路径', 'groupid' => 'watermark', 'type' => 'text', 'value' => 'C:\Windows\Fonts\msyh.ttc'],
            ['varname' => 'watermark_font_size', 'info' => '水印字号', 'groupid' => 'watermark', 'type' => 'text', 'value' => '20'],
            ['varname' => 'watermark_color', 'info' => '水印颜色 HEX', 'groupid' => 'watermark', 'type' => 'text', 'value' => '#ffffff'],
            ['varname' => 'watermark_opacity', 'info' => '水印透明度 0-100', 'groupid' => 'watermark', 'type' => 'text', 'value' => '50'],
            ['varname' => 'watermark_image', 'info' => '图片水印路径', 'groupid' => 'watermark', 'type' => 'image', 'value' => ''],
            ['varname' => 'watermark_position', 'info' => '水印位置 top-left/top-right/bottom-left/bottom-right/center', 'groupid' => 'watermark', 'type' => 'select', 'value' => 'bottom-right'],
            ['varname' => 'watermark_margin', 'info' => '水印边距 px', 'groupid' => 'watermark', 'type' => 'text', 'value' => '10'],
        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = $now;
            $setting['updated_at'] = $now;
            DB::table('site_infos')->updateOrInsert(
                ['varname' => $setting['varname']],
                $setting
            );
        }

        $this->command->info('Site settings seeded successfully! (total: ' . count($settings) . ')');
    }
}
