<?php

declare(strict_types=1);

namespace wavycraft\antivoid;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use wavycraft\antivoid\event\AntiVoidListener;
use wavycraft\antivoid\command\PurchaseCommand;
use wavycraft\antivoid\economy\EconomyManager;

class AntiVoid extends PluginBase {

    public static self $instance;
    private $saveManager;
    private $economyManager;
    private array $allowedWorlds;
    private $messages;
    private bool $enableAllWorlds;

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onEnable() : void{
        $this->saveResource("messages.yml");
        $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        $this->saveDefaultConfig();
        $maxSaves = (int) $this->getConfig()->get("starting_saves");
        $this->saveManager = new SaveManager($this->getDataFolder(), $maxSaves);
        $this->economyManager = new EconomyManager($this);
        $this->allowedWorlds = $this->getConfig()->get("worlds", []);
        $this->enableAllWorlds = $this->getConfig()->get("enable_all_worlds", false);
        $this->getServer()->getPluginManager()->registerEvents(new AntiVoidListener(), $this);
        $this->getServer()->getCommandMap()->register("buysaves", new PurchaseCommand());
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getSaveManager() : SaveManager{
        return $this->saveManager;
    }

    public function getEconomyManager() : EconomyManager{
        return $this->economyManager;
    }

    public function getAllowedWorlds() : array{
        return $this->allowedWorlds;
    }

    public function isEnableAllWorlds() : bool{
        return $this->enableAllWorlds;
    }

    public function getMessages() : Config{
        return $this->messages;
    }
}
