<?php

declare(strict_types=1);

namespace davidglitch04\iLand\utils;

use davidglitch04\iLand\iLand;
use pocketmine\resourcepacks\ZippedResourcePack;
use pocketmine\utils\Filesystem;
use Symfony\Component\Filesystem\Path;
use function json_decode;
use function json_encode;
use function preg_replace;
use function str_contains;
use function utf8_encode;

class DataUtils {
	public static function encode(array $data) : mixed {
		$encode = utf8_encode(json_encode($data));
		return $encode;
	}

	public static function decode(string $encrypt) : mixed {
		$decode = json_decode($encrypt);
		return $decode;
	}

	public static function zipPack(iLand $iLand) : ZippedResourcePack {
		$zip = new \ZipArchive();
		$zip->open(Path::join($iLand->getDataFolder(), $iLand->getName() . '.mcpack'), \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		foreach ($iLand->getResources() as $resource) {
			if ($resource->isFile() and str_contains($resource->getPathname(), 'iLandPack')) {
				$relativePath = Path::normalize(preg_replace("/.*[\/\\\\]iLandPack[\/\\\\].*/U", '', $resource->getPathname()));
				$iLand->saveResource(Path::join('iLandPack', $relativePath), false);
				$zip->addFile(Path::join($iLand->getDataFolder(), 'iLandPack', $relativePath), $relativePath);
			}
		}
		$zip->close();
		Filesystem::recursiveUnlink(Path::join($iLand->getDataFolder() . 'iLandPack'));
		return new ZippedResourcePack(Path::join($iLand->getDataFolder(), $iLand->getName() . '.mcpack'));
	}
}