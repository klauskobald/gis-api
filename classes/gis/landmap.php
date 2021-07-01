<?php
/**
 * User: klausk
 * Date: 29.06.21
 * Time: 14:24
 */


class gis_landmap extends gis_base {

	protected function makeQuery(gisRequest $r){
		$r->extend(1);

		return sprintf("select st_astext(geom) from land_polygons WHERE geom && ST_MakeEnvelope(%f,%f,%f,%f, 4326)", $r->minx, $r->miny, $r->maxx, $r->maxy);
	}
}