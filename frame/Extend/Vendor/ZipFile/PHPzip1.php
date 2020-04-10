<?php
set_time_limit(0);

class PHPzip
{
	
	
	/**
	 * 
	 * //使用
		if(is_dir($csv_dir))
		{
			Vendor('ZipFile.PHPzip');
			$zip = new PHPZip();
			$zip->zip($csv_dir, "bak.zip"); //$csv_dir 带路径
		}
	 * 
	*@desc  生成zip压缩文件的函数
	*
	*@param $dir             string 需要压缩的文件夹名
	*@param $filename     string 压缩后的zip文件名  包括zip后缀
	*@param $missfile      array   不需要的文件
	*@param $fromString  array   自定义压缩文件     
	比如我往里面加一个 内容为 this is my file 的 info.ini  可以这样定义 array(array('info.ini','this is my file'));
	*/
	function zip($dir,$filename,$missfile=array(),$addfromString=array()){
		if(!file_exists($dir) || !is_dir($dir)){
			die(' can not exists dir '.$dir);
		}
		if(strtolower(end(explode('.',$filename))) != 'zip'){
			die('only Support zip files');
		}
		$dir = str_replace('\\','/',$dir);
		$filename = str_replace('\\','/',$filename);
		if(file_exists($filename)){
			die('the zip file '.$filename.' has exists !');
		}
		$files = array();
		$this->getfiles($dir,$files);
		if(empty($files)){
			die(' the dir is empty');
		}
	
		$zip = new ZipArchive;
		$res = $zip->open($filename, ZipArchive::CREATE);
		if ($res === TRUE) {
			foreach($files as $v){
				if(!in_array(str_replace($dir.'/','',$v),$missfile)){
					$zip->addFile($v,str_replace($dir.'/','./',$v));
				}
			}
			if(!empty($addfromString)){
				foreach($addfromString as $v){
					$zip->addFromString($v[0],$v[1]);
				}
			}
			$zip->close();
			echo 'ok';
		} else {
			echo 'failed';
		}
	}
	
	function getfiles($dir,&$files=array()){
		if(!file_exists($dir) || !is_dir($dir)){return;}
		if(substr($dir,-1)=='/'){
			$dir = substr($dir,0,strlen($dir)-1);
		}
		$_files = scandir($dir);
		foreach($_files as $v){
			if($v != '.' && $v!='..'){
				if(is_dir($dir.'/'.$v)){
					getfiles($dir.'/'.$v,$files);
				}
				else
				{
					$files[] = $dir.'/'.$v;
				}
			}
		}
		return $files;
	}
}