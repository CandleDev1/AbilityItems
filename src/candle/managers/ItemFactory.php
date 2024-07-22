<?php

namespace candle\managers;

use candle\ItemManager;
use pocketmine\player\Player;

class ItemFactory {

    public static function createItem(string $type, Player $receiver): ?ItemManager
    {
        $class = ItemRegistry::getItemClass($type);
        if ($class !== null && class_exists($class)) {
            return new $class($receiver);
        }
        return null;
    }

}
