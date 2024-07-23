<?php

namespace candle;

use pocketmine\entity\effect\Effect;
use pocketmine\item\Item;
use pocketmine\player\Player;

abstract class  ItemManager
{

    public function __construct(public string $itemName, public string $nbt, public Effect $effect, public int $time, public int $cooldown, public Player $receiver) {}

    abstract public static function init(): void;

    abstract public function createItem(): Item;
    abstract public function apply(Player $receiver) : void;

    public function getItemName(): string {
        return $this->itemName;
    }

    public function getNbt(): string {
        return $this->nbt;
    }

    public function getEffect(): Effect {
        return $this->effect;
    }

    public function getTime(): int {
        return $this->time;
    }

    public function getReceiver(): Player {
        return $this->receiver;
    }

    public function getCooldown(): Int {
        return $this->cooldown;
    }
}