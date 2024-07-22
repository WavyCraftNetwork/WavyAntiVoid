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
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
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
                $player->sendMessage(TextFormat::RED . "Invalid amount entered.");
                return;
            }

            $costPerSave = 100;
            $totalCost = $amount * $costPerSave;

            $economyManager = AntiVoid::getInstance()->getEconomyManager();

            $economyManager->getMoney($player, function($balance) use ($player, $amount, $totalCost, $economyManager) {
                if ($balance < $totalCost) {
                    $player->sendMessage(TextFormat::RED . "You do not have enough money to buy $amount saves. Total cost: $totalCost");
                    return;
                }

                $economyManager->reduceMoney($player, $totalCost, function(bool $success) use ($player, $amount, $totalCost) {
                    if ($success) {
                        $saveManager = AntiVoid::getInstance()->getSaveManager();
                        $saveManager->addSaves($player, $amount);
                        $player->sendMessage(TextFormat::GREEN . "You have successfully purchased §e{$amount}§f saves for a total cost of§a $" . $totalCost);
                    } else {
                        $player->sendMessage(TextFormat::RED . "Transaction failed. Please try again.");
                    }
                });
            });
        });

        $form->setTitle("Buy Saves");
        $form->addLabel("Purchase additional saves, Each save costs §la$100");
        $form->addInput("Enter the number of saves you want to buy:", "Amount of saves");
        $player->sendForm($form);
    }
}