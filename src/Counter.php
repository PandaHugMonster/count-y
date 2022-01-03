<?php


namespace spaf\county;


use spaf\county\base\BaseCounter;

/**
 * Counter object
 *
 * If cast to string by default - represents the "count" value
 *
 */
class Counter extends BaseCounter {

	public function __toString(): string {
		return $this->count;
	}
}
