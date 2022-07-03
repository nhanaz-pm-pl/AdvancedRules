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
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Toggle;
use dktapps\pmforms\CustomFormResponse;

class Main extends PluginBase implements Listener {

	protected Config $cfg;

	protected Config $data;

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->data = new Config($this->getDataFolder() . "Data.json", Config::JSON);
		$this->cfg = $this->getConfig();
	}

	private function CheckData(Player $player, bool $kickMode = true): void {
		if (!$this->data->exists($player->getName())) {
			if ($this->getConfig()->get("KickMode", true)) {
				if ($kickMode) {
					$player->kick(TextFormat::colorize($this->getConfig()->get("KickMessage", "&e» &cPlease agree to the server's rules!")));
				}
			}
			$player->sendForm($this->AdvancedRulesForm());
		}
	}

	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$this->CheckData($player, false);
	}

	private function AdvancedRulesForm(): CustomForm {
		return new CustomForm(
			TextFormat::colorize($this->cfg->get("TitleForm", "AdvancedRules")),
			[
				new Label("content", TextFormat::colorize($this->cfg->get("Rules", "&8» &rAdvancedRules by NhanAZ.\n&8» &rEdit the &6config.yml &rFile and restart server."))),
				new Toggle("switch", TextFormat::colorize($this->cfg->get("AgreeButon", "I agree")), false)
			],
			function (Player $submitter, CustomFormResponse $response): void {
				if ($response->getBool("switch")) {
					$this->data->set($submitter->getName(), true);
					$this->data->save();
					$submitter->sendMessage(TextFormat::colorize($this->cfg->get("AgreedMessage", "[&2ServerName&f]&8»&aYou accepted the rules, have fun!")));
				}
				$this->CheckData($submitter, true);
			},
			function (Player $submitter): void {
				$this->CheckData($submitter, false);
			}
		);
	}
}
