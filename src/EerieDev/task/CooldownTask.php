<?php

namespace EerieDev\task;

use EerieDev\Main;
use pocketmine\Player;
use pocketmine\scheduler\Task;

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
            if ($this->timer == 30) {
                $this->player->setXpLevel($this->timer);
            }
            if ($this->timer < 30) {
                $this->player->setXpLevel($this->timer);
            }
            if ($this->timer <= 0) {
                $this->player->setXpLevel(0);
                unset(Main::$cooldown[$this->player->getName()]);
                $this->plugin->getScheduler()->cancelTask($this->getTaskId());
                $this->player->sendMessage("§aGapple cooldown ended.");
            }
        }
    }
}
