<?php
require_once('../autorun.php');
require_once('../classes/log.php');

class TestOfLogging extends UnitTestCase {
    function testLogCreatesNewFileOnFirstMessage() {
        @unlink('../temp/test.log');
        $log = new Log('../temp/test.log');
        $this->assertFalse(file_exists('../temp/test.log'));
        $log->message('Should write this to a file');
        $this->assertTrue(file_exists('../temp/test.log'));
    }
}
?>
