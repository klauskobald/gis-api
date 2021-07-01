<?php
/**
 * User: klausk
 * Date: 2020-05-02
 * Time: 12:33
 */
ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_STRICT);

require("classes/api/response.php");

spl_autoload_register(
	function ($c) {
		$f = "classes/" . str_replace("_","/",$c) . ".php";
		require_once $f;
	}
);

$ob_file = @fopen('/proc/1/fd/1', 'a');
if(!$ob_file)
	$ob_file = fopen("php://stdout", 'a');

function ob_file_callback($buffer) {
	global $ob_file;
	fwrite($ob_file, $buffer);
}

if ($ob_file)
	ob_start('ob_file_callback');
