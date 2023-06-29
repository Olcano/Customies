<?php
declare(strict_types=1);

namespace Customies;

use Customies\block\CustomiesBlockFactory;
use Customies\util\Cache;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class Customies
{

    private PluginBase $pluginBase;

    public function __construct(PluginBase $pluginBase)
    {
        $this->pluginBase = $pluginBase;
    }

    public static function make(PluginBase $pluginBase): self
    {
        return new Customies($pluginBase);
    }

    public function load(): void
    {
        Cache::setInstance(new Cache($this->pluginBase->getDataFolder() . "idcache", true));
    }

    public function enable(): void
    {
        $this->pluginBase->getServer()->getPluginManager()->registerEvents(new CustomiesListener(), $this->pluginBase);

        $cachePath = $this->pluginBase->getDataFolder() . "idcache";
        $this->pluginBase->getScheduler()->scheduleDelayedTask(new ClosureTask(static function () use ($cachePath): void {
            // This task is scheduled with a 0-tick delay so it runs as soon as the server has started. Plugins should
            // register their custom blocks and entities in onEnable() before this is executed.
            Cache::getInstance()->save();
            CustomiesBlockFactory::getInstance()->addWorkerInitHook($cachePath);
        }), 0);
    }
}
