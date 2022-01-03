<?php

namespace spaf\county\base;


use Closure;
use spaf\simputils\attributes\Property;
use spaf\simputils\generic\SimpleObject;
use spaf\simputils\models\Box;
use function spaf\simputils\basic\box;

/**
 * Class BaseCounter
 *
 * @property-read int $count
 *
 * @package spaf\county\base
 */
abstract class BaseCounter extends SimpleObject {

//	TODO    Implement historical values (hour, day, week, month, year)

	const EVENT_ARRAY_INDEX_CONDITION = 'if';
	const EVENT_ARRAY_INDEX_DELEGATE = 'on';

	private Box|array $_event_list;

	protected ?BaseStorage $storage = null;

	public function __construct(?BaseStorage $storage = null) {
		$this->_event_list = box([]);

		if (empty($storage))
			$storage = new StorageMemory();
		$this->storage = $storage;
		$this->storage->checker = Closure::fromCallable([$this, 'checkValue']);
	}

	#[Property('count')]
	protected function getCount(): int {
		return $this->storage->value;
	}

	public function visit(): int {
		return $this->storage->incrementValue();
	}

	public function resetCount(): int {
		$this->storage->resetValue();
		return $this->storage->value;
	}

	protected function checkValue(int $value): void {
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

	public function addEvent(string $name, callable $on_event, callable|int $condition = null, bool $is_replace = false): bool {
		if (!isset($this->_event_list[$name]) || $is_replace) {
			$this->_event_list[$name] = [
				self::EVENT_ARRAY_INDEX_CONDITION => $condition,
				self::EVENT_ARRAY_INDEX_DELEGATE => $on_event,
			];
			return true;
		}

		return false;
	}

	public function getEvent(string $name): ?Box {
		if (isset($this->_event_list[$name]))
			return box($this->_event_list[$name]);
		return null;
	}

	public function removeEvent(string $name): bool {
		if (isset($this->_event_list[$name])) {
			unset($this->_event_list[$name]);
			return true;
		}
		return false;
	}

}
