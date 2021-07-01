<?php

/**
 *     Payload kann auch array sein, dann wird im Json isCollection = true
 *
 * Class Response
 */
abstract class Response {

	protected $payload;
	protected $log;
	protected $httpStatusCode = null;

	/**
	 * @var bool Falls die Antwort mehrere EInzelteile enthält, zb von actions mit mehreren IDs
	 */
	protected $isCollection;

	/**
	 * Response constructor.
	 * @param mixed $payload
	 */
	public function __construct($payload = null, $isCollection = false) {
		$this->payload = $payload;
		$this->isCollection = $isCollection;
	}

	/**
	 * @param string $log
	 */
	public function setLog($log) {
		$this->log = explode("\n", $log);
	}

	final public function getHttpStatusCode() {

//		if (!$this->httpStatusCode) {
//			throw new Exception('No HTTP-StatusCode defined for class' . get_class($this));
//		}

		return $this->httpStatusCode;
	}

	public function toJson($pretty = false) {
		$a = array(
			'status' => str_replace('Response', '', get_class($this))
		);
		if ($this->payload !== null)
			$a['payload'] = $this->payload;

		if ($this->log)
			$a['log'] = $this->log;
//		print_r($a);
		return json_encode(
			$a, JSON_UNESCAPED_SLASHES | ($pretty ? JSON_PRETTY_PRINT : 0)
		);
	}
}

class ResponseOk extends Response {

	protected $httpStatusCode = 200;
}

class ResponseNoContent extends Response {
	protected $httpStatusCode = 204;
}

class ResponseBadRequest extends Response {
	protected $httpStatusCode = 400;
}

class ResponseForbidden extends Response {
	protected $httpStatusCode = 403;

	public function __construct($code, $details = null) {
		parent::__construct(array('code' => $code, 'details' => $details));
	}
}

class ResponseNotFound extends Response {
	protected $httpStatusCode = 404;
}

class ResponseException extends Response {

	protected $httpStatusCode = 500;

	public function __construct(Exception $e) {
		parent::__construct(array('class' => get_class($e), 'message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile() . ':' . $e->getLine()));
	}
}