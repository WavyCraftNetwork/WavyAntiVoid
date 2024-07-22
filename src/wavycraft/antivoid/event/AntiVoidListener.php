<?php

declare(strict_types=1);

namespace wavycraft\antivoid\event;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use wavycraft\antivoid\AntiVoid;

class AntiVoidListener implements Listener {

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $worldName = $player->getWorld()->getFolderName();
        $y = $event->getTo()->getY();

        $allowedWorlds = AntiVoid::getInstance()->getAllowedWorlds();
        if (in_array($worldName, $allowedWorlds, true) && $y < 0) {
            $saveManager = AntiVoid::getInstance()->getSaveManager();
            $savesLeft = $saveManager->getSaves($player);

            if ($savesLeft > 0) {
                $saveManager->useSave($player);
                $savesLeft--;
                $player->teleport($player->getWorld()->getSpawnLocation());
                $player->sendMessage("Teleported to a safe location!");
                $player->sendSubtitle("Be careful next time!");
                $player->sendToastNotification("§l§eSaves Remaining", "You have §e{$savesLeft}§r§f more saves left!");
            } else {
                $player->sendMessage("You have no saves left, Falling into the void!");
            }
        }
    }
}
