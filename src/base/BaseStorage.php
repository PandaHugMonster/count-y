<?php


namespace spaf\county\base;


use Closure;
use spaf\simputils\attributes\Property;
use spaf\simputils\generic\SimpleObject;

/**
 * Class BaseStorage
 *
 * @property int $value
 * @property Closure|Box|array|null $checker
 * @package spaf\county\base
 */
abstract class BaseStorage extends SimpleObject {

	private array|Box|Closure|null $_checker_callback_array = null;

	abstract protected function storeValue(int $value);

	#[Property('value')]
	abstract protected function getValue(): int;

	#[Property('value')]
	protected function setValue(int $value) {
		$this->storeValue($value);
		if (!empty($this->_checker_callback_array))
			call_user_func($this->_checker_callback_array, $value);
	}

	#[Property('checker')]
	protected function setChecker(Closure|Box|array|null $callback) {
		$this->_checker_callback_array = $callback;
	}

	#[Property('checker')]
	protected function getChecker(): Closure|Box|array|null {
		return $this->_checker_callback_array;
	}

	public function resetValue(): bool {
		$this->value = 0;
		return $this->value === 0;
	}

	public function incrementValue(): int {
		$current_value = $this->value + 1;
		$this->value = $current_value;
		return $this->value === $current_value
			?$current_value
			:$this->value;
	}

	public function decrementValue(): int {
		$current_value = $this->value - 1;
		if ($current_value < 0)
			$current_value = 0;
		$this->value = $current_value;
		return $this->value === $current_value
			?$current_value
			:$this->value;
	}
}
