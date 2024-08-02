<?php

namespace candle\types;

use candle\AbilityItem;
use candle\ItemManager;
use candle\managers\ItemRegistry;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\Position;

class PrisonAbility extends ItemManager
{

    private Item $item;
    private array $jailBlocks = [];

    public static function init(): void
    {
        ItemRegistry::registerItem("Prison", PrisonAbility::class);
    }

    public function __construct(Player $receiver)
    {
        $itemName = "§cJail Trap";
        $nbt = "jailtrap";
        $effect = VanillaEffects::SLOWNESS();
        $time = AbilityItem::getInstance()->getConfig()->get("PrisonDuration");
        $cooldown = AbilityItem::getInstance()->getConfig()->get("PrisonCooldown");

        parent::__construct($itemName, $nbt, $effect, $time, $cooldown, $receiver);

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
        $this->createJailAroundPlayer($receiver);
        AbilityItem::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () {
            $this->removeJailAroundPlayer();
        }), 20 * 30); //not sure if i want this to be in config maybe later
        $receiver->getEffects()->add($effectInstance);
    }

    public function getItem(): Item {
        return $this->item;
    }

    private function createJailAroundPlayer(Player $player): void {
        $world = $player->getWorld();
        $center = $player->getPosition();
        $radius = 5;
        $height = 8;

        $this->jailBlocks = [];

        for ($x = -$radius; $x <= $radius; $x++) {
            for ($z = -$radius; $z <= $radius; $z++) {
                for ($y = 0; $y < $height; $y++) {
                    $position = new Position($center->getX() + $x, $center->getY() + $y, $center->getZ() + $z, $world);
                    if ($x === -$radius || $x === $radius || $z === -$radius || $z === $radius) {
                        if ($y < 7) {
                            $block = VanillaBlocks::IRON_BARS();
                        } else {
                            $block = VanillaBlocks::OBSIDIAN();
                        }
                        $world->setBlock($position, $block);
                        $this->jailBlocks[] = $position;
                    }
                }
                if ($x === -$radius || $x === $radius || $z === -$radius || $z === $radius) {
                    $yBelow = -1;
                    while ($world->getBlock(new Position($center->getX() + $x, $center->getY() + $yBelow, $center->getZ() + $z, $world))->getTypeId() === BlockTypeIds::AIR) {
                        $positionBelow = new Position($center->getX() + $x, $center->getY() + $yBelow, $center->getZ() + $z, $world);
                        $world->setBlock($positionBelow, VanillaBlocks::IRON_BARS());
                        $this->jailBlocks[] = $positionBelow;
                        $yBelow--;
                    }
                }
            }
        }
    }

    private function removeJailAroundPlayer(): void {
        foreach ($this->jailBlocks as $position) {
            $position->getWorld()->setBlock($position, VanillaBlocks::AIR());
        }
        $this->jailBlocks = [];
    }

}
