<?php

declare(strict_types=1);

namespace wavycraft\antivoid;

use pocketmine\plugin\PluginBase;
use wavycraft\antivoid\event\AntiVoidListener;
use wavycraft\antivoid\SaveManager;

class AntiVoid extends PluginBase {

    private static self $instance;
    private $saveManager;

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onEnable() : void{
        $this->saveDefaultConfig();
        $maxSaves = (int) $this->getConfig()->get("max_saves");
        $this->saveManager = new SaveManager($this->getDataFolder(), $maxSaves);
        $this->getServer()->getPluginManager()->registerEvents(new AntiVoidListener(), $this);
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getSaveManager() : SaveManager{
        return $this->saveManager;
    }
}