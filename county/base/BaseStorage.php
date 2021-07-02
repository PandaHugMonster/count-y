<?php


namespace spaf\county\base;


use Closure;

abstract class BaseStorage {

	abstract protected function store_value(int $value): bool;
	abstract public function get_value(): int;

	private array|Closure|null $_checker_callback_array = null;

	public function _set_checker(Closure|array|null $callback) {
		$this->_checker_callback_array = $callback;
	}

	public function set_value(int $value): bool {
		$res = $this->store_value($value);
		if ($res && !empty($this->_checker_callback_array))
			call_user_func($this->_checker_callback_array, $value);
		return $res;
	}

	public function reset_value(): bool {
		return $this->set_value(0);
	}

	public function increment_value(): int {
		$current_value = $this->get_value() + 1;
		if ($this->set_value($current_value))
			return $current_value;
		return $this->get_value();
	}

	public function decrement_value(): int {
		$current_value = $this->get_value() - 1;
		if ($current_value < 0)
			$current_value = 0;
		if ($this->set_value($current_value))
			return $current_value;
		return $this->get_value();
	}

}