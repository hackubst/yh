<?php
//微信消息群发模型
class WxMassMsgModel extends Model{

	//添加消息
	public function addWxMassMsg($arr){
		if(!is_array($arr)) return false;
		//已存在则先删除再添加
		if($this->getWxMassMsg()){
			$this->where('1')->delete(); 
		}
		return $this->add($arr);
	}

	//获取消息信息
	public function getWxMassMsg(){
		return $this->find();
	}
}