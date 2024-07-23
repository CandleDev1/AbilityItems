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

class StrengthItem extends ItemManager
{


    private Item $item;

    public static function init(): void {
        ItemRegistry::registerItem('strength', StrengthItem::class);
    }

    public function __construct(Player $receiver) {
       $itemName = "§cStrength";
       $nbt = "strength_item";
       $effect = VanillaEffects::STRENGTH();
       $time = 600;
       $cooldown = 600;

       parent::__construct($itemName, $nbt, $effect, $time, $cooldown, $receiver);

       $this->item = $this->createItem();
    }


    public function createItem(): Item
    {
        $item = VanillaItems::BLAZE_ROD();
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