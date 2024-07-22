<?php

namespace candle\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class renameCommand extends Command
{


    public function __construct() {
        parent::__construct("rename");
        $this->setPermission(DefaultPermissions::ROOT_USER);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;

        $itemInHand = $sender->getInventory()->getItemInHand();

        if(!isset($args[0])) {
            $sender->sendMessage("wtf?");
            return;
        }

        $customName = implode(" ", $args);
        $itemInHand->setCustomName($customName);


        $sender->getInventory()->setItemInHand($itemInHand);

        $sender->sendMessage("Item renamed to: " . $customName);
    }
}