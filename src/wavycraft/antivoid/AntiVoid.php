<?php

declare(strict_types=1);

namespace wavycraft\antivoid;

use pocketmine\plugin\PluginBase;
use wavycraft\antivoid\event\AntiVoidListener;
use wavycraft\antivoid\command\PurchaseCommand;
use wavycraft\antivoid\economy\EconomyManager;

class AntiVoid extends PluginBase {

    private static self $instance;
    private $saveManager;
    private $economyManager;

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onEnable() : void{
        $this->saveDefaultConfig();
        $maxSaves = (int) $this->getConfig()->get("max_saves");
        $this->saveManager = new SaveManager($this->getDataFolder(), $maxSaves);
        $this->economyManager = new EconomyManager($this);
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
}
