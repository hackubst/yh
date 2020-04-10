<?php
require_once('log.php');

class SessionPool {
	public $_log = null;

	function SessionPool($log = null)
	{
		if ($log)
		{
			$this->_log = $log;
			return;
		}

		$this->_log = new Log('../temp/test.log');
	}

    function logIn($username) {
        $this->_log->message("User $username logged in.");
    }
}
