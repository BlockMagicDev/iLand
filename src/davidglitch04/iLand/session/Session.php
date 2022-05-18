<?php

declare(strict_types=1);

namespace davidglitch04\iLand\session;

use pocketmine\world\Position;
use function is_null;

class Session {
	private array $data = [];


	public function __construct(string $username) {
		$this->data['Username'] = $username;
		$this->data['A'] = null;
		$this->data['B'] = null;
	}

	public function setNextPosition(Position $position) : string {
		if ($this->isNull('A')) {
			$this->data['A'] = $position;
			$string = "A";
		} elseif ($this->isNull('B')) {
			$this->data['B'] = $position;
			$string = "B";
		}
		return $string;
	}

	public function getPositionA() : Position {
		return $this->data['A'];
	}

	public function getPositionB() : Position {
		return $this->data['B'];
	}

	public function isNull(string $location) : bool {
		if (is_null($this->data[$location])) {
			return true;
		} else {
			return false;
		}
	}
}
