<?php


class McpDepositAction extends McpAction
{
	/**
	 * 构造函数
	 * @return void
	 * @todo
	 */
	private $user_id;

	public function McpDepositAction()
	{
		parent::_initialize();
		$this->user_id = intval(session('user_id'));
	}

	/**
	 * 资金信息
	 * @author chenhuabo
	 * @param void
	 * @return void
	 * @todo 资金信息
	 */
	public function get_fund_info()
	{
		$user_obj = new UserModel($this->user_id);
		$left_money = $user_obj->getLeftMoney();

		$merchant_obj = new MerchantModel();
		$merchant_info = $merchant_obj->getMerchantInfo('merchant_id='.$this->m_id);
		if($merchant_info['is_deposit'] == 0){
			$deposit_num =0;
		}else{
			$config_obj = new ConfigBaseModel();
			$deposit_num = $config_obj->getConfig('deposit_num');
		}
		
		$order_item_obj = new OrderItemModel();
		$frozen_money = $order_item_obj->get_static("tp_order.isuse = 1 AND tp_order.merchant_id=".$this->m_id." AND tp_order.order_status in (1,2,3,5) AND tp_order.is_confirm=0",'sum(cost_price*number) as sum,sum(tp_order_item.vouchers_amount) as vouchers_amount,sum(tp_order_item.express_fee) as express_fee');
		//商品供货商给分销售的拿货价合计  -  优惠券总计 +  供货商付的运费
		
		$frozen_money = $frozen_money['sum'] - $frozen_money['vouchers_amount'] + $frozen_money['express_fee'] ? : 0.00;
		$this->assign('deposit_num',$deposit_num);
		$this->assign('left_money',$left_money);
		$this->assign('frozen_money',$frozen_money);
		$this->assign('head_title', '资金信息');
		$this->display();
	}

	/**
	 * 财务明细
	 * @author 姜伟
	 * @return void
	 * @todo 取account表中该用户的所有数据
	 */
	public function get_account_log()
	{
		$AccountModel = new AccountModel();
		$start_time = $this->_request('start_time', '');
		$end_time = $this->_request('end_time', '');
		$change_type = $this->_request('change_type', '');

		if ($start_time) {
			$start_time = str_replace('+', ' ', $start_time);
		}
		if ($end_time) {
			$end_time = str_replace('+', ' ', $end_time);
		}

		//通过时间区间筛选数据
		if ($start_time && $end_time)
			$where['addtime'] = array(array('GT', strtotime($start_time)), array('LT', strtotime($end_time)), 'AND');
		//按用户id筛选
		$where['user_id'] = $this->user_id;
		if ($change_type) {
			$where['change_type'] = $change_type;
		}

		if(I('post.export')){
			//引入PHPExcel类库
			vendor('Excel.PHPExcel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Da")
				->setLastModifiedBy("Da")
				->setTitle("Office 2007 XLSX Test Document")
				->setSubject("Office 2007 XLSX Test Document")
				->setDescription("Test document for Office 2007 XLSX,generated using PHP classes.")
				->setKeywords("office 2007 openxml php")
				->setCategory("明细列表");

			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet(0)->setTitle('明细列表');          //标题
			$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);      //单元格宽度
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial'); //设置字体
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);      //设置字体大小

			$objPHPExcel->getActiveSheet(0)->setCellValue('A1', '流水号');
			$objPHPExcel->getActiveSheet(0)->setCellValue('B1', '时间');
			$objPHPExcel->getActiveSheet(0)->setCellValue('C1', '类型');
			$objPHPExcel->getActiveSheet(0)->setCellValue('D1', '收入');
			$objPHPExcel->getActiveSheet(0)->setCellValue('E1', '支出');
			$objPHPExcel->getActiveSheet(0)->setCellValue('F1', '当前预存款');
			$objPHPExcel->getActiveSheet(0)->setCellValue('G1', '备注');
//			$objPHPExcel->getActiveSheet(0)->setCellValue('H1', '库位');
//			$objPHPExcel->getActiveSheet(0)->setCellValue('I1', '状态');
//			$objPHPExcel->getActiveSheet(0)->setCellValue('J1', '停用时间');

			$AccountModel->setStart(0);  //分页获取所有的级别信息
			$AccountModel->setLimit(10000);

			$fields = 'user_id,proof,addtime,change_type,amount_in,amount_out,amount_after_pay,remark';
			$list = $AccountModel->getAccountList($fields, $where, 'addtime desc');
			foreach ($list as $key => $value) {
				$list[$key]['change_type'] = change_change_type($value['change_type']);
			}
			var_dump($list);
//			$list = $obj->getList($list);
//			$list = $obj->getSafeColor($list);

			//每行数据的内容
			foreach ($list AS $k => $v ) {
				static $i  = 2;
				$objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i, strval($v['proof'].'\t'));
				$objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, date('Y-m-d H:i:s',$v['addtime']));
				$objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, $v['change_type']);
				$objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, '+'.number_format($v['amount_in'],2));
				$objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, '-'.number_format(-$v['amount_out'],2));
				$objPHPExcel->getActiveSheet(0)->setCellValue('F' . $i, number_format($v['amount_after_pay'],2));
				$objPHPExcel->getActiveSheet(0)->setCellValue('G' . $i, $v['remark']);
//				$objPHPExcel->getActiveSheet(0)->setCellValue('H' . $i, $v['stock_position']);

//				if($v['material_status'] == 0){
//					$objPHPExcel->getActiveSheet(0)->setCellValue('I' . $i, "停用");
//				}
//				if($v['material_status'] == 1){
//					$objPHPExcel->getActiveSheet(0)->setCellValue('I' . $i, "在用");
//				}
//
//				if($v['stop_status_time']){
//					$objPHPExcel->getActiveSheet(0)->setCellValue('J' . $i, date("Y-m-d H:i:s",$v['stop_status_time']));
//				}else{
//					$objPHPExcel->getActiveSheet(0)->setCellValue('J' . $i, "无");
//				}

				$i++;
			}

			//直接输出文件到浏览器下载
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="' . '明细列表' . date("YmdHis") . '.xls');
			header('Cache-Control: max-age=0');
			ob_clean(); //关键
			flush(); //关键
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}

		//获取订单列表
		import('ORG.Util.Pagelist');// 导入分页类
		$AccountModel = new AccountModel();
		$count = $AccountModel->getAccountNum($where);
		$Page = new Pagelist($count, C('PER_PAGE_NUM'));
		$show = $Page->show();
		$fields = 'user_id,proof,addtime,change_type,amount_in,amount_out,amount_after_pay,not_confirm_money,remark';
		$AccountModel->setStart($Page->firstRow);
		$AccountModel->setLimit(C('PER_PAGE_NUM'));
		$changed_list = $AccountModel->getAccountList($fields, $where, 'addtime desc');
		foreach ($changed_list as $key => $value) {
			$changed_list[$key]['change_type'] = change_change_type($value['change_type']);
		}

		$kind_arr = [];
		for ($i = 1; $i <= 20; $i++) {
			$arr = [1,5,7,17,18,19,20];
			if(in_array($i,$arr)) {
				$kind_arr[] = [
					'change_type' => $i,
					'change_type_name' => change_change_type($i)
				];
			}
		}

		$this->assign('kind_arr', $kind_arr);
		$this->assign('changed_list', $changed_list);
		$this->assign('page', $show);
		$this->assign('start_time', $start_time);
		$this->assign('end_time', $end_time);
		$this->assign('head_title', '明细列表');

		$this->display();
	}


	/**
	 * 申请提现
	 * @author chenhuabo
	 * @param void
	 * @return void
	 * @todo 申请提现
	 */
	public function deposit_apply()
	{
		// $this->assign('action_title','提现记录');
		// $this->assign('action_src','/McpDeposit/get_deposit_list/mod_id/5');

		$user_obj = new UserModel($this->user_id);
		$left_money = $user_obj->getLeftMoney();
		$user_obj = new UserModel($this->user_id);
		$user_info = $user_obj->getUserInfo('realname,mobile','user_id ='.$this->user_id);

		if (IS_POST) {
			$money = $this->_post('money');

			if (!$money || $money<=0){
				$this->error('请填写提现金额');
			}

			if ($money > $left_money) {
				$this->error('余额不足，提现申请失败！');
			}
			$deposit_obj = new DepositApplyModel();

			$config_obj = new ConfigBaseModel();
			$success = $deposit_obj->addDepositApply([
				'user_id' => $this->user_id,
				'money' => $money,
				'deposit_server_charge'=>$money*$config_obj->getConfig('deposit_percent')/100,
				'real_get_money'=>$money*(1-$config_obj->getConfig('deposit_percent')/100)
			]);

			if ($success) {
				$this->success('提现申请成功！');
			} else {
				$this->error('提现申请失败！');
			}
		}

		$this->assign('left_money', $left_money);
		$this->assign('user_info', $user_info);
		$this->assign('head_title', '申请提现');
		$this->display();
	}


	/**
	 * 银行卡绑定
	 * @author chenhuabo
	 * @param void
	 * @return void
	 * @todo 银行卡绑定
	 */
	public function add_edit_bankcard()
	{
		$user_obj = new UserModel();
		$username = $user_obj->getUserInfo('username', 'user_id=' . $this->user_id)['username'];

		$bank_card_obj = new BankCardModel();
		$card_info = $bank_card_obj->getBankCardInfo('user_id=' . $this->user_id);

		if (IS_POST) {
			$account = $this->_post('account');
			$realname = $this->_post('realname');
			$opening_bank = $this->_post('opening_bank');

			if (!$account) {
				$this->error('请填写银行卡号！');
			}

			if (!$realname) {
				$this->error('请填写开户人姓名！');
			}

			if (!$opening_bank) {
				$this->error('请填写开户支行！');
			}

			if ($card_info) {
				$success = $bank_card_obj
					->where('user_id=' . $this->user_id)
					->save([
						'account' => $account,
						'realname' => $realname,
						'opening_bank' => $opening_bank,
					]);
			} else {
				$success = $bank_card_obj
					->addBankCard([
						'user_id' => $this->user_id,
						'account' => $account,
						'realname' => $realname,
						'opening_bank' => $opening_bank,
					]);
			}
			if ($success) {
				$this->success('银行卡绑定成功！');
			} else {
				$this->error('银行卡绑定失败！');
			}

		}
		$this->assign('card_info', $card_info);
		$this->assign('username', $username);
		$this->assign('head_title', '银行卡绑定');
		$this->display();
	}

	/**
	 * 提现记录
	 * @author chenhuabo
	 * @param void
	 * @return $deposit_list
	 * @todo 提现记录
	 */
	public function get_deposit_list()
	{
		$where = 'user_id=' . $this->user_id;
		$deposit_obj = new DepositApplyModel();

		//分页处理
		import('ORG.Util.Pagelist');
		$count = $deposit_obj->getDepositApplyNum($where);
		$Page = new Pagelist($count, C('PER_PAGE_NUM'));
		$deposit_obj->setStart($Page->firstRow);
		$deposit_obj->setLimit($Page->listRows);
		$show = $Page->show();
		$this->assign('show', $show);

		$deposit_list = $deposit_obj->getDepositApplyList('', $where, ' addtime DESC');
		$deposit_list = $deposit_obj->getListData($deposit_list);
		$this->assign('deposit_list', $deposit_list);
		#echo "<pre>";
		#print_r($deposit_list);
		#echo "</pre>";
		#echo $deposit_obj->getLastSql();

		//获取提现类型列表
		$this->assign('deposit_type_list', DepositApplyModel::getDepositTypeSelectList());

		$this->assign('head_title', '提现记录');
		$this->display();
	}
}