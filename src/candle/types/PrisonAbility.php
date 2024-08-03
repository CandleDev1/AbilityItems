<?php

namespace candle\types;

use candle\AbilityItem;
use candle\ItemManager;
use candle\managers\ItemRegistry;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\utils\MobHeadType;
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
    private array $originalBlocks = [];

    public static function init(): void
    {
        ItemRegistry::registerItem("Prison", PrisonAbility::class);
    }

    public function __construct(Player $receiver)
    {
        $itemName = "§l§cCrimson Cage§r";
        $lore = [
            "§7Those who find themselves ensnared by this trap\nwill face the unyielding walls of their own misdeeds.",
            " ",
            "§l§eAbility §r",
            "§c* §7Slowness for 30 seconds",
            "§c* §7Crimson Cage 30 seconds"
        ];
        $nbt = "CrimsonCage_AbilityItem";
        $effect = VanillaEffects::SLOWNESS();
        $time = AbilityItem::getInstance()->getConfig()->get("PrisonDuration");
        $cooldown = AbilityItem::getInstance()->getConfig()->get("PrisonCooldown");

        parent::__construct($itemName, $lore, $nbt, $effect, $time, $cooldown, $receiver);

        $this->item = $this->createItem();
    }

    public function createItem(): Item
    {
        $item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON)->asItem();
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
        $this->createJailPlayer($receiver);
        AbilityItem::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () {
            $this->removeJailPlayer();
        }), 20 * 30);
        $receiver->getEffects()->add($effectInstance);
    }

    public function getItem(): Item {
        return $this->item;
    }

    private function createJailPlayer(Player $player): void {
        $world = $player->getWorld();
        $center = $player->getPosition();
        $radius = 5;
        $height = 8;

        $this->jailBlocks = [];
        $this->originalBlocks = [];

        for ($x = -$radius; $x <= $radius; $x++) {
            for ($z = -$radius; $z <= $radius; $z++) {
                for ($y = 0; $y < $height; $y++) {
                    $position = new Position($center->getX() + $x, $center->getY() + $y, $center->getZ() + $z, $world);

                    $originalBlock = $world->getBlock($position);
                    $this->originalBlocks[$position->asVector3()->__toString()] = $originalBlock;
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

    private function removeJailPlayer(): void {
        foreach ($this->jailBlocks as $position) {
            $world = $position->getWorld();
            $originalBlock = $this->originalBlocks[$position->asVector3()->__toString()] ?? VanillaBlocks::AIR();
            $position->getWorld()->setBlock($position, $originalBlock);
        }
        $this->jailBlocks = [];
        $this->originalBlocks = [];
    }

}
