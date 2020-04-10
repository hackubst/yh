<?php
//捐赠管理
class AcpDonationAction extends AcpAction {

	public function _initialize()
	{
		parent::_initialize();
	}

	function get_donation_list()
	{
		$this->display();
	}
}
