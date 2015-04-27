<?php

/* 
 * 	StatusReturn
 * 
 */
class StatusReturn {

	public static function E500($msg = null) {
		header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error", true, 500);
		if (is_null($msg)) {
			return array('status' => 500);
		}
		return array('status' => 500, 'error' => $msg);
	}

	public static function E409($msg = null) {
		header($_SERVER['SERVER_PROTOCOL'] . " 409 Conflict", true, 409);
		if (is_null($msg)) {
			return array('status' => 409);
		}
		return array('status' => 409, 'error' => $msg);
	}

	public static function E404($msg = null) {
		header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
		if (is_null($msg)) {
			return array('status' => 404);
		}
		return array('status' => 404, 'error' => $msg);
	}

	public static function E401($msg = null) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 401 Authorization Required', true, 401);
		if (is_null($msg)) {
			return array('status' => 401);
		}
		return array('status' => 401, 'error' => $msg);
	}

	public static function E400($msg = null) {
		header($_SERVER['SERVER_PROTOCOL'] . " 400 Bad Request", true, 400);
		if (is_null($msg)) {
			return array('status' => 400);
		}
		return array('status' => 400, 'error' => $msg);
	}

	public static function E204($msg = null) {
		header($_SERVER['SERVER_PROTOCOL'] . " 204 No Content", true, 204);
		if (is_null($msg)) {
			return array('status' => 204);
		}
		return array('status' => 204, 'error' => $msg);
	}

	public static function S200($msg = null) {
		header($_SERVER['SERVER_PROTOCOL'] . " 200 OK", true, 200);
		if (is_null($msg)) {
			return array('status' => 200);
		}
		return array('status' => 200, 'success' => $msg);
	}

}
