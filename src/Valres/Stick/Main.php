<?php

namespace Valres\Stick;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Valres\Stick\Listeners\ItemUse;

class Main extends PluginBase
{
    use SingletonTrait;

    protected function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->getLogger()->info("by Valres est lancé !");
        $this->getServer()->getPluginManager()->registerEvents(new ItemUse(), $this);
    }

    protected function onLoad(): void
    {
        self::setInstance($this);
    }
}
