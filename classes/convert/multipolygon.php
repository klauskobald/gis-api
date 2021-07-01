<?php
/**
 * User: klausk
 * Date: 29.06.21
 * Time: 17:06
 */


class convert_multipolygon {

	static public function toArray($str) {
		$k = "MULTIPOLYGON(((";
		if (strpos($str, $k) === false)
			throw new Exception("no $k found");

		$str = str_replace(array($k, ")))"), "", $str);
		$a = explode("),(", $str);
		$r = array();
		foreach ($a as $poly) {
			$coords = array();

#			$ct=0;

			foreach (explode(",", $poly) as $i) {
				$c = explode(" ", $i);
				$c[0] = floatval($c[0]);
				$c[1] = floatval($c[1]);
				$coords[] = $c;

#				if($ct++>2) break;
			}
			$r[] = $coords;

		}

		return $r;
	}


}