<?php

namespace candle\types;

use candle\AbilityItem;
use candle\ItemManager;
use candle\managers\ItemRegistry;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\Position;

class IceAbility extends ItemManager
{
    private Item $item;
    private array $iceBlocks = [];

    public static function init(): void
    {
        ItemRegistry::registerItem("IceAbility", IceAbility::class);
    }

    public function __construct(Player $receiver)
    {
        $itemName = "§b§lFrozen Core§r";
        $lore = [
            "§7Unleash the freezing winds, trapping your foes\nin an icy prison.",
            " ",
            "§l§eAbility §r",
            "§b* §7Slowness for 7 seconds",
            "§b* §7Ice Prison for 7 seconds"
        ];
        $nbt = "IceAbility_Item";
        $effect = VanillaEffects::SLOWNESS();
        $time = 7 * 20;
        $cooldown = AbilityItem::getInstance()->getConfig()->get("IceAbilityCooldown");

        parent::__construct($itemName, $lore, $nbt, $effect, $time, $cooldown, $receiver);

        $this->item = $this->createItem();
    }

    public function createItem(): Item
    {
        $item = VanillaItems::SNOWBALL();
        $item->setCustomName($this->itemName);

        $item->setLore($this->getLore());

        $nbt = $item->getNamedTag();
        $nbt->setTag($this->nbt, new CompoundTag());
        $item->setNamedTag($nbt);

        return $item;
    }

    public function apply(Player $receiver): void
    {
        $effectInstance = new EffectInstance($this->effect, $this->time);
        $this->createIcePrison($receiver);
        AbilityItem::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () {
            $this->removeIcePrison();
        }), $this->time);
        $receiver->getEffects()->add($effectInstance);
    }

    public function getItem(): Item {
        return $this->item;
    }

    private function createIcePrison(Player $player): void {
        $world = $player->getWorld();
        $center = $player->getPosition();
        $radius = 5;

        $this->iceBlocks = [];

        for ($x = -$radius; $x <= $radius; $x++) {
            for ($y = -$radius; $y <= $radius; $y++) {
                for ($z = -$radius; $z <= $radius; $z++) {
                    $distance = sqrt($x * $x + $y * $y + $z * $z);
                    if ($distance <= $radius && $distance >= $radius - 1) {
                        $position = new Position($center->getX() + $x, $center->getY() + $y, $center->getZ() + $z, $world);
                        $block = VanillaBlocks::ICE();
                        $world->setBlock($position, $block);
                        $this->iceBlocks[] = $position;
                    }
                }
            }
        }
    }

    private function removeIcePrison(): void {
        foreach ($this->iceBlocks as $position) {
            $position->getWorld()->setBlock($position, VanillaBlocks::AIR());
        }
        $this->iceBlocks = [];
    }
}
