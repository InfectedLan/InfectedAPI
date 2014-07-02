<?php

/*
 * Attempt at outputting clean code to the client
 */
class Formatting {
	private static $indents = 0;
	public static function echoIndent($str)
	{
		for($i = 0; $i < self::indents; $i++)
		{
			echo "    ";
		}

		echo $str;
		echo "\n";
		
		self::indents
	}
	public static function echo($str)
	{
		for($i = 0; $i < self::indents; $i++)
		{
			echo "    ";
		}

		echo $str;
		echo "\n";
	}
	public static function echoDeIndent($str)
	{
		for($i = 0; $i < self::indents; $i++)
		{
			echo "    ";
		}

		echo $str;
		echo "\n";

		if(self::indents>0)
		{
			self::indents--;
		}
	}
}

?>