<?php


namespace spaf\county\base;


use JetBrains\PhpStorm\ArrayShape;

abstract class BaseStorage {

	const EVENT_ARRAY_INDEX_CONDITION = 'if';
	const EVENT_ARRAY_INDEX_DELEGATE = 'on';

	private array $_event_list = [];

	abstract protected function store_value(int $value): bool;
	abstract public function get_value(): int;

	public function set_value(int $value): bool {
		$res = $this->store_value($value);
		if ($res)
			$this->check_value($value);
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

	protected function check_value(int $value): void {
		if ($this->_event_list) {
			foreach ($this->_event_list as $name => $ar) {
				$condition = $ar[self::EVENT_ARRAY_INDEX_CONDITION];
				$delegate = $ar[self::EVENT_ARRAY_INDEX_DELEGATE];
				$is_true = true;
				if (!empty($condition)) {
					if (is_callable($condition))
						$is_true = $condition($value);
					elseif (is_int($condition))
						$is_true = $condition == $value;
				}


				if ($is_true && !empty($delegate))
					$delegate(value: $value, name: $name);
			}
		}

	}

	public function add_event(string $name, callable $on_event, callable|int $condition = null, bool $is_replace = false): bool {
		if (!isset($this->_event_list[$name]) || $is_replace) {
			$this->_event_list[$name] = [
				self::EVENT_ARRAY_INDEX_CONDITION => $condition,
				self::EVENT_ARRAY_INDEX_DELEGATE => $on_event,
			];
			return true;
		}

		return false;
	}

	#[ArrayShape([ 'if' => 'callable', 'on' => 'callable' ])]
	public function get_event(string $name): ?array {
		if (isset($this->_event_list[$name]))
			return $this->_event_list[$name];
		return null;
	}

	public function remove_event(string $name): bool {
		if (isset($this->_event_list[$name])) {
			unset($this->_event_list[$name]);
			return true;
		}
		return false;
	}
}