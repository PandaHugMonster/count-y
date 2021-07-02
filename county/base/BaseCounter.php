<?php


namespace spaf\county\base;


use spaf\county\storage\StorageMemory;

abstract class BaseCounter {

//	TODO    Implement historical values (hour, day, week, month, year)

	const EVENT_ARRAY_INDEX_CONDITION = 'if';
	const EVENT_ARRAY_INDEX_DELEGATE = 'on';

	private array $_event_list = [];

	protected ?BaseStorage $storage = null;

	public function __construct(?BaseStorage $storage = null) {
		if (empty($storage))
			$storage = new StorageMemory();
		$this->storage = $storage;
		$this->storage->_set_checker(function ($value) {
			$this->check_value($value);
		});
	}

//	protected function record_history($value, $name) {
//		if (empty($this->storage) || !method_exists($this->storage, 'save_history'))
//			return ;
//
//
//	}

	public function visit(): int {
		return $this->storage->increment_value();
	}

	public function get_count(): int {
		return $this->storage->get_value();
	}

	public function reset_count(): int {
		$this->storage->reset_value();
		return $this->storage->get_value();
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