<?php

namespace candle\commands;


use candle\commands\forms\AbilityForm;
use candle\managers\ItemFactory;
use candle\managers\ItemRegistry;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class AbilityCommand extends Command
{
    public function __construct() {
        parent::__construct("ability");
        $this->setUsage('/ability <type>');
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;
        if (!$sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            $sender->sendMessage("§cYou have no permission to execute this command");
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->getUsage());
            return;
        }

        $type = $args[0];
        $knownTypes = ItemRegistry::getItems();
        $abilityItem = ItemFactory::createItem($type, $sender);

        if ($abilityItem !== null) {
            $sender->getInventory()->addItem($abilityItem->getItem());
        } else {
            $sender->sendMessage("§cUnknown ability type: $type");
            $sender->sendMessage("§aKnown Ability type: " . implode(", ", array_keys($knownTypes)));
        }
    }
}
