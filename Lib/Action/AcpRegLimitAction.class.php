<?php

class AcpRegLimitAction extends AcpAction{

	public function __construct()
    {
        parent::_initialize();
    }


    public function reg_limit(){

    	$act = I('act');
    	if ($act == 'save') //提交保存时
        {
            $limit_reg_open         = $this->_post('limit_reg_open');
            $limit_reg_num           = $this->_post('limit_reg_num');
            $limit_reg_desc  = $this->_post('limit_reg_desc');

            $data = array(
                'limit_reg_open'         => $limit_reg_open == 1 ? 1 : 0,
                'limit_reg_num'           => $limit_reg_num ?: 100,
                'limit_reg_desc'  => $limit_reg_desc ?: '项目测试中，注册人数已达上限，无法继续注册！',
            );

            $ConfigBaseModel = new ConfigBaseModel();
            $ConfigBaseModel->setConfigs($data);
            $this->success('恭喜你，参数设置成功了!');
        }

        $config = $this->system_config;
        $this->assign('config', $config);
    	$this->assign('head_title', '注册人数限制');
    	$this->display();
    }

}