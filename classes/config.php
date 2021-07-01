<?php
/**
 * User: klausk
 * Date: 2019-01-22
 * Time: 17:20
 */

class config {

	static private $_data, $_cache;

	static public function get($keypath, $strict=false) {
		if (!self::$_data) {
			self::$_data = json_decode(file_get_contents("config.json"), JSON_OBJECT_AS_ARRAY);
			self::$_cache = array();
		}
		if (!array_key_exists($keypath, self::$_cache)) {
			self::$_cache[$keypath] = self::_get(explode(":", $keypath), self::$_data);
			if(self::$_cache[$keypath]===null)
				throw new Exception("config missing: ".$keypath);
		}
		return self::$_cache[$keypath];
	}

	static private function _get($lst, $data) {
//		if(count($lst)==0) return $data;
		$k = array_shift($lst);
		if (array_key_exists($k, $data)) {
			if (count($lst) == 0) return $data[$k];
			return self::_get($lst, $data[$k]);
		}
	}
}