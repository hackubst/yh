<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// |         lanfengye <zibin_5257@163.com>
// +----------------------------------------------------------------------

class PageFront {
    
    // 分页栏每页显示的页数
    public $rollPage = 10;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页URL地址
    public $url     =   '';
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow    ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    protected $config  =    array('prev'=>'上一页','next'=>'下一页','theme'=>'%upPage% %linkPage% %downPage% &nbsp;&nbsp;共%totalPage%页');
    // 默认分页变量名
    protected $varPage;

    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows,$listRows='',$parameter='',$url='') {
        $this->totalRows    =   $totalRows;
        $this->parameter    =   $parameter;
        $this->varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(!empty($listRows)) {
            $this->listRows =   intval($listRows);
        }
        $this->totalPages   =   ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages    =   ceil($this->totalPages/$this->rollPage);
        $this->nowPage      =   !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if($this->nowPage<1){
            $this->nowPage  =   1;
        }elseif(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage  =   $this->totalPages;
        }
        $this->firstRow     =   $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     * 分页显示输出
     * @access public
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$this->url,'',false),$depr).$depr.'__PAGE__';
        }else{
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
            }elseif(empty($this->parameter)){
            	unset($_GET[C('VAR_URL_PARAMS')]);
				$var =  $_GET;
				if(empty($var)) {
					$parameter  =   array();
				}else{
					$parameter  =   $var;
				}
				if ($_POST) {
					foreach ($_POST as $k => $v) {
						$parameter[$k] = $v;
					}
				}
            }
            $parameter[$p]  =   '__PAGE__';
            $url            =   U('',$parameter);
        }
        //上下翻页字符串
        $upRow          =   $this->nowPage-1;
        $downRow        =   $this->nowPage+1;
        if ($upRow>0){
            $upPage     =   "<a href='".str_replace('__PAGE__',$upRow,$url)."' class='prev'>".$this->config['prev']."</a>";
        }else{
            $upPage     =   "<a class='prev disabled' style='cursor: default;'>".$this->config['prev']."</a>";
        }

        if ($downRow <= $this->totalPages){
            $downPage   =   "<a href='".str_replace('__PAGE__',$downRow,$url)."' class='next'>".$this->config['next']."</a>";
        }else{
            $downPage   =   "<a class='next disabled' style='cursor: default;'>".$this->config['next']."</a>";
        }
		
        // 1 2 ... 4 5
        $linkPage = "";
		$stillPage = 2;//固定显示页数，前2页
		$firstRollPage = 5;//第一页显示的分页数
		if($this->totalPages > 1 && $this->totalPages <= $firstRollPage)
		{
			for($i = 1; $i < $firstRollPage; $i++)
			{
				$page = ($nowCoolPage - 1) * $this->rollPage + $i;
				if($page > $this->totalPages)
				{
					break;
				}
				if($page == $this->nowPage)
				{
					$linkPage .= "<a class='cur'>".$page."</a>&nbsp;";
				}
				else
				{
					$linkPage .= "<a href='".str_replace('__PAGE__',$page,$url)."'>".$page."</a>&nbsp;";
				}
			}
		}
		else
		{
			if($this->nowPage <= $firstRollPage)
			{
				for($i = 1; $i < $this->nowPage + $firstRollPage; $i++)
				{
					$page = ($nowCoolPage - 1) * $this->rollPage + $i;
					if($page > $this->totalPages)
					{
						break;
					}
					if($page == $this->nowPage)
					{
						$linkPage .= "<a class='cur'>".$page."</a>&nbsp;";
					}
					else
					{
						$linkPage .= "<a href='".str_replace('__PAGE__',$page,$url)."'>".$page."</a>&nbsp;";
					}
				}
			}
			elseif($this->nowPage > $firstRollPage)
			{
				for($i = 1; $i <= $stillPage; $i++)
				{
					$linkPage .= "<a href='".str_replace('__PAGE__',$i,$url)."'>".$i."</a>&nbsp;";
				}
				$linkPage .= "<span>...</span>&nbsp;";
				for($j = 0; $j < $this->rollPage - $stillPage - 1; $j++)
				{
					$page = $this->nowPage - 2 + $j;
					if($page > $this->totalPages)
					{
						break;
					}
					if($page == $this->nowPage)
					{
						$linkPage .= "<a class='cur'>".$page."</a>&nbsp;";
					}
					else
					{
						$linkPage .= "<a href='".str_replace('__PAGE__',$page,$url)."'>".$page."</a>&nbsp;";
					}
				}
				if($this->nowPage <= $this->totalPages - ($this->rollPage - $stillPage - 3))
				{
					$linkPage .= "<span>...</span>";
				}
			}
		}
        $pageStr = str_replace(
			array(
				'%upPage%',
				'%linkPage%',
				'%downPage%',
				'%totalPage%'
			),
			array(
				$upPage,
				$linkPage,
				$downPage,
				$this->totalPages
			),
			$this->config['theme']
		);
        
		$pageStr = '<form action="" method="get">' . $pageStr . '<span>到</span><input type="text" name="p" id="p" /><span>页</span><button type="submit" style="cursor: pointer;">确定</button><form>';
		return $pageStr;
    }

}