<?php

/*
 *
 *   _____ _                     _
 *  |_   _| |                   | |
 *    | | | |     __ _ _ __   __| |
 *    | | | |    / _` | '_ \ / _` |
 *   _| |_| |___| (_| | | | | (_| |
 *  |_____|______\__,_|_| |_|\__,_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author DavidGlitch04
 * @link https://github.com/David-pm-pl/iLand
 *
 *
*/

declare(strict_types=1);

namespace davidglitch04\iLand\database;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\object\Land;
use davidglitch04\iLand\utils\DataUtils;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\world\WorldException;

use function count;
use function file_exists;
use function glob;
use function is_null;
use function mkdir;
use function strtolower;
use function trim;

class YamlProvider implements Provider {
	protected iLand $iland;

	protected Config $received;

	public function __construct(iLand $iland) {
		$this->iland = $iland;
	}

	public function initConfig() : void {
		if (!file_exists($this->iland->getDataFolder() . "players/")) {
			@mkdir($this->iland->getDataFolder() . "players/");
		}
	}

	public function getData(Player $player) : array {
		$name = trim(strtolower($player->getName()));
		if ($name === "") {
			return [];
		}
		$path = $this->iland->getDataFolder() . "players/" . $name . ".yml";
		if (!file_exists($path)) {
			return [];
		} else {
			$config = new Config($path, Config::YAML);
			return (array) $config->getAll();
		}
	}

	/**
	 * @param $key
	 * @param $landdb
	 * @throws \JsonException
	 */
	public function setData(Player $player, $key, $landdb) : void {
		$name = trim(strtolower($player->getName()));
		$landdb = DataUtils::encode($landdb);
		$data = new Config($this->iland->getDataFolder() . "players/" . $name . ".yml", Config::YAML);
		$data->set($key, $landdb);
		$data->save();
	}

	public function CountLand(Player $player) : int {
		$data = $this->getData($player);
		if (empty($data)) {
			return 0;
		} else {
			return count($data);
		}
	}

	public function isOverlap(Position $position) : bool {
		if (!is_null($this->getLandByPosition($position))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @throws \JsonException
	 */
	public function addLand(
		Player   $player,
		Position $positionA,
		Position $positionB
	) : void {
		$name = trim(strtolower($player->getName()));
		if ($player->getWorld()->getFolderName() !== $player->getWorld()->getDisplayName()) {
			throw new WorldException("World foldername does not match world displayname");
			return;
		}
		@mkdir($this->iland->getDataFolder() . "players/");
		$data = new Config($this->iland->getDataFolder() . "players/" . $name . ".yml", Config::YAML);
		$data->set($this->CountLand($player) + 1, DataUtils::encode([
			"Leader" => $player->getName(),
			"Name" => iLand::getLanguage()->translateString("gui.landmgr.unnamed"),
			"Spawn" => iLand::getInstance()->getLandManager()->PositionToString($positionA),
			"Start" => iLand::getInstance()->getLandManager()->PositionToString($positionA),
			"End" => iLand::getInstance()->getLandManager()->PositionToString($positionB),
			"Members" => [],
			"Settings" => [
				"allow_open_chest" => false,
				"use_bucket" => false,
				"use_furnace" => false,
				"allow_place" => false,
				"allow_dropitem" => false,
				"allow_pickupitem" => false,
				"allow_destroy" => false
			]
		]));
		$data->save();
	}

	/**
	 * @throws \JsonException
	 */
	public function delLand(Player $player, int $key) : void {
		$name = trim(strtolower($player->getName()));
		$data = new Config($this->iland->getDataFolder() . "players/" . $name . ".yml", Config::YAML);
		$data->remove($key);
		$data->save();
	}

	public function getLandByPosition(Position $position) : Land|null {
		$x = $position->x;
		$z = $position->z;
		$world = $position->world->getFolderName();
		foreach (glob($this->iland->getDataFolder() . "players/" . "*.yml") as $filename) {
			$config = new Config($filename, Config::YAML);
			foreach ($config->getAll() as $lands) {
				$lands = DataUtils::decode($lands);
				$start = $this->iland->getLandManager()->StringToPosition($lands["Start"]);
				$end = $this->iland->getLandManager()->StringToPosition($lands["End"]);
				if ($start->getWorld()->getFolderName() == $world) {
					if (($x <= $end->getX() && $x >= $start->getX()
						&& $z >= $start->getZ() && $z <= $end->getZ())) {
						return new Land($lands);
					}
				}
			}
		}
		return null;
	}
}
