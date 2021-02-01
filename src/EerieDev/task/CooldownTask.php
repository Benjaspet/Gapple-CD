<?php

namespace EerieDev\task;

use EerieDev\Main;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class CooldownTask extends Task {

    private $plugin;
    private $player;
    private $timer;

    public function __construct(Main $plugin, Player $player, int $timer) {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->timer = $timer;
    }

    public function onRun(int $currentTick) {
        if ($this->player->isOnline()) {
            $this->timer--;
            $yaml = new Config($this->plugin->getDataFolder() . "cooldown.yml", Config::YAML);
            $int = $yaml->get("cooldown-length");
            if ($this->timer == $int) {
                $yaml = new Config($this->plugin->getDataFolder() . "cooldown.yml", Config::YAML);
                if ((string) $yaml->get("xpbar-enabled") == true) $this->player->setXpLevel($this->timer);
            }
            if ($this->timer < $int) {
                $this->player->setXpLevel($this->timer);
            }
            if ($this->timer <= 0) {
                $this->player->setXpLevel(0);
                unset(Main::$cooldown[$this->player->getName()]);
                $this->plugin->getScheduler()->cancelTask($this->getTaskId());
                $yaml = new Config($this->plugin->getDataFolder() . "cooldown.yml", Config::YAML);
                $endmsg = (string) $yaml->get("cooldown-end");
                $this->player->sendMessage($endmsg);
            }
        }
    }
}
