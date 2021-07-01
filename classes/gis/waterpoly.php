<?php
/**
 * User: klausk
 * Date: 29.06.21
 * Time: 14:24
 */


class gis_waterpoly extends gis_base {

	protected function makeQuery(gisRequest $r){
		$r->extend(1);

		return sprintf("select st_astext(geom) from water_polygons WHERE geom && ST_MakeEnvelope(%f,%f,%f,%f, 4326)", $r->minx, $r->miny, $r->maxx, $r->maxy);
	}

	protected function run_get() {
		throw new Exception("not implemented");

		list($minx,$miny,$maxx,$maxy)=$this->arguments;

		$pg=new pg();
		$pg->query( "select st_astext(geom) from water_polygons WHERE geom && ST_MakeEnvelope($minx,$miny,$maxx,$maxy, 4326)");

		$r=array();
		while($row=$pg->getNextRow()){
			$r[]=print_r($row[0],1);
		}

		return new ResponseOk($r);
	}


}