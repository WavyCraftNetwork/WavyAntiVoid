<?php

declare(strict_types=1);

namespace wavycraft\antivoid\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use wavycraft\antivoid\AntiVoid;
use jojoe77777\FormAPI\CustomForm;
use Closure;

class PurchaseCommand extends Command {

    public function __construct() {
        parent::__construct("buysaves", "Purchase additional saves", "/buysaves", ["purchasesaves", "saveshop", "sshop"]);
        $this->setPermission("wavyantivoid.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(AntiVoid::getInstance()->getMessages()->get("player_only"));
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        $this->sendPurchaseForm($sender);
        return true;
    }

    private function sendPurchaseForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return;
            }

            $amount = intval($data[1]);
            if ($amount <= 0) {
                $player->sendMessage(AntiVoid::getInstance->getMessages()->get("numbers_only"));
                return;
            }

            $costPerSave = AntiVoid::getInstance()->getConfig()->get("saves_price");
            $totalCost = $amount * $costPerSave;

            $economyManager = AntiVoid::getInstance()->getEconomyManager();

            $economyManager->getMoney($player, function($balance) use ($player, $amount, $totalCost, $economyManager) {
                if ($balance < $totalCost) {
                    $player->sendMessage(AntiVoid::getInstance->getMessages()->get(str_replace(["{saves_amount}", "{total_cost}"], [(string)$amount, (string)$totalCost], "insignificant_balance")));
                    return;
                }

                $economyManager->reduceMoney($player, $totalCost, function(bool $success) use ($player, $amount, $totalCost) {
                    if ($success) {
                        $saveManager = AntiVoid::getInstance()->getSaveManager();
                        $saveManager->addSaves($player, $amount);
                        $player->sendMessage(AntiVoid::getInstance->getMessages()->get(str_replace(["{saves_bought}", "{total_cost}"], [(string)$amount, (string)$totalCost], "purchase_successful")));
                    } else {
                        $player->sendMessage(AntiVoid::getInstance->getMessages()->get("purchase_failed"));
                    }
                });
            });
        });

        $price = AntiVoid::getInstance()->getConfig()->get("saves_price");
        $form->setTitle(AntiVoid::getInstance->getMessages()->get("form_title"));
        $form->addLabel(AntiVoid::getInstance->getMessages()->get(str_replace("{price}", (string)$price, "form_label")));
        $form->addInput(AntiVoid::getInstance->getMessages()->get("form_input_1"), AntiVoid::getInstance->getMessages()->get("form_input_2"));
        $player->sendForm($form);
    }
}
