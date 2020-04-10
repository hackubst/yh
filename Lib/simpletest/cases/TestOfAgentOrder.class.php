<?php
define('TEST_DIR', 	dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
require_once(TEST_DIR . 'autorun.php');
require_once(dirname(TEST_DIR) . DIRECTORY_SEPARATOR . 'Model/AgentOrderModel.class.php');

class TestOfAgentOrder extends UnitTestCase {
	private $agent_order_obj = null;

	function TestOfAgentOrder()
	{
		$this->agent_order_obj = new AgentOrderModel(1);
	}

    function testGetOrderId() {
        $this->assertEqual($this->agent_order_obj->getOrderId(), 1);

        $this->agent_order_obj = new AgentOrderModel(-1);
        $this->assertEqual($this->agent_order_obj->getOrderId(), -1);
    }

    #function testSetOrderState() {
		#$order_status = $this->agent_order_obj->getOrderState();
        #$this->assertEqual($order_status, 0);
        #$this->agent_order_obj->setOrderState(1);
        #$this->agent_order_obj->saveOrderInfo();
        #$order_status = $this->agent_order_obj->getOrderState();
        #$this->assertEqual($order_status, 1);
    #}
}
