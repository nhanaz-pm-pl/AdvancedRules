<?php

declare(strict_types=1);

namespace NhanAZ\AdvancedRules;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Toggle;
use dktapps\pmforms\CustomFormResponse;

class Main extends PluginBase implements Listener {

	protected Config $data;

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->data = new Config($this->getDataFolder() . "Data.json", Config::JSON);
	}

	private function checkData(Player $player, bool $kickMode = true): void {
		if (!$this->data->exists($player->getName())) {
			if ($this->getConfig()->get("KickMode", true)) {
				if ($kickMode) {
					$player->kick($this->getConfig()->get("KickMessage"));
				}
			}
			$player->sendForm($this->advancedRulesForm());
		}
	}

	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$this->checkData($player, false);
	}

	private function advancedRulesForm(): CustomForm {
		return new CustomForm(
			$this->getConfig()->get("TitleForm", "AdvancedRules"),
			[
				new Label("content", $this->getConfig()->get("Rules")),
				new Toggle("switch", $this->getConfig()->get("AgreeButon"), false)
			],
			function (Player $submitter, CustomFormResponse $response): void {
				if ($response->getBool("switch")) {
					$this->data->set($submitter->getName(), true);
					$this->data->save();
					$submitter->sendMessage($this->getConfig()->get("AgreedMessage"));
				}
				$this->checkData($submitter, true);
			},
			function (Player $submitter): void {
				$this->checkData($submitter, false);
			}
		);
	}
}
