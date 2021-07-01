<?php
/**
 * User: klausk
 * Date: 30.06.21
 * Time: 20:33
 */

class gisRequest {

	public $siz;
	public $minx, $miny, $maxx, $maxy;

	public function __construct($args) {
		foreach ($args as &$v) $v = floatval($v);
		list($this->siz, $a, $b, $c, $d) = $args;
		if ($this->siz < 16) $this->siz = 16;
		elseif ($this->siz > 8000) $this->siz = 8000;
		if (!$d) { // a/b -> center, c = size
			$deform = cos(deg2rad($b));
#			echo "deform = $deform, ";
			$s = $c;
			$this->minx = $a - $s / 2;
			$this->maxx = $a + $s / 2;
			$this->maxy = $b + $s * $deform / 2;
			$this->miny = $b - $s * $deform / 2;
		} else { // regular rectangle
			$this->minx = $a;
			$this->miny = $b;
			$this->maxx = $c;
			$this->maxy = $d;
		}
	}

	public function extend($degree) {
		$this->minx -= $degree;
		$this->miny -= $degree;
		$this->maxx += $degree;
		$this->maxy += $degree;
	}
}

abstract class gis_base extends api_base {

	/***
	 * @return gisRequest
	 */
	protected function gisRequest() {
		return new gisRequest($this->arguments);
	}

	abstract protected function makeQuery(gisRequest $r);

	protected function run_get() {

		$r = $this->gisRequest();

		echo "query $r->minx, $r->miny, $r->maxx, $r->maxy\n";
		$img = new texture_map($r->minx, $r->miny, $r->maxx, $r->maxy, $r->siz);
		$pg = new pg();
		$pg->query($this->makeQuery($r));
		$ct = 0;
		$pct = 0;
		$vct = 0;
		while ($row = $pg->getNextRow()) {
			$ct++;
			foreach (convert_multipolygon::toArray($row[0]) as $poly) {
				$pct++;
				$vct += count($poly);
				$img->addPoints($poly);
			}
		}
		$mem=intval(memory_get_usage()/1024+1024);
		echo " > $ct rows $pct polygons $vct points, memory usage: $mem Mb\n";

		$img->stream();
	}


}