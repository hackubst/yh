<?php
require_once('../autorun.php');
require_once('../classes/log.php');
require_once('../classes/session_pool.php');

Mock::generate('Log');

class TestOfSessionLogging extends UnitTestCase {
    
    function testLoggingInIsLogged() {
        $log = &new MockLog();
        #$log->expectOnce('message', array('User fred logged in.'));
        $log->expectOnce('message', array('User fred logged in.'));
        $session_pool = &new SessionPool($log);
        $session_pool->logIn('fred');
    }

	function testLoggingInIsLogged1()
	{
		$session_pool = &new SessionPool();
        $session_pool->logIn('fred');
        $messages = file('../temp/test.log');
        $this->assertEqual($messages[0], "Should write this to a fileUser fred logged in.");
	}
}
?>
