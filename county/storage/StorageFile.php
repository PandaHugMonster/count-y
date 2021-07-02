<?php


namespace spaf\county\storage;


use spaf\county\base\BaseStorage;

class StorageFile extends BaseStorage {

//	TODO    Implement optional locking mechanism

	private ?string $_file_path = null;
	protected ?array $last_state = null;
	public string $data_set_name = 'default';

	public function __construct(?string $file_path = null) {
		$this->_file_path = $file_path ?? 'count-y.json';
	}

	public function read_from_file(): ?string {
		if (!file_exists($this->_file_path))
			return null;

//		$fd = fopen($this->_file_path, 'r');
//		$content = fread($fd, filesize($this->_file_path) + 1);
//		fclose($fd);
		$content = file_get_contents($this->_file_path);

		return $content;
	}

	public function write_to_file(string $data_string): bool {
//		$fd = fopen($this->_file_path, 'w+');
//		$res = fwrite($fd, $data_string);
//		fclose($fd);
		$res = file_put_contents($this->_file_path, $data_string);
		return boolval($res);
	}

	protected function set_data_to_file(int $value): bool {
//		print_r('TEST: '.$value);
		$data = $this->get_data_from_file();
		$data[$this->data_set_name] = [
			'counter' => $value,
			'last_update_at' => date('Y-m-d H:i:s'),
		];
		$data_string = json_encode($data);
		return $this->write_to_file($data_string);
	}

	protected function get_data_from_file(): ?array {
		$this->last_state = null;
		$content = $this->read_from_file();

		if (empty($content))
			return null;

		// Currently only json format is supported
		$this->last_state = json_decode($content, true);
		return $this->last_state;
	}

	protected function get_named_data(): ?array {
		$data = $this->get_data_from_file();
		if (!empty($data[$this->data_set_name]))
			return $data[$this->data_set_name];

		return null;
	}

	/**
	 * @param int $value
	 *
	 * @return bool
	 */
	protected function store_value(int $value): bool {
		return $this->set_data_to_file($value);
	}

	/**
	 * @return int
	 */
	public function get_value(): int {
		$data = $this->get_named_data();
		if (empty($data['counter']))
			return 0;
		return (int) $data['counter'];
	}
}