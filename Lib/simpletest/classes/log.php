<?php
class Log 
{
	public $filename = '';

	function Log($filename)
	{
		echo 'calling function Log().' . "<br>";
		$this->filename = $filename;
	}

	function message($message)
	{
		$file = fopen($this->filename, 'a');
		fwrite($file, $message);
		fclose($file);
	}
}
