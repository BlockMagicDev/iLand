<?php

namespace davidglitch04\iLand\Form;

use davidglitch04\iLand\Economy\EconomyManager;
use davidglitch04\iLand\iLand;
use davidglitch04\iLand\Libs\Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;

class BuyForm{

    public function __construct(Player $player)
    {
        $this->sendForm($player);
    }

    private function sendForm(Player $player){
        $language = iLand::getLanguage();
        $startpos = iLand::getInstance()->getSessionManager()->getSession($player)->getPositionA();
        $endpos = iLand::getInstance()->getSessionManager()->getSession($player)->getPositionB();
        $length = abs((int)$startpos->getX() - (int)$endpos->getX());
        $width = abs((int)$startpos->getZ() - (int)$endpos->getZ());
        $priceperblock = iLand::getInstance()->getConfig()->get("price/area");
        $blocks = $length * $width;
        $price = $priceperblock * $blocks;
        $form = new SimpleForm(function (Player $player, $data) use ($price, $startpos, $endpos, $blocks, $language){
            if(!isset($data)){
                return false;
            }
            if($data === 0){
                $ecomgr = new EconomyManager();
                $ecomgr->reduceMoney($player, (int)$price, static function(bool $success) use($player, $startpos, $endpos, $language) {
                    if($success){
                        iLand::getInstance()->getProvider()->addLand($player, $startpos, $endpos);
                        $secondform = new SimpleForm(function (Player $player, $data) use ($language){
                            if(!isset($data)){
                                return false;
                            }
                        });
                        $secondform->setTitle("Complete");
                        $secondform->setContent($language->translateString("gui.buyland.succeed"));
                        $secondform->addButton($language->translateString("gui.general.looklook"));
                        $secondform->addButton($language->translateString("gui.general.close"));
                        $player->sendForm($secondform);
                    } else{

                    }
                });
            }
        });
        $form->setTitle($language->translateString("gui.buyland.title"));
        $form->setContent($language->translateString("gui.buyland.content", [$length, $width, $blocks, $price]));
        $form->addButton($language->translateString("gui.buyland.button.confirm"));
        $form->addButton($language->translateString("gui.buyland.button.cancel"));
        $player->sendForm($form);
    }
}