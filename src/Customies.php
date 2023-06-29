<?php
declare(strict_types=1);

namespace customiesdevs\customies;

use customiesdevs\customies\block\CustomiesBlockFactory;
use customiesdevs\customies\util\Cache;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

final class Customies
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

    protected function onLoad(): void
    {
        Cache::setInstance(new Cache($this->pluginBase->getDataFolder() . "idcache", true));
    }

    protected function onEnable(): void
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
