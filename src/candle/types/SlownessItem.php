<?php

namespace candle\types;

use candle\ItemManager;
use candle\managers\ItemRegistry;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class SlownessItem extends ItemManager
{

    private Item $item;

    public static function init(): void {
        ItemRegistry::registerItem('Slowness', SlownessItem::class);
    }

    public function __construct(Player $receiver) {
        $itemName = "§7Slowness";
        $nbt = "slowness_item";
        $effect = VanillaEffects::SLOWNESS();
        $time = 600;

        parent::__construct($itemName, $nbt, $effect, $time, $receiver);

        $this->item = $this->createItem();
    }


    public function createItem(): Item
    {
        $item = VanillaItems::BONE();
        $item->setCustomName($this->itemName);

        $nbt = $item->getNamedTag();
        $nbt->setTag($this->nbt, new CompoundTag());
        $item->setNamedTag($nbt);

        return $item;
    }

    public function apply(Player $receiver): void
    {
        $effectInstance = new EffectInstance($this->effect, $this->time);

        $receiver->getEffects()->add($effectInstance);
    }

    public function getItem(): Item {
        return $this->item;
    }

}