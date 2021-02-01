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
        $this->config = new Config($this->getDataFolder() . "cooldown.yml", Config::YAML);
        $this->saveResource("cooldown.yml");
    }

    public function onConsume(PlayerItemConsumeEvent $event) {
        $player = $event->getPlayer();
        $gapple = $event->getItem();
        $yaml = new Config($this->getDataFolder() . "cooldown.yml", Config::YAML);
        if ((string) $yaml->get("enchanted-gapples") == true) {
            $gapple = Item::ENCHANTED_GOLDEN_APPLE;
        } else {
            $gapple = Item::GOLDEN_APPLE;
        }
        if ($event->getItem()->getId() == $gapple) {
            if ($player instanceof Player) {
                if (!isset(Main::$cooldown[$player->getName()])) {
                    Main::$cooldown[$player->getName()] = 1;
                    $yaml = new Config($this->getDataFolder() . "cooldown.yml", Config::YAML);
                    $timer = (int) $yaml->get("cooldown-length") + 1;
                    $this->getScheduler()->scheduleRepeatingTask(new CooldownTask($this, $player, $timer), 20);
                    $playerstartmsg = (string) $yaml->get("cooldown-enable");
                    $player->sendMessage($playerstartmsg);
                }
            }
        }
    }

    public function giveGapple(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        if ($player instanceof Player && isset(Main::$cooldown[$player->getName()])) {
            $yaml = new Config($this->getDataFolder() . "cooldown.yml", Config::YAML);
            if ((string) $yaml->get("enchanted-gapples") == true) {
                $gapple = Item::ENCHANTED_GOLDEN_APPLE;
            } else {
                $gapple = Item::GOLDEN_APPLE;
            }
            if ($event->getItem()->getId() == $gapple) {
                $event->setCancelled(true);
                $yaml = new Config($this->getDataFolder() . "cooldown.yml", Config::YAML);
                $msg = (string) $yaml->get("cooldown-message");
                $player->getLevel()->addSound(new BlazeShootSound($player));
                $player->sendMessage($msg);
            }
        }
    }
}