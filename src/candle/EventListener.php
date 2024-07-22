<?php

namespace candle;

use candle\managers\ItemFactory;
use candle\managers\ItemRegistry;
use candle\utils\TimeConverter;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\nbt\tag\CompoundTag;

class EventListener implements Listener
{
    public function abilityListener(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $nbt = $item->getNamedTag();

        foreach (ItemRegistry::getItems() as $type => $class) {
            $abilityItem = ItemFactory::createItem($type, $player);
            if ($abilityItem && $nbt->getTag($abilityItem->getNbt()) instanceof CompoundTag) {
                $abilityItem->apply($player);
                $time = TimeConverter::ticksToSeconds($abilityItem->getTime());
                $player->sendMessage("§aYou received $time seconds of " . strtolower($type) . "!");
                break;
            }
        }
    }
}
