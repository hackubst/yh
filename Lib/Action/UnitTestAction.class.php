<?php
/**
 * 测试
 * @access public
 * @author 姜伟
 * @Date 2014-02-24
 */
class UnitTestAction extends GlobalAction {
	function all_test()
	{
		require('Lib/simpletest/cases/all_test.php');
	}
}
