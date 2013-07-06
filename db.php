<?php

class db extends mysqli {
	public static $c = array();
	
	public static function addConnection($id = 0, $host = MYSQL_HOST, $user = MYSQL_USER, $pass = MYSQL_PASS, $db = MYSQL_DB, $port = MYSQL_PORT, $socket = MYSQL_SOCKET) {
		self::$c[$id] = new self($host, $user, $pass, $db, $port, $socket);
		return self::$c[$id];
	}
	public static function c($id) {
		return self::$c[$id];
	}
	
	public function __construct($host = MYSQL_HOST, $user = MYSQL_USER, $pass = MYSQL_PASS, $db = MYSQL_DB, $port = MYSQL_PORT, $socket = MYSQL_SOCKET) {
		$this->_connect($host, $user, $pass, $db, $port, $socket);
	}
	private function _connect($host, $user, $pass, $db, $port = 3306, $socket = '') {
		$this->_host = $host;
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_db = $db;
		$this->_port = $port;
		$this->_socket = $socket;
		$this->reconnect();
	}
	private function reconnect() {
		parent::connect($this->_host, $this->_user, $this->_pass, $this->_db, $this->_port, $this->_socket);
		if(mysqli_connect_errno()) trigger_error('DB CONNECT ERROR', E_USER_ERROR);
		parent::query('SET NAMES UTF8');
	}
	public function direct_query($q, $resultmode = MYSQLI_STORE_RESULT, $retry = 0) {
		$rv = parent::query($q, $resultmode);
		if($this->errno) if($this->__error($q, $retry)) return $this->direct_query($q, $resultmode, $retry + 1);
		return $rv;
	}
	public function query($q, $resultmode = MYSQLI_STORE_RESULT, $retry = 0) {
		$rv = parent::query($q, $resultmode);
		if($this->errno) if($this->__error($q, $retry)) return $this->query($q, $resultmode, $retry + 1);
		return $rv;
	}
	public function multi_query($q, $retry = 0) {
		$rv = parent::multi_query($q);
		if($this->errno) if($this->__error($q, $retry)) return $this->direct_query($q, $retry + 1);
		return $rv;
	}
	protected function __error(&$q, $retry) {
		$retry_error_numbers = array();
		$retry_error_numbers[] = 1205; //Lock wait timeout exceeded; try restarting transaction
		$retry_error_numbers[] = 2006; //MySQL server has gone away
		if($retry < 2 and in_array($this->errno, $retry_error_numbers)) {
			parent::close();
			$this->reconnect();
			return true;
		}
		$ee = debug_backtrace();
		$s = '';
		foreach($ee as $e) $s .= basename($e['file']).': '.$e['function'].':'.$e['line']."\n";
		#header('Content-Type: text/plain');
		trigger_error('FUCK: '.$this->errno.': '.$this->error.($q ? ' -- '.$q : '')."\n".$s, E_USER_ERROR);
		die;
	}
}

function db($id = 0) {
    return db::$c[$id];
}
function es($s, $id = 0) {
    return db::$c[$id]->escape_string($s);
}

function hashed_array_to_sql($arr, $ignore_keys = array()) {
    $out = array();
    foreach($arr as $k=>$v) {
    	if(!in_array($k, $ignore_keys)) {
	        $out[] = "`".es($k)."`='".es($v)."'";
	    }
    }
    return $out;
}

?>
