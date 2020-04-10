<?php

// +-------------------------------------------------------------------------
// | Highcharts.class.php 
// +-------------------------------------------------------------------------
// | 功能: Highcharts 图表驱动 终级版
// +-------------------------------------------------------------------------
// | xia 2013-10-16 15:04 <xiatsing@china.com.cn>
// +-------------------------------------------------------------------------


/**
 * Highcharts 版本 3.06
 * 使用前请先导入jquery驱动
 *
 * $basePath                                图表驱动的根目录
 * $theme                                   图表的主题
 * $container                               显示图表的容器
 * $errorMsg                                错误信息
 * $driveFile                               图表驱动文件
 *
 * Class Highcharts
 */
class Highcharts{

    // Highcharts根目录
    private   $basePath;
    // 主题
    private   $theme;
    // 显示图表的容器

    // 图表驱动的js文件
    private   $driveFile  = 'highcharts.js';
    private   $inputData;
    private   $graphData = array(
                                'credits'   => array(
                                    'text'      => '@yurtree',
                                    'enabled'   =>  false,
                                    'href'      => 'http://www.yurtree.com',
                                    'position'  => array(
                                                    'align' => 'right',
                                                    'y'     => -10
                                                )

                                ),

                                'legend'    => array(
                                    'enabled'   => true
                                ),

                                'xAxis'     => array(
                                    'categories'    => array()
                                ),

                                'title'     => array(
                                    'text'  => ''
                                ),
                                'yAxis'     => array(
                                    'title'         => '',
                                    'allowDecimals' => false
                                ),

                            );

    // 驱动
    private $jsConttent = '
    <script type="text/javascript">
            $(function () {
                    $("#{$container}").highcharts(
                        {$grapData}
                    );
                });
    </script>
';

    public function __construct($_container = 'container', $_grapData = '',  $_theme = '', $_basePath = '/Public/Plugins/Highcharts/'){
        $this->inputData    = $_grapData;
        $this->container    = $_container;
        $this->theme        = $_theme;
        $this->basePath     = $_basePath;
    }

    // 设置container
    public function setContainer($_container){
        $this->container = $_container;
    }

    // 设置theme
    public function setTheme($_theme){
        $this->theme = $_theme;
    }

    // 设置根目录
    public function setBasePath($_basePath){
        $this->basePath = $_basePath;
    }

    // 设置数据
    public function setData($_data){
        $this->inputData = $_data;
    }

    // 设置驱动
    public function setDriveFile($_file){
        $this->driveFile = $_file;
    }

    /**
     * 返回highcharts 图表驱动js代码
     * @param string $driverFile
     * @return string
     */
    public function getDriveJsContent(){
        $_driveFile = $this->basePath . $this->driveFile;
        if($this->driveFile){
            $jsStr = '<script type="text/javascript" ';
            $jsStr.= 'src="' . $_driveFile . '"';
            $jsStr.= '></script>';
            return $jsStr;
        }

        return '';
    }

    /**
     * 主题文件
     * @return string
     */
    public function getThemeContent(){
        $_themeFile = $this->basePath . 'Theme/' . $this->theme . '.js';
        if($this->theme){
            $jsStr = '<script type="text/javascript" ';
            $jsStr.= 'src="' . $_themeFile . '"';
            $jsStr.= '></script>';
            return $jsStr;
        }

        return '';
    }


    /**
     * 组装结果数据
     */
    public function getContent(){

        // 驱动
        $_strJs = $this->getDriveJsContent();
        $_strJs.= "\n";
        // 主题
        $_strJs.= $this->getThemeContent();
        $_strJs.= "\n";
        // 注入数据
        $_strJs.= $this->injectData();
        $_strJs.= "\n";

        return $_strJs;
    }


    /**
     * 注入数据
     */
    public function injectData(){
        // $container
        $this->jsConttent = str_replace('{$container}', $this->container, $this->jsConttent);
        // data
        $this->merge($this->inputData, $this->graphData);
        $this->jsConttent = str_replace('{$grapData}', json_encode($this->graphData), $this->jsConttent);

        return $this->jsConttent;
    }

    /**
     * 递归merge 如果key存在则覆盖如果不存在则添加
     * @param $in
     * @param $input 要改变的数组
     */
    private function merge($in, &$input){
        if(is_array($in)){
            foreach($in as $key => $val){
                if(is_array($val)){
                    if(!isset($input[$key]) || !is_array($input[$key])){
                        $input[$key] = array();
                    }

                    $this->merge($val, $input[$key]);
                }else{
                    $input[$key] = $val;
                }
            }
        }
    }

    public function  __toString (){
        return $this->getContent();
    }
}
