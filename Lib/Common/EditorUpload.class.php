<?php
class EditorUpload
{
	private $_imageDomain = '';
	
	private $_rootPath = '';
	
	private $_rootDir = '';
	
	private $_type = 'image';
	
	private $_maxSize = 9000000;
	
	private $_allowExts = array(
		'image' => array('gif', 'jpg', 'jpeg', 'png')
	);
	
	
	public function __construct(array $conf)
	{
		if(!$conf['imageDomain'])
		{
			return false;
		}
		if(!$conf['rootPath'])
		{
			return false;
		}
		if(!$conf['rootDir'])
		{
			return false;
		}
		$this->_imageDomain = $conf['imageDomain'];
		$this->_rootPath = $conf['rootPath'];
		$this->_rootDir = $conf['rootDir'];
		
		if(isset($conf['type']))
		{
			$this->_type = $conf['type'];
		}
		
		if(isset($conf['allowExts']))
		{
			$this->_allowExts = $conf['allowExts'];
		}
		
		if(isset($conf['maxSize']))
		{
			$this->_maxSize = $conf['maxSize'];
		}
	}
	
	public function save($file)
	{
		//PHP上传失败
		if(!empty($file['error']))
		{
			switch($file['error'])
			{
				case '1':
					$error = '超过php.ini允许的大小！';
					break;
				case '2':
					$error = '超过表单允许的大小！';
					break;
				case '3':
					$error = '图片只有部分被上传！';
					break;
				case '4':
					$error = '请选择图片！';
					break;
				case '6':
					$error = '找不到临时目录！';
					break;
				case '7':
					$error = '写文件到硬盘出错！';
					break;
				case '8':
					$error = 'File upload stopped by extension！';
					break;
				case '999':
				default:
					$error = '未知错误！';
			}
			self::_error($error);
		}
		
		$savePath = $this->_rootPath . '/' . $this->_type . '/' . $this->_rootDir . '/' . date('Y-m');
		//有上传文件时
		if(!empty($file))
		{
			//原文件名
			$fileName = $file['name'];
			
			//服务器上临时文件名
			$tmpFile = $file['tmp_name'];
			
			//文件大小
			$fileSize = $file['size'];
			
			//检查文件名
			if (!$fileName) {
				self::_error("请选择文件！");
			}
			
			//检查目录
		//	if (@is_dir($savePath) === false) {
		//		self::_error("上传目录不存在！");
		//	}
			
			//检查目录写权限
		//	if (@is_writable($savePath) === false) {
		//		self::_error("上传目录没有写权限！");
		//	}
			
			//检查是否已上传
			if (@is_uploaded_file($tmpFile) === false) {
				self::_error("上传失败！");
			}
			
			//检查文件大小
			if ($fileSize > $this->_maxSize) {
				self::_error("上传文件过大，请上传" . floor($this->_maxSize / pow(1024, 2)) . "MB以内大小文件！");
			}
			
			//检查目录名
			if (empty($this->_allowExts[trim($this->_type)])) {
				self::_error("目录名不正确！");
			}
			
			//获得文件扩展名
			$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
			
			//检查扩展名
			if (in_array($fileExt, $this->_allowExts[$this->_type]) === false) {
				self::_error("上传文件扩展名是不允许的扩展名！\n只允许" . implode(",", $this->_allowExts[$this->_type]) . "格式。");
			}
			
			if(!is_dir($savePath))
			{
				if(!@mkdir($savePath, 0700, true))
				{
					self::_error("上传目录创建失败！");
				}
			}
			
			//移动文件
			$saveFile = $savePath . '/' . date("YmdHis") . '_' . rand(10000, 99999) . '.' . $fileExt;
			if (move_uploaded_file($tmpFile, $saveFile) === false) {
				self::_error("上传文件失败！");
			}
		
			//$fileUrl = str_replace($this->_rootPath, $this->_imageDomain . '/Uploads', $saveFile);
			$fileUrl = str_replace($this->_rootPath,  '/Uploads', $saveFile);

			self::_success($fileUrl);
		}
	}
	
	private static function _error($msg)
	{
		echo json_encode(array('error' => 1, 'message' => $msg));
		exit;
	}
	
	private static function _success($url)
	{

		echo json_encode(array('error' => 0, 'url' => $url));
		exit;
	}
}
