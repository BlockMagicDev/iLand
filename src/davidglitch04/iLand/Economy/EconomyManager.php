<?php

namespace davidglitch04\iLand\economy;

use Closure;
use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\Server;

final class EconomyManager
{
    /** @var \pocketmine\plugin\Plugin|null $eco */
    private $eco;

    public function __construct(){
        $manager = Server::getInstance()->getPluginManager();
        $this->eco = $manager->getPlugin("EconomyAPI") ?? $manager->getPlugin("BedrockEconomy") ?? null;
        unset($manager);
    }
    /**
     * @param Player $player
     * @return int
     */
    public function getMoney(Player $player, Closure $callback): void {
        switch ($this->eco->getName()){
            case "EconomyAPI":
                $money = $this->eco->myMoney($player);
		        assert(is_float($money));
		        $callback($money);
                break;
            case "BedrockEconomy":
                $this->eco->getAPI()->getPlayerBalance($player->getName(), ClosureContext::create(static function(?int $balance) use($callback) : void{
                    $callback($balance ?? 0);
                }));
                break;
            default:
                $this->eco->getAPI()->getPlayerBalance($player->getName(), ClosureContext::create(static function(?int $balance) use($callback) : void{
                    $callback($balance ?? 0);
                }));
        }
    }

    /**
     * @param Player $player
     * @param int $amount
     * @return bool
     */
    public function reduceMoney(Player $player, int $amount, Closure $callback){
        if($this->eco == null){
            $this->plugin->getLogger()->warning("You not have Economy plugin");
            return true;
        }
        switch ($this->eco->getName()){
            case "EconomyAPI":
                $callback($this->eco->reduceMoney($player, $amount) === EconomyAPI::RET_SUCCESS);
                break;
            case "BedrockEconomy":
                $this->eco->getAPI()->subtractFromPlayerBalance($player->getName(), (int) ceil($amount), ClosureContext::create(static function(bool $success) use($callback) : void{
                    $callback($success);
                }));
                break;
        }
    }
}
