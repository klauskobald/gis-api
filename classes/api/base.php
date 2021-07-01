<?php

require_once __DIR__ . '/response.php';

abstract class api_base {

	protected $arguments;
	protected $method;
	protected $auth;
	protected $body = null;

	final public function __construct($method, $arguments, api_authentication $auth = null, $body = null) {
		$this->method = strtolower($method);
		$this->arguments = $arguments;
		$this->auth = $auth;
		$this->body = $this->repairJson(json_decode($body, JSON_OBJECT_AS_ARRAY));
	}

	private function repairJson($j) {
		$r = array();
		foreach ((array)$j as $k => $v) {
			if (is_array($v))
				$r[$k] = $this->repairJson($v);
			else {
				$vl = strtolower($v);
				switch($vl){
					case 'true': $v=true; break;
					case 'false': $v=false; break;
				}
				$r[$k] = $v;
			}
		}
		return $r;
	}


	public function run_head() {
	}


	/**
	 * @return Response
	 */
	protected function run_get() {
		throw new ApiException('Implement ' . get_class($this) . '::' . __FUNCTION__);
	}

	/**
	 * @return Response
	 */
	protected function run_put() {
		throw new ApiException('Implement ' . get_class($this) . '::' . __FUNCTION__);
	}

	/**
	 * @return Response
	 */
	protected function run_post() {
		throw new ApiException('Implement ' . get_class($this) . '::' . __FUNCTION__);
	}


	/**
	 * @return Response
	 */
	protected function run_delete() {
		throw new ApiException('Implement ' . __METHOD__);
	}


	/**
	 * @return Response
	 */
	final public function run() {
		if ($this->auth === null || $this->auth->isAllowed(str_replace('api_', '', get_class($this)), $this->method)) {
			$method = 'run_' . $this->method;
			$r = $this->$method();
			if ($r) {

				if (!is_a($r, 'Response')) {
					throw new ApiException(get_class($this) . '->' . $method . ' does not return a Response object');
				}
				return $r;
			}

			return new ResponseNotFound();
		} else {
			return new ResponseForbidden('auth.missing', '');
		}

	}


}

class ApiException extends Exception {

}