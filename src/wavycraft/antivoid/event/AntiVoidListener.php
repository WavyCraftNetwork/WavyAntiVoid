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

        $antiVoid = AntiVoid::getInstance();
        $allowedWorlds = $antiVoid->getAllowedWorlds();
        $enableAllWorlds = $antiVoid->isEnableAllWorlds();

        if (($enableAllWorlds || in_array($worldName, $allowedWorlds, true)) && $y < 0) {
            $saveManager = $antiVoid->getSaveManager();
            $savesLeft = $saveManager->getSaves($player);

            if ($savesLeft > 0) {
                $saveManager->useSave($player);
                $savesLeft--;
                $player->teleport($player->getWorld()->getSpawnLocation());
                
                $message = $antiVoid->getMessages()->get("teleportation_message");
                $message = str_replace("{saves_left}", (string)$savesLeft, $message);
                $player->sendMessage($message);
                
                $toastTitle = $antiVoid->getMessages()->get("teleportation_toast_title");
                $toastBody = str_replace("{saves_left}", (string)$savesLeft, $antiVoid->getMessages()->get("teleportation_toast_body"));
                $player->sendToastNotification($toastTitle, $toastBody);
            } else {
                $player->sendMessage($antiVoid->getMessages()->get("teleportation_failed"));
            }
        }
    }
}
