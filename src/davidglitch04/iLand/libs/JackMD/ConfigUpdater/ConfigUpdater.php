<?php

declare(strict_types = 1);

/**
 *  _____              __ _       _   _           _       _
 * /  __ \            / _(_)     | | | |         | |     | |
 * | /  \/ ___  _ __ | |_ _  __ _| | | |_ __   __| | __ _| |_ ___ _ __
 * | |    / _ \| '_ \|  _| |/ _` | | | | '_ \ / _` |/ _` | __/ _ \ '__|
 * | \__/\ (_) | | | | | | | (_| | |_| | |_) | (_| | (_| | ||  __/ |
 *  \____/\___/|_| |_|_| |_|\__, |\___/| .__/ \__,_|\__,_|\__\___|_|
 *                           __/ |     | |
 *                          |___/      |_|
 *
 * ConfigUpdater, a config updater virion for PocketMine-MP
 * Copyright (c) Ifera (aka JackMD) < https://github.com/Ifera >
 *
 * Discord: ifera#3717
 * Twitter: ifera_tr
 *
 * This software is distributed under "GNU General Public License v3.0".
 *
 * ConfigUpdater is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace davidglitch04\iLand\libs\JackMD\ConfigUpdater;

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
use function basename;
use function explode;
use function rename;
use function str_replace;
use function trim;

class ConfigUpdater {

	/**
	 * @param Plugin $plugin        The plugin you are calling this from.
	 * @param Config $config        The config you want to update.
	 * @param string $configKey     The version key that needs to be checked in the config.
	 * @param int    $latestVersion The latest version of the config. Needs to be integer.
	 * @param string $updateMessage The update message that would be shown on console if the plugin is outdated.
	 */
	public static function checkUpdate(Plugin $plugin, Config $config, string $configKey, int $latestVersion, string $updateMessage = "") : bool {
		if (($config->exists($configKey)) && ((int) $config->get($configKey) === $latestVersion)) {
			return false;
		}

		$configData = self::getConfigData($config);
		$configPath = $configData["configPath"];
		$originalConfig = $configData["configName"];
		$oldConfig = $configData["oldConfigName"];

		if (trim($updateMessage) === "") {
			$updateMessage = "Your $originalConfig file is outdated. Your old $originalConfig has been saved as $oldConfig and a new $originalConfig file has been generated. Please update accordingly.";
		}

		rename($configPath . $originalConfig, $configPath . $oldConfig);

		$plugin->saveResource($originalConfig);

		$task = new ClosureTask(function() use ($plugin, $updateMessage) : void {
			$plugin->getLogger()->critical($updateMessage);
		});

		/* This task is here so that the update message can be sent after full server load */
		$plugin->getScheduler()->scheduleDelayedTask($task, 3 * 20);

		return true;
	}

	/**
	 * Returns the path to current config, the path to the config in plugins folder, the
	 * name of the config and the name of config suffixed with old.
	 */
	private static function getConfigData(Config $config) : array {
		$configPath = $config->getPath();
		$configData = explode(".", basename($configPath));

		$configName = $configData[0];
		$configExtension = "json";

		$originalConfigName = $configName . "." . $configExtension;
		$oldConfigName = $configName . "_old." . $configExtension;

		$configPath = str_replace($originalConfigName, "", $configPath);
		$pluginPath = str_replace("plugin_data", "plugins", $configPath);

		return [
			"configPath" => $configPath,
			"pluginPath" => $pluginPath,
			"configName" => $originalConfigName,
			"oldConfigName" => $oldConfigName
		];
	}
}
