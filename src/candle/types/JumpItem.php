<?php

namespace candle\types;

use candle\AbilityItem;
use candle\ItemManager;
use candle\managers\ItemRegistry;
use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class JumpItem extends ItemManager
{

    private Item $item;

    public static function init(): void
    {
        ItemRegistry::registerItem("Jump boost", JumpItem::class);
    }

    public function __construct(Player $receiver)
    {
        $itemName = "§aJump boost§r";
        $lore = [];
        $nbt = "jump_item";
        $effect = VanillaEffects::JUMP_BOOST();
        $time = AbilityItem::getInstance()->getConfig()->get("JumpDuration");
        $cooldown = AbilityItem::getInstance()->getConfig()->get("JumpCooldown");

        parent::__construct($itemName, $lore, $nbt, $effect, $time, $cooldown, $receiver);

        $this->item = $this->createItem();
    }

    public function createItem(): Item
    {
        $item = VanillaItems::SLIMEBALL();
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