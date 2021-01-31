<?php

declare(strict_types=1);

namespace EerieDev;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\item\GoldenApple;
use pocketmine\item\Item;
use pocketmine\level\sound\BlazeShootSound;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use EerieDev\task\CooldownTask;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    private $main;
    public $config;
    public static $cooldown = [];

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getLogger()->info("Gapple-CD by EerieDev enabled.");
        $this->saveResource("cooldown.yml");
        $this->config = new Config($this->getDataFolder() . "cooldown.yml", Config::YAML);
    }

    public function onConsume(PlayerItemConsumeEvent $event) {
        $player = $event->getPlayer();
        $gapple = $event->getItem();
        if ($event->getItem()->getId() == Item::ENCHANTED_GOLDEN_APPLE) {
            if ($player instanceof Player) {
                if (!isset(Main::$cooldown[$player->getName()])) {
                    Main::$cooldown[$player->getName()] = 1;
                    $timer = 31;
                    $this->getScheduler()->scheduleRepeatingTask(new CooldownTask($this, $player, $timer), 20);
                }
            }
        }
    }

    public function giveGapple(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        if ($player instanceof Player && isset(Main::$cooldown[$player->getName()])) {
            if ($event->getItem()->getId() == Item::ENCHANTED_GOLDEN_APPLE) {
                $event->setCancelled(true);
                $player->sendMessage("§cYou are currently in a gapple cooldown.");
                $player->getLevel()->addSound(new BlazeShootSound($player));
            }
        }
    }
}