<?php
class UploadImageWidget extends Widget
{

    /**
     * 渲染输出 render方法是Widget唯一的接口
     * 使用字符串返回 不能有任何输出
     * @access public
     * @param mixed $data  要渲染的数据
     * @return string
     */
    public function render($data=array())
    {

        // 标记是否载入图片上传js
        defined('UPLOAD_IMAGE_SCRIPT') or define('UPLOAD_IMAGE_SCRIPT', true);

        // 默认配置
        $default = array(
            'batch'   => false, // false表示单图，true表示多图
            'must'    => true, // 是否显示一个红色*
            'name'    => 'pic', // 表单名称（多图表单不要加上[]）
            'url'     => '', // 单图的链接（不填则为空）
            'pic_arr' => array(), // 多图的链接（不填则为空）
            'title'   => '图片', // 标题
            'help'    => '暂时只支持上传2M以内JPG,JPEG,PNG,GIF格式图片', // 提示信息
            'warn'    => false, // 帮助信息是否显示为红色字体
            'dir'     => '', // 上传子目录，为空则取默认配置
            'module'  => defined('MODULE_NAME') ? MODULE_NAME : 'Global', // 默认用GlobalAction下的上传方法
        );

        // 合并参数
        $data = array_merge($default, $data);

        // 单图模版 or 多图模版
        $template = $data['batch'] ? 'batch' : 'single';

        return $this->renderFile($template, $data);
    }

}
