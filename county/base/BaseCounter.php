<?php


namespace spaf\county\base;


use JetBrains\PhpStorm\ArrayShape;
use spaf\county\storage\StorageMemory;

abstract class BaseCounter {

	protected ?BaseStorage $storage = null;

	/** @noinspection PhpPureAttributeCanBeAddedInspection */
	public function __construct(?BaseStorage $storage = null) {
		if (empty($storage))
			$storage = new StorageMemory();
		$this->storage = $storage;
	}

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


	public function add_event(string $name, callable $on_event, callable|int $condition = null, bool $is_replace = false): bool {
		return $this->storage->add_event($name, $on_event, $condition, $is_replace);
	}

	#[ArrayShape([ 'if' => 'callable', 'on' => 'callable' ])]
	public function get_event(string $name): ?array {
		return $this->storage->get_event($name);
	}

	public function remove_event(string $name): bool {
		return $this->storage->remove_event($name);
	}

}