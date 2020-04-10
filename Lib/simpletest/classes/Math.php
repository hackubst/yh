<?php
class Math 
{
	function Math()
	{
		echo 'calling function Math().' . "<br>";
	}

	function add($opt1, $opt2)
	{
		return $opt1 + $opt2;
	}

	function minus($opt1, $opt2)
	{
		return $opt1 - $opt2;
	}

	function multiply($opt1, $opt2)
	{
		return $opt1 * $opt2;
	}

	function divide($opt1, $opt2)
	{
		return $opt1 / $opt2;
	}
}
