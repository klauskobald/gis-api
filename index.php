<?php
/**
 * User: klausk
 * Date: 2019-04-02
 * Time: 23:39
 */
chdir(__DIR__);
header("Access-Control-Allow-Origin: *");
header("Content-type:application/json");

require 'start.inc.php';

$body = file_get_contents('php://input');
$data = json_decode($body, JSON_OBJECT_AS_ARRAY);
$r = array();
echo "-------------------------------------------------------------\n";
echo date("H:i:s ") . " " . $_SERVER["SERVER_ADDR"] . " [" . getmypid() . "] " . $_REQUEST['cmd'] . ": " . $body;
echo "\n";
try {

	$scr = $_SERVER['REQUEST_URI'];
	echo '[REQUEST] ' . $_SERVER['REQUEST_METHOD'] . " " . $scr . "\n";
	$path = explode('/', $scr);
	$classPath = __DIR__ . '/classes';
	$cl = null;
	while (count($path)) {
		$p = array_shift($path);
		if (strpos($p, "api-") === 0) continue;
		$classPath .= '/' . $p;
		if (is_file($classPath . '.php')) {
			if($last) {
				require $classPath . '.php';
				$cl = $last . '_' . $p;
				break;
			}
		}
		$last = $p;
	}
	if (!$cl) throw new Exception('api.invalid');
	#echo '[CLASS] ' . $cl."\n";

	$auth = null;

	switch ($_SERVER['REQUEST_METHOD']) {
		case 'OPTIONS':
			header("Access-Control-Allow-Headers: apikey, Accept-Language, Content-Type, accept");
			header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, OPTIONS, PATCH');
			$res = new ResponseOk();
			break;
		case 'HEAD':
			$api = new $cl($_SERVER['REQUEST_METHOD'], $path, $auth, $body);
			$res = $api->head();
			break;
		default:
			$api = new $cl($_SERVER['REQUEST_METHOD'], $path, $auth, $body);
			$res = $api->run();
			break;
	}

} catch (Exception $e) {
	$res = new ResponseException($e);
}
//header(':', true, $res->getHttpStatusCode());
$pretty=strpos($_SERVER['REQUEST_URI'],'?pretty')>0;
$str = $res->toJson($pretty);
//echo "[RESPONSE] ".get_class($res)." ".strlen($str)." bytes\n";
ob_flush();
ob_end_clean();
echo $str;