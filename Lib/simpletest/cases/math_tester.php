<?php
require_once('../autorun.php');
require_once('../classes/Math.php');

class TestOfMath extends UnitTestCase {
	private $math = null;

	function TestOfMath()
	{
		$this->math = new Math();
	}

	function testMathAdd()
	{
        $this->assertEqual($this->math->add(1, 2), 3);
        $this->assertEqual($this->math->add(10, 20), 30);
        $this->assertTrue(3.3 - $this->math->add(1.1, 2.2) < 0.01);
    }

	function testMathMinus()
	{
		$this->assertEqual($this->math->minus(3, 2), 1);
	}

	function testMathMutiply()
	{
        $this->assertEqual($this->math->multiply(3, 2), 6);
	}

	function testMathDivide()
	{
        $this->assertEqual($this->math->divide(4, 2), 2);
        $this->assertEqual($this->math->divide(3, 2), 1.5);
	}
}
?>
