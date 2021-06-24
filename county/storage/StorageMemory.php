<?php


namespace spaf\county\storage;


use Exception;
use spaf\county\base\BaseStorage;

class StorageMemory extends BaseStorage {

	private int $_storage = 0;

	/**
	 * @param int $value
	 *
	 * @return bool
	 */
	protected function store_value(int $value): bool {
		try {
			$this->_storage = $value;
		} catch (Exception) {
			return false;
		}
		return true;
	}

	/**
	 * @return int
	 */
	public function get_value(): int {
		return $this->_storage;
	}
}