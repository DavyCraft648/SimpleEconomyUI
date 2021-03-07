<?php

/**
 * Plugin By DontTouchMeXD (RicardoMilos384)
 * Don't Edit This Plugin
 * _____________________
 * XD     XD  XDXDXDX
 *  XD   XD   XD    XD
 *   XD-XD    XD     XD
 *  XD   XD   XD    XD
 * XD     XD  XDXDXDX
 * —————————————————————
 * Copyright © by RicardoMilos384
 * Github: https://github.com/RicardoMilos384
 * Ok Thanks For Your Respect
 */

namespace RicardoMilos384\SimpleEconomyUI;

use jojoe77777\FormAPI\{CustomForm, SimpleForm};
use onebone\economyapi\EconomyAPI;
use onebone\economyapi\event\money\PayMoneyEvent;
use pocketmine\command\{Command, CommandSender};
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase
{
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		if ($sender instanceof Player) {
			if (isset($args[0])) {
				switch (strtolower($args[0])) {
					case "mymoney":
						$this->myMoneyForm($sender);
						break;
					case "othersmoney":
						$this->otherMoneyForm($sender);
						break;
					case "payplayer":
						$this->payPlayerForm($sender);
						break;
					case "topmoney":
						$this->topMoneyForm($sender);
						break;
					default:
						$this->mainForm($sender);
				}
			} else $this->mainForm($sender);
		} else $sender->sendMessage("Copyright © by RicardoMilos384\nGithub: https://github.com/RicardoMilos");
		return true;
	}

	/**
	 * Get EconomyAPI instance
	 * @return EconomyAPI
	 */
	public function getEconomyAPI(): EconomyAPI
	{
		return EconomyAPI::getInstance();
	}

	/**
	 * Open SimpleEconomyUI Main form
	 * @param Player $player
	 */
	public function mainForm(Player $player)
	{
		$form = new SimpleForm(function (Player $player, $data) {
			switch ($data) {
				case "MyMoney":
					$this->myMoneyForm($player);
					break;
				case "OthersMoney":
					$this->otherMoneyForm($player);
					break;
				case "PayPlayer":
					$this->payPlayerForm($player);
					break;
				case "TopMoney":
					$this->topMoneyForm($player);
			}
		});
		$name = $player->getName();
		$form->setTitle(TextFormat::DARK_GREEN."EconomyUI");
		$form->setContent(TextFormat::GREEN."Hello ".TextFormat::AQUA.$name.TextFormat::GREEN.", May I help you?");
		$form->addButton(TextFormat::DARK_GRAY."My Money\n".TextFormat::GRAY."See your money", -1, "", "MyMoney");
		$form->addButton(TextFormat::DARK_GRAY."Player Money\n".TextFormat::GRAY."See other player's money", -1, "", "OthersMoney");
		$form->addButton(TextFormat::DARK_GRAY."Pay Player\n".TextFormat::GRAY."Pay another player", -1, "", "PayPlayer");
		$form->addButton(TextFormat::DARK_GRAY."Top Money\n".TextFormat::GRAY."Top richest players", -1, "", "TopMoney");
		$form->addButton(TextFormat::RED."Exit\n".TextFormat::GRAY."Tap to close");
		$player->sendForm($form);
	}

	/**
	 * Open MyMoney form
	 * @param Player $player
	 */
	public function myMoneyForm(Player $player)
	{
		$form = new SimpleForm(function (Player $player, $data) {
			if ($data === 0) $this->mainForm($player);
		});
		$name = $player->getName();
		$money = $this->getEconomyAPI()->myMoney($name);
		$form->setTitle(TextFormat::DARK_GREEN."My Money");
		$form->setContent(TextFormat::GREEN."Hello ".TextFormat::AQUA.$name.TextFormat::GREEN.",\nYour money is".TextFormat::GRAY.": ".TextFormat::AQUA."$money\n\n\n\n\n");
		$form->addButton(TextFormat::RED."Back");
		$player->sendForm($form);
	}

	/**
	 * Open PayPlayer form
	 * @param Player $player
	 */
	public function payPlayerForm(Player $player)
	{
		$form = new CustomForm(function (Player $player, array $data = null) {
			if (is_null($data)) return;
			$this->payPlayerSubForm($player, $data["TargetName"], floatval($data["Amount"]));
		});
		$form->setTitle(TextFormat::DARK_GREEN."Pay Player");
		$form->addInput(TextFormat::YELLOW."Enter player's name", "DontTouchMeXD", null, "TargetName");
		$form->addInput(TextFormat::YELLOW."Enter amount of money", "10000", null, "Amount");
		$player->sendForm($form);
	}

	private function payPlayerSubForm(Player $payer, string $targetName, float $amount)
	{
		$target = $this->getServer()->getPlayer($targetName);
		$payerName = $payer->getName();
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		$form->setTitle(TextFormat::DARK_GREEN."Pay Player");
		if (trim($targetName) === "") $form->setContent(TextFormat::RED."Player name can not be empty\n\n\n\n\n\n");
		elseif (strtolower($payerName) === strtolower($targetName)) $form->setContent(TextFormat::RED."You can not pay yourself\n\n\n\n\n\n");
		elseif (!$this->getEconomyAPI()->accountExists($targetName)) $form->setContent(TextFormat::RED."That player is never connected before!\n\n\n\n\n\n");
		elseif (is_null($target) and $this->getEconomyAPI()->getConfig()->get("allow-pay-offline", true) === false)
			$form->setContent(TextFormat::RED."You are not allowed to pay offline player\n\n\n\n\n\n");
		elseif (!is_numeric($amount)) $form->setContent(TextFormat::RED."Money amount must be a number\n\n\n\n\n\n");
		elseif ($amount <= 0) $form->setContent(TextFormat::RED."Money amount can not less or equal to 0\n\n\n\n\n\n");
		else {
			$form->setTitle(TextFormat::GREEN."Pay to $targetName");
			$event = new PayMoneyEvent($this->getEconomyAPI(), $payerName, $targetName, $amount);
			$event->call();
			$result = EconomyAPI::RET_CANCELLED;
			if (!$event->isCancelled()) $result = $this->getEconomyAPI()->reduceMoney($payer, $amount);
			if ($result === EconomyAPI::RET_SUCCESS) {
				$this->getEconomyAPI()->addMoney($targetName, $amount, true);
				$form->setContent(TextFormat::GREEN."Successfully paid ".TextFormat::GOLD.$amount.TextFormat::GREEN." to ".TextFormat::GOLD.$targetName."\n\n\n\n\n\n");
				if (!is_null($target)) $target->sendMessage($this->getEconomyAPI()->getMessage("money-paid", [$payerName, $amount], $payerName));
			}
			else $form->setContent(TextFormat::RED."Failed to paid ".TextFormat::GOLD.$amount.TextFormat::RED." to ".TextFormat::GOLD.$targetName."\n\n\n\n\n\n");
		}
		$form->addButton(TextFormat::RED."Back");
		$payer->sendForm($form);
	}

	/**
	 * Open TopMoney form
	 * @param Player $player
	 */
	public function topMoneyForm(Player $player) {
		$form = new CustomForm(function (Player $player, array $data = null) {
			if (is_null($data)) return;
			$this->topMoneySubForm($player, intval($data["TotalPlayers"]));
		});
		$form->setTitle(TextFormat::DARK_GREEN."Top Money");
		$form->addInput(TextFormat::YELLOW."Total player to display", "10", null, "TotalPlayers");
		$player->sendForm($form);
	}

	private function topMoneySubForm(Player $player, $total) {
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		$topMoney = $this->getEconomyAPI()->getAllMoney();
		$content = "";
		if ($total >= 1 and $total <= count($topMoney)) {
			if (count($topMoney) > 0) {
				arsort($topMoney);
				$i = 0;
				foreach ($topMoney as $name => $money) {
					$content .= TextFormat::GREEN.($i + 1).". ".TextFormat::AQUA.$name.TextFormat::GRAY.": ".TextFormat::AQUA.$money."\n";
					if ($i >= $total) {
						break;
					}
					$i++;
				}
			}
		} else $content = TextFormat::RED . "Some error occurred\n\n\n\n\n\n";
		$form->setTitle(TextFormat::DARK_GREEN."Top Money");
		$form->setContent($content);
		$form->addButton(TextFormat::RED . "Back");
		$player->sendForm($form);
	}

	/**
	 * Open OtherPlayerMoney form
	 * @param Player $player
	 */
	public function otherMoneyForm(Player $player)
	{
		$form = new CustomForm(function (Player $player, array $data = null) {
			if (isset($data["TargetName"])) $this->otherMoneySubForm($player, $data["TargetName"]);
		});
		$form->setTitle(TextFormat::DARK_GREEN."Other Player's Money");
		$form->addInput(TextFormat::YELLOW."Enter player's name", "DontTouchMeXD", null, "TargetName");
		$player->sendForm($form);
	}

	private function otherMoneySubForm(Player $player, string $target)
	{
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		if (trim($target) === "") {
			$form->setTitle(TextFormat::DARK_GREEN."Other Player's Money");
			$form->setContent(TextFormat::GOLD.$target.TextFormat::RED."Player name can not be empty\n\n\n\n\n\n");
		}
		elseif (!$this->getEconomyAPI()->accountExists($target)) {
			$form->setTitle(TextFormat::DARK_GREEN."Other Player's Money");
			$form->setContent(TextFormat::GOLD.$target.TextFormat::RED."Account not found\n\n\n\n\n\n");
		} else {
			$money = $this->getEconomyAPI()->myMoney($target);
			$form->setTitle(TextFormat::DARK_GREEN."$target's Money");
			$form->setContent(TextFormat::AQUA.$target.TextFormat::GREEN." has ".TextFormat::AQUA.$money.TextFormat::GREEN." money\n\n\n\n\n");
		}
		$form->addButton(TextFormat::RED."Back");
		$player->sendForm($form);
	}
}
