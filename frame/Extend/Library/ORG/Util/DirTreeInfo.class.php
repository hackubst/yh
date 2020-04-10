<?php
/** 该类实现遍历一个目录及其子目录，得到一个层级数组，以及可生成一个树形字符串以供输出。
 *  @author 23585472@qq.com
 *  @var public $dirarray 遍历得到的层级数组
 *  @method dirscan 遍历一个目录
 *  @method buildtree 生成树形字符串
 *  使用
 *   $dt = new DirTree(APP_PATH . 'Uploads/userWater');//实例化类，并传入目录所在路径
 *   $tree_arr = $dt->dirarray;
 */
class DirTreeInfo{
    public $dirarray;
    public $is_detect = false;
    public $is_replace = false;
    public $replace_path = '';
    function __construct($dirpath=".", $is_detect = false,  $is_replace = false, $replace_path = '') 
    {

        $this->is_detect=$is_detect;//是否检测权限
        $this->is_replace=$is_replace;//是否替换路径
        $this->replace_path=$replace_path;//要替换路径
        $this->dirarray=$this->dirscan($dirpath);//遍历所有目录
    }
    /**
     * 遍历目录
     * @param string $dirname 目录路径
     * @return array 
     */
    function dirscan($dirname = ".") {
        $dirarray = array();
        $d = dir($dirname);
        if (!chdir($dirname)){
            return FALSE;
        }

        $temp = '';
        while ($fname = $d->read()) {
            if ($fname == "." || $fname == "..") {
                continue;
            }

            if (is_dir($fname)) 
            {
                if($this->is_replace)
                {

                    $temp = str_replace($this->replace_path, '', $d->path .'/'. $fname);
                    $dirarray[] = array($temp, $this->dirscan($d->path .'/'.$fname));
                }
                else
                {
                    $dirarray[] = array($d->path .'/'. $fname, $this->dirscan($d->path .'/'.$fname));
                }
            } 
            else 
            {
                
                if($this->is_replace)
                {
                    $temp = str_replace($this->replace_path, '', $d->path .'/'. $fname);
                    $dirarray[] = $temp;
                }
                else
                {
                    $dirarray[] = $d->path .'/'.$fname;
                }
            }
        }
        chdir("..");
        $d->close();
        return $dirarray;
    }
    
    /**
     * 生成树形字符串 
     *   $tree_arr2 = $dt->buildtree();
     *   echo($tree_arr2);
     * 
     */
    function buildtree($arr=array()){
        $str = "";
        static $snum = 0;
        if(!$arr){
            $arr = $this->dirarray?$this->dirarray:array();
        }

        $temp = '';
        foreach($arr as $v)
        {

            if(is_array($v))
            {
                if($this->is_detect)
                {
                    if(is_dir($v[0]))
                    {
                        //dump($v[0]);
                        $temp = $this->getChmod($v[0]);
                       // <p>./data/1 状态：<span class="cur">【正常】</span></p>
                        $temp = ($temp) ? '<span class="cur">正常</span>' : '<span class="hov">异常</span>' ;
                    }
                    else
                    {
                        $temp = '<span style="color:#368ee0">待创建</span>';
                    }
                   
                }
                $str .= "<p>".$this->buildline($snum).iconv('GB2312', 'UTF-8', $v[0])." 【".$temp."】</p>";

                $snum++;

                 //注意空数组的话会造成死循环
                if(is_array($v[1]) && !empty($v[1]))
                {
                   // dump($v[1]);
                    $str.=$this->buildtree($v[1]);
                }
            }
            else
            {

                if($this->is_detect)
                {

                    if(file_exists($v))
                    {
                        $temp = is_writable($v);
                        $temp = ($temp) ? '<span class="cur">正常</span>' : '<span class="hov">异常</span>' ;
                    }
                    else
                    {
                        $temp = '<span style="color:#368ee0">待创建</span>';
                    }
                    
                }
                $str.= "<p>".$this->buildline($snum). iconv('GB2312', 'UTF-8', $v) ." 【".$temp."】</p>";
            }
        }
        $snum=max(0,$snum-1);
        return $str;
    }
    /**
     * 构建分隔符
     * @param int $num 层数 
     * @return str 
     */
    function buildline($num){
        $str="|-";
        for($i=0;$i<$num;$i++){
            $str.="--";
        }
        return $str."|";
    }

    /**
     * 判断文件夹是否也可写权限
     * @param  [str] $filepath 文件夹路径
     * @return [bool] 
     * @author  <[23585472@qq.com]> 
     */
    function getChmod($filepath)
    {
        //dump($filepath);
        if(is_dir($filepath))
        {

            //开始写入测试;
            $file = '_______' . time() . rand() . '_______';
            $file = $filepath .'/'. $file;
            if (file_put_contents($file, '//'))
            {
                unlink($file);//删除测试文件
                return true;
                //return '正常';
            }
            else
            {
                return false;
                //return '不可写';
            }
        }
    }
    
}

