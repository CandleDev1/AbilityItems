<?php

namespace candle;

use candle\commands\AbilityCommand;
use candle\managers\ItemRegistry;
use candle\types\IceAbility;
use candle\types\JumpItem;
use candle\types\PrisonAbility;
use candle\types\SlownessItem;
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
        $this->saveDefaultConfig();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register("AbilityCommand", new AbilityCommand());

        self::initAbilityItems();

    }

    public function initAbilityItems(): void {
        SlownessItem::init();
        PrisonAbility::init();
        IceAbility::init();
    }

}