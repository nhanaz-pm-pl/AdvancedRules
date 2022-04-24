<?php

declare(strict_types=1);

namespace NhanAZ\AdvancedRules;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use dktapps\pmforms\CustomForm;
use NhanAZ\AdvancedRules\libs\dktapps\pmforms\element\Label;
use NhanAZ\AdvancedRules\libs\dktapps\pmforms\element\Toggle;
use NhanAZ\AdvancedRules\libs\dktapps\pmforms\CustomFormResponse;

class Main extends PluginBase implements Listener {

	protected Config $cfg;

	protected Config $data;

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->data = new Config($this->getDataFolder() . "Data.json", Config::JSON);
		$this->cfg = $this->getConfig();
	}

	private function CheckData(Player $player): void {
		if (!$this->data->exists($player->getName())) {
			$player->sendForm($this->AdvancedRulesForm());
		}
	}

	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$this->CheckData($player);
	}

	private function AdvancedRulesForm(): CustomForm {
		return new CustomForm(
			TextFormat::colorize($this->cfg->get("TitleForm")),
			[
				new Label("content", TextFormat::colorize($this->cfg->get("Rules"))),
				new Toggle("switch", TextFormat::colorize($this->cfg->get("AgreeButon")), false)
			],
			function (Player $submitter, CustomFormResponse $response): void {
				if ($response->getBool("switch")) {
					$this->data->set($submitter->getName(), true);
					$this->data->save();
					$submitter->sendMessage(TextFormat::colorize($this->cfg->get("AgreedMessage")));
				}
				$this->CheckData($submitter);
			},
			function (Player $submitter): void {
				$this->CheckData($submitter);
			}
		);
	}
}
