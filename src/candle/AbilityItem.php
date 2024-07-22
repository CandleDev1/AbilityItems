<?php

namespace candle;

use candle\commands\AbilityCommand;
use candle\commands\renameCommand;
use candle\managers\ItemRegistry;
use candle\types\JumpItem;
use candle\types\StrengthItem;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class AbilityItem extends PluginBase
{

    use SingletonTrait;


    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public static function getInstance(): self {
        return self::$instance;
    }


    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register("AbilityCommand", new AbilityCommand());
        $this->getServer()->getCommandMap()->register("RenameCommand", new renameCommand());

        self::initAbilityItems();

    }

    public function initAbilityItems(): void {
        StrengthItem::init();
        JumpItem::init();
    }

}