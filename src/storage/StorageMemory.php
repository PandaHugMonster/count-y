<?php


namespace spaf\county\storage;


use Exception;
use spaf\county\base\BaseStorage;
use spaf\simputils\attributes\Property;

class StorageMemory extends BaseStorage {

	private int $_storage = 0;

	/**
	 * @param int $value
	 *
	 * @return bool
	 */
	protected function storeValue(int $value): bool {
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
	#[Property('value')]
	public function getValue(): int {
		return $this->_storage;
	}
}
