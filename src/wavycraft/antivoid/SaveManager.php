<?php

declare(strict_types=1);

namespace wavycraft\antivoid;

use pocketmine\player\Player;
use pocketmine\utils\Config;

class SaveManager {
    private $config;
    private int $maxSaves;

    public function __construct(string $dataFolder, int $maxSaves) {
        $this->maxSaves = $maxSaves;
        $this->config = new Config($dataFolder . "data.json", Config::JSON);

        foreach ($this->config->getAll() as $playerName => $saves) {
            if (!is_int($saves)) {
                $this->config->set($playerName, $this->maxSaves);
            }
        }
        $this->config->save();
    }

    public function getSaves(Player $player): int {
        return $this->config->get($player->getName(), $this->maxSaves);
    }

    public function useSave(Player $player): void {
        $playerName = $player->getName();
        $saves = $this->getSaves($player) - 1;
        $this->config->set($playerName, $saves);
        $this->config->save();
    }

    public function resetSaves(Player $player): void {
        $this->config->set($player->getName(), $this->maxSaves);
        $this->config->save();
    }
}