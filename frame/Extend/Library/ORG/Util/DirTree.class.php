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
class DirTree{
    public $dirarray;
    function __construct($dirpath=".") {
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
        while ($fname = $d->read()) {
            if ($fname == "." || $fname == "..") {
                continue;
            }

            if (is_dir($fname)) {
                $dirarray[] = array($fname, $this->dirscan($fname));
            } else {
                $dirarray[] = $fname;
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
        $str="";
        static $snum=0;
        if(!$arr){
            $arr=$this->dirarray?$this->dirarray:array();
        }
        foreach($arr as $v){
            if(is_array($v)){
                $str.=$this->buildline($snum).$v[0]."<br>";
                $snum++;
                if(is_array($v[1])){
                    $str.=$this->buildtree($v[1]);
                }
            }else{
                $str.=$this->buildline($snum).$v."<br>";
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
    
}

