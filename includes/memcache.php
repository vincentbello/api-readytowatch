<?php

class Memcache {

	private $memcached;
	protected $memcached_conf = array(
		'default_host'   => 'localhost',
		'default_port'   => 11211,
		'default_weight' => 1
	);

	public function __construct() {
		$this->memcached = new Memcached();
		$this->memcached->addServer($this->$memcached_conf['default_host'], $this->$memcached_conf['default_port']) or die('Could not connect to memcached.');
	}

	// Save data into cache
	// default expiration: 30 days
	public function save($key, $data, $expiration = 2592000) {
		if (get_class($this->memcached) == 'Memcached') {
			return $this->memcached->set($key, $data, $expiration);
		} else {
			return false;
		}
	}

	public function get($key) {
		$data = $this->memcached->get($key);
		return $data ? $data : false;
	}

	public function delete($key) {
		return $this->memcached->delete($key);
	}

	// clean will marks all the items as expired, so occupied memory will be overwritten by new items.
	public function clean() {
		return $this->memcached->flush();
	}
}


?>