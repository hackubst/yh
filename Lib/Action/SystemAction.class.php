<?php
// 系统配置 
class SystemAction extends AcpAction {
	/**
	 * 操作日志管理
	 */
	public function mg_logs() {
		$tb_name = trim($this->_post('tb_name'));
		$tb_id = $this->_request('tb_id');
		$op_type = $this->_request('op_type');
		$op_key = trim($this->_request('op_key'));
		$change_key = trim($this->_request('change_key'));
		
		$s_year = $this->_request('s_Year', date('Y'));
		$s_month = $this->_request('s_Month', date('m'));
		$s_day = $this->_request('s_Day', date('d'));
		$s_time = mktime(0, 0, 0, $s_month, $s_day, $s_year);
		
		$e_year = $this->_request('e_Year', date('Y'));
		$e_month = $this->_request('e_Month', date('m'));
		$e_day = $this->_request('e_Day', date('d'));
		$e_time = mktime(23, 59, 59, $e_month, $e_day, $e_year);
		
		$this->assign('s_year', $s_year);
		$this->assign('s_month', $s_month);
		$this->assign('s_day', $s_day);
		$this->assign('s_time', $s_time);
		
		$this->assign('e_year', $e_year);
		$this->assign('e_month', $e_month);
		$this->assign('e_day', $e_day);
		$this->assign('e_time', $e_time);
		
		$this->assign('tb_name', $tb_name);
		$this->assign('tb_id', $tb_id);
		$this->assign('op_type', $op_type);
		$this->assign('op_key', $op_key);
		$this->assign('change_key', $change_key);
		
		$where_str = C('DB_PREFIX') . "logs.op_time >= " . $s_time . " AND " . C('DB_PREFIX') . "logs.op_time <= " . $e_time;
		$where_str .= ($tb_name) ? " AND " . C('DB_PREFIX') . "logs.tb_name = '" . $tb_name . "'" : '';
		$where_str .= ($tb_id) ? " AND " . C('DB_PREFIX') . "logs.tb_id = " . $tb_id : '';
		$where_str .= ($op_type) ? " AND " . C('DB_PREFIX') . "logs.op_type = " . $op_type : '';
		$op_user_id = intval($op_key);
		if ($op_user_id > 0) {
			$where_str .= " AND (" . C('DB_PREFIX') . "logs.op_user_id = " . $op_user_id . " OR tb_op.username = '" . trim($op_key) . "')";
		} else {
			$where_str .= ($op_key) ? " AND tb_op.username = '" . trim($op_key) . "'" : '';
		}
		$change_user_id = intval($change_key);
		if ($change_user_id > 0) {
			$where_str .= " AND (" . C('DB_PREFIX') . "logs.change_user_id = " . $change_user_id . " OR tb_change.username = '" . trim($change_key) . "')";
		} else {
			$where_str .= ($change_key) ? " AND tb_change.username = '" . trim($change_key) . "'" : '';
		}
		
		$Logs = M('Logs');
		$total_item = $Logs->join(' ' . C('DB_PREFIX') . 'users tb_op ON tb_op.user_id = ' . C('DB_PREFIX') . 'logs.op_user_id')->join(' ' . C('DB_PREFIX') . 'users tb_change ON tb_change.user_id = ' . C('DB_PREFIX') . 'logs.change_user_id')->where($where_str)->count();

		$per_page_num = C('PER_PAGE_NUM'); //每页显示记录数
		$page = $this->_request('p', '', 1);
		$page = ($page < 1) ? 1 : $page;
		$page = ($page > ceil($total_item / $per_page_num)) ? ceil($total_item / $per_page_num) : $page;
		
		$m_list = $Logs->join(' ' . C('DB_PREFIX') . 'users tb_op ON tb_op.user_id = ' . C('DB_PREFIX') . 'logs.op_user_id')->join(' ' . C('DB_PREFIX') . 'users tb_change ON tb_change.user_id = ' . C('DB_PREFIX') . 'logs.change_user_id')->where($where_str)->order(C('DB_PREFIX') . 'logs.op_time desc')->page($page . ',' . $per_page_num)->field(C('DB_PREFIX') . 'logs.*, tb_op.username AS op_username, tb_change.username AS change_username, tb_change.linkman AS change_linkman')->select();
		$this->assign('m_list', $m_list);

		//分页
		import('ORG.Util.Page');// 导入分页类
		$Page = new Page($total_item, $per_page_num);// 实例化分页类 传入总记录数和每页显示的记录数
		$page_str = $Page->show();// 分页显示输出
		$this->assign('page_str', $page_str);
		
		//TITLE中的页面标题
		$this->assign('head_title', '操作日志管理');
		$this->display();
	}
	
	/**
	 * 修改系统配置
	 */
	public function edit_system_config() {
		$act = $this->_post('act');
		
		if ($act == 'submit') {
		//配置表的配置名称
			$Config = M('Config');
			$c_name = $Config->getField('config_name', true);
			
			foreach ($c_name as $k => $v) {
				$$v = $this->_post($v);
				
				if (!$$v) {
					$this->ajaxReturn($v, '请填写完整', 0);
				}
			}
			
			foreach ($c_name as $k => $v) {
				$Config->where("config_name = '" . $v . "'")->save(array('config_value' => $$v));
			}
			
			//清除缓存
			clear_cache(1);
			clear_cache(2);
			clear_cache(4);
			
			$this->save_logs(CL_OP_TYPE_EDIT, '修改系统配置', C('DB_PREFIX') . 'config', 0, $this->login_user['user_id']);
			$this->ajaxReturn(U('/system/edit_system_config'), '系统配置修改成功！', 1);
		}
		
		//TITLE中的页面标题
		$this->assign('head_title', '修改系统配置');
		$this->display();
	}
}
