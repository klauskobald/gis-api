<?php
/**
 * User: klausk
 * Date: 29.06.21
 * Time: 14:50
 */

class pg{

	protected $pg;
	protected $res;

	public function __construct() {
		$c=config::get("postgres");

		$this->pg=pg_connect($c);
	}

	public function query($query){
		echo " > $query\n";
		$this->res= pg_query($this->pg, $query);
	}

	public function getNextRow(){
		return pg_fetch_row($this->res);
	}



}