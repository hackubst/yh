<?php
define('TEST_DIR', 	dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
require_once(TEST_DIR . 'autorun.php');
echo C('TEST_DIR');
class AllTests extends TestSuite 
{
	function AllTests() 
	{
		//echo C("DB_HOST");
		$this->TestSuite('All tests ');
		#$this->addFile(TEST_DIR . 'cases/log_tester.php');
		#$this->addFile(TEST_DIR . 'cases/math_tester.php');
		#$this->addFile(TEST_DIR . 'cases/session_pool_tester.php');
		$this->addFile(TEST_DIR . 'cases/TestOfAgentOrder.class.php');
		
		
	}
}
