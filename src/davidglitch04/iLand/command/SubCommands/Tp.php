<?php

declare(strict_types=1);

namespace davidglitch04\iLand\command\SubCommands;

use davidglitch04\iLand\form\TeleportLandForm;
use davidglitch04\iLand\libs\CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Tp extends BaseSubCommand {
	protected function prepare() : void {
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		if ($sender instanceof Player) {
			new TeleportLandForm($sender);
		}
	}
}
