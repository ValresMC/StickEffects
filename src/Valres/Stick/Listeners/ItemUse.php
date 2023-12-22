<?php

namespace Valres\Stick\Listeners;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use Valres\Stick\Main;

class ItemUse implements Listener
{

    private static array $cooldown = [];

    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $time = time();

        if($this->isStick($item)){
            if(isset(self::$cooldown[$player->getName()][strtolower($item->getName())]) and self::$cooldown[$player->getName()][strtolower($item->getName())] > $time){
                $remaining = self::$cooldown[$player->getName()][strtolower($item->getName())] - $time;
                $player->sendMessage(str_replace("{temps}", $remaining, Main::getInstance()->getConfig()->get("message")));
            } else {
                $effects = $this->getEffects($item);
                foreach($effects as $effect){
                    $player->getEffects()->add(new EffectInstance(StringToEffectParser::getInstance()->parse($effect[0]), $effect[1]*20, $effect[2]+1, $effect[3]));
                }
                $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
                self::$cooldown[$player->getName()][strtolower($item->getName())] = $time + $this->getCooldown($item);
            }
        }
    }

    public function isStick(Item $item): bool
    {
        $config = Main::getInstance()->getConfig();

        foreach($config->get("sticks") as $name => ["effects" => $effects, "cooldown" => $cooldown]){
            if(strtolower($item->getName()) === strtolower($name)){
                return true;
            }
        }
        return false;
    }

    public function getEffects(Item $item): ?array
    {
        $config = Main::getInstance()->getConfig();

        foreach($config->get("sticks") as $name => ["effects" => $effects, "cooldown" => $cooldown]){
            if(strtolower($item->getName()) === strtolower($name)){
                return $effects;
            }
        }
        return null;
    }

    public function getCooldown(Item $item): ?int
    {
        $config = Main::getInstance()->getConfig();

        foreach($config->get("sticks") as $name => ["effects" => $effects, "cooldown" => $cooldown]){
            if(strtolower($item->getName()) === strtolower($name)){
                return $cooldown;
            }
        }
        return null;
    }
}
