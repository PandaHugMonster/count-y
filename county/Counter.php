<?php


namespace spaf\county;


class Counter {

	private int $_counter = 0;

	public function visit() {
		$this->_counter++;
	}

	public function get_count() {
		return $this->_counter;
	}

}