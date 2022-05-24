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

namespace davidglitch04\iLand\command\SubCommands;

use davidglitch04\iLand\form\NewLandForm;
use davidglitch04\iLand\iLand;
use davidglitch04\iLand\libs\CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class NewLand extends BaseSubCommand {
	protected function prepare() : void {
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		if ($sender instanceof Player) {
			$session = iLand::getInstance()->getSessionManager();
			$language = iLand::getLanguage();
			if ($session->inSession($sender)) {
				$sender->sendTip($language->translateString("talk.invalidaction"));
				return;
			} else {
				new NewLandForm($sender);
			}
		}
	}
}
