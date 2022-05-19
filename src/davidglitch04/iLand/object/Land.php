<?php

declare(strict_types=1);

namespace davidglitch04\iLand\object;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\utils\DataUtils;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use function explode;
use function intval;
use function strtolower;
use function trim;

class Land {
	private string $leader;

	private string $startpos;

	private string $endpos;

	public function __construct(string $json) {
		$json = (array) DataUtils::decode($json);
		$this->leader = $json["Name"];
		$this->startpos = $json["Start"];
		$this->endpos = $json["End"];
	}


	public function getLeader() : string {
		return $this->leader;
	}


	public function getWorldName() : string {
		return $this->getStart()->getWorld()->getFolderName();
	}


	public function getConfigFile() : Config {
		$name = trim(strtolower($this->getLeader()));
		$path = iLand::getInstance()->getDataFolder() . "players/" . $name[0] . "/$name.yml";
		return new Config($path, Config::YAML);
	}


	public function getData() : array {
		foreach ($this->getConfigFile()->getAll() as $lands) {
			$lands = DataUtils::decode($lands);
			if ($lands["Start"] == $this->startpos and $lands["End"] == $this->endpos) {
				return $lands;
			}
		}
		return [];
	}


	public function getStart() : Position {
		$position = explode(",", $this->startpos);
		return new Position(
			intval($position[0]),
			intval($position[1]),
			intval($position[2]),
			Server::getInstance()->getWorldManager()->getWorldByName($position[3])
		);
	}


	public function getEnd() : Position {
		$position = explode(",", $this->endpos);
		return new Position(
			intval($position[0]),
			intval($position[1]),
			intval($position[2]),
			Server::getInstance()->getWorldManager()->getWorldByName($position[3])
		);
	}


	public function contains(Position $position) : bool {
		$start = $this->getStart();
		$end = $this->getEnd();
		if ($start->getWorld()->getFolderName() == $position->getWorld()->getFolderName()) {
			if (($position->getX() <= $end->getX() && $position->getX() >= $start->getX()
			&& $position->getZ() >= $start->getZ() && $position->getZ() <= $end->getZ())) {
				return true;
			}
		}
		return false;
	}


	public function equals(string $startpos, string $endpos) : bool {
		$worldname = iLand::getInstance()->getLandManager()->StringToPosition($startpos)->getWorld()->getFolderName();
		if ($this->getWorldName() == $worldname and $startpos == $this->startpos and $endpos == $this->endpos) {
			return true;
		} else {
			return false;
		}
	}
}