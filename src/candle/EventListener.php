<?php

namespace candle;

use candle\managers\ItemFactory;
use candle\managers\ItemRegistry;
use candle\utils\TimeConverter;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class EventListener implements Listener
{
    private $cooldowns = [];

    public function abilityListener(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $nbt = $item->getNamedTag();

        foreach (ItemRegistry::getItems() as $type => $class) {
            $abilityItem = ItemFactory::createItem($type, $player);
            if ($abilityItem && $nbt->getTag($abilityItem->getNbt()) instanceof CompoundTag) {
                $time = TimeConverter::ticksToSeconds($abilityItem->getCooldown());

                $cooldown = $player->getName() . "_" . strtolower($type);
                $currentTime = microtime(true);

                if (isset($this->cooldowns[$cooldown]) && ($currentTime - $this->cooldowns[$cooldown]) < $time) {
                    $remainingTime = $time - ($currentTime - $this->cooldowns[$cooldown]);
                    $player->sendMessage(TextFormat::RED . "You must wait " . round($remainingTime, 1) . " seconds before using " . strtolower($type) . " again.");
                    return;
                }

                if ($abilityItem->getItemName() != "§7Slowness") {
                    $abilityItem->apply($player);
                    $player->sendMessage("§aYou received $time seconds of " . strtolower($type) . "!");
                } else {
                    foreach ($player->getWorld()->getPlayers() as $nearbyPlayer) {
                        if ($nearbyPlayer !== $player && $nearbyPlayer->getPosition()->distance($player->getPosition())) {
                            if ($nearbyPlayer->getPosition()->distance($player->getPosition()) < 3) {
                                $player->sendMessage("§aThey received $time seconds of " . strtolower($type) . "!");
                                $abilityItem->apply($nearbyPlayer);
                            }
                        }
                    }
                }

                $this->cooldowns[$cooldown] = $currentTime;
            }
        }
    }
}
