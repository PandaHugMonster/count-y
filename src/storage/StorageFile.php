<?php


namespace spaf\county\storage;


use spaf\county\base\BaseStorage;
use spaf\simputils\attributes\Property;
use spaf\simputils\models\Box;
use spaf\simputils\models\File;
use function spaf\simputils\basic\box;
use function spaf\simputils\basic\fl;
use function spaf\simputils\basic\now;

/**
 * TODO Implement optional locking mechanism
 *
 * @property-read File $file Storage file instance
 */
class StorageFile extends BaseStorage {

	const DEFAULT_FILE = 'count-y.json';

	protected null|File|string $_file = null;

	private ?string $_file_path = null;
	protected ?Box $last_state = null;
	public string $data_set_name = 'default';

	public function __construct(null|File|string $file = null) {
		$this->_file = fl($file ?? static::DEFAULT_FILE);
	}

	#[Property('file')]
	public function getFile(): File {
		// TODO When "read only object-wrapper" will be implemented in SimpUtils
		//      implement here through that wrapper
		return $this->_file;
	}

	/**
	 * @return int
	 */
	#[Property('value')]
	public function getValue(): int {
		$data = $this->getNamedData();
		if (empty($data['counter'])) {
			return 0;
		}
		return (int) $data['counter'];
	}

	protected function getNamedData(): ?Box {
		$this->last_state = box([]);
		if (!empty($content = $this->_file->content)) {
			$this->last_state = box($content);
		}
		return box($this->last_state[$this->data_set_name]) ?? null;
	}

	/**
	 * @param int $value
	 *
	 * @return bool
	 * @throws \Exception
	 */
	protected function storeValue(int $value): bool {
		$data = $this->_file->content;

		$data[$this->data_set_name] = [
			'counter' => $value,
			'last_update_at' => now(),
		];

		// TODO Implement try-catch here with returning false
		$this->_file->content = $data;
		return true;
	}
}
