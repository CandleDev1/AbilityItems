<?php

namespace candle;

use candle\managers\ItemFactory;
use candle\managers\ItemRegistry;
use candle\types\BalloonItem;
use candle\utils\TimeConverter;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
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
                $name = $abilityItem->getItemName();
                $time = TimeConverter::ticksToSeconds($abilityItem->getCooldown());

                $cooldown = $player->getName() . "_" . strtolower($type);
                $currentTime = microtime(true);

                if (isset($this->cooldowns[$cooldown]) && ($currentTime - $this->cooldowns[$cooldown]) < $time) {
                    $remainingTime = $time - ($currentTime - $this->cooldowns[$cooldown]);
                    $player->sendMessage(TextFormat::RED . "You must wait " . round($remainingTime, 1) . " seconds before using " . strtolower($name) . "§c again.");
                    $event->cancel();
                    return;
                }
                $event->cancel();

                if ($abilityItem->getItemName() != "§7Slowness") {
                    $abilityItem->apply($player);
                    $player->sendMessage("§aYou received $time seconds of " . strtolower($name) . "§a!");
                } else {
                    foreach ($player->getWorld()->getPlayers() as $nearbyPlayer) {
                        if ($nearbyPlayer !== $player && $nearbyPlayer->getPosition()->distance($player->getPosition())) {
                            if ($nearbyPlayer->getPosition()->distance($player->getPosition()) < 3) {
                                $player->sendMessage("§aThey received $time seconds of " . strtolower($name) . "§a!");
                                $abilityItem->apply($nearbyPlayer);
                            }
                        }
                    }
                }

                $this->cooldowns[$cooldown] = $currentTime;
            }
        }
    }


    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $nbt = $item->getNamedTag();

        foreach (ItemRegistry::getItems() as $type => $class) {
            $abilityItem = ItemFactory::createItem($type, $player);
            if ($abilityItem && $nbt->getTag($abilityItem->getNbt()) instanceof CompoundTag) {
                $event->cancel();
            }
        }
    }
}
