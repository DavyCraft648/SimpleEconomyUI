<?php

/**
 * Original plugin by DontTouchMeXD (RicardoMilos384)
 * Maintained by DavyCraft648
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
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\TextFormat;

class Main extends PluginBase
{
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		if ($sender instanceof Player) {
			if (isset($args[0])) {
				switch (strtolower($args[0])) {
					case "givemoney":
						if ($this->checkPerm($sender, "givemoney")) $this->giveMoneyForm($sender);
						else $this->mainForm($sender);
						break;
					case "mymoney":
						if ($this->checkPerm($sender, "mymoney")) $this->myMoneyForm($sender);
						else $this->mainForm($sender);
						break;
					case "mystatus":
						if ($this->checkPerm($sender, "mystatus")) $this->myStatusForm($sender);
						else $this->mainForm($sender);
						break;
					case "payplayer":
					case "pay":
						if ($this->checkPerm($sender, "pay")) $this->payForm($sender);
						else $this->mainForm($sender);
						break;
					case "seemoney":
						if ($this->checkPerm($sender, "seemoney")) $this->seeMoneyForm($sender);
						else $this->mainForm($sender);
						break;
					case "setlang":
						if ($this->checkPerm($sender, "setlang")) $this->setLangForm($sender);
						else $this->mainForm($sender);
						break;
					case "setmoney":
						if ($this->checkPerm($sender, "setmoney")) $this->setMoneyForm($sender);
						else $this->mainForm($sender);
						break;
					case "takemoney":
						if ($this->checkPerm($sender, "takemoney")) $this->takeMoneyForm($sender);
						else $this->mainForm($sender);
						break;
					case "topmoney":
						if ($this->checkPerm($sender, "topmoney")) $this->topMoneyForm($sender);
						else $this->mainForm($sender);
						break;
					default:
						$this->mainForm($sender);
				}
			} else $this->mainForm($sender);
		} else $sender->sendMessage("Copyright © by RicardoMilos384\nGithub: https://github.com/RicardoMilos");
		return true;
	}

	/**
	 * Check player permission
	 * @param Player $player
	 * @param string $command
	 * @return bool
	 */
	public function checkPerm(Player $player, string $command): bool
	{
		if ($player->hasPermission("economyapi.*")
			or $player->hasPermission("economyapi.command.*")
			or $player->hasPermission("economyapi.command.$command")
		) return true;
		return false;
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
	 * Get description of economy command
	 * @param string $command
	 * @return string
	 */
	private function getCommandDesc(string $command): string
	{
		return $this->getEconomyAPI()->getCommandMessage($command)["description"];
	}

	/**
	 * Send SimpleEconomyUI Main form to player
	 * @param Player $player
	 */
	public function mainForm(Player $player)
	{
		$form = new SimpleForm(function (Player $player, $data) {
			switch ($data) {
				case "GiveMoney":
					$this->giveMoneyForm($player);
					break;
				case "MyMoney":
					$this->myMoneyForm($player);
					break;
				case "MyStatus":
					$this->myStatusForm($player);
					break;
				case "Pay":
					$this->payForm($player);
					break;
				case "SeeMoney":
					$this->seeMoneyForm($player);
					break;
				case "SetLang":
					$this->setLangForm($player);
					break;
				case "SetMoney":
					$this->setMoneyForm($player);
					break;
				case "TakeMoney":
					$this->takeMoneyForm($player);
					break;
				case "TopMoney":
					$this->topMoneyForm($player);
			}
		});
		$form->setTitle(TextFormat::DARK_GREEN . "EconomyUI");
		if ($this->checkPerm($player, "givemoney"))
			$form->addButton(TextFormat::DARK_GRAY . "Give money\n" . TextFormat::GRAY . $this->getCommandDesc("givemoney"), -1, "", "GiveMoney");
		if ($this->checkPerm($player, "mymoney"))
			$form->addButton(TextFormat::DARK_GRAY . "My Money\n" . TextFormat::GRAY . $this->getCommandDesc("mymoney"), -1, "", "MyMoney");
		if ($this->checkPerm($player, "mystatus"))
			$form->addButton(TextFormat::DARK_GRAY . "My Status\n" . TextFormat::GRAY . $this->getCommandDesc("mystatus"), -1, "", "MyStatus");
		if ($this->checkPerm($player, "pay"))
			$form->addButton(TextFormat::DARK_GRAY . "Pay\n" . TextFormat::GRAY . $this->getCommandDesc("pay"), -1, "", "Pay");
		if ($this->checkPerm($player, "seemoney"))
			$form->addButton(TextFormat::DARK_GRAY . "See Money\n" . TextFormat::GRAY . $this->getCommandDesc("seemoney"), -1, "", "SeeMoney");
		if ($this->checkPerm($player, "setlang"))
			$form->addButton(TextFormat::DARK_GRAY . "Set Language\n" . TextFormat::GRAY . $this->getCommandDesc("setlang"), -1, "", "SetLang");
		if ($this->checkPerm($player, "setmoney"))
			$form->addButton(TextFormat::DARK_GRAY . "Set Money\n" . TextFormat::GRAY . $this->getCommandDesc("setmoney"), -1, "", "SetMoney");
		if ($this->checkPerm($player, "takemoney"))
			$form->addButton(TextFormat::DARK_GRAY . "Take Money\n" . TextFormat::GRAY . $this->getCommandDesc("takemoney"), -1, "", "TakeMoney");
		if ($this->checkPerm($player, "topmoney"))
			$form->addButton(TextFormat::DARK_GRAY . "Top Money\n" . TextFormat::GRAY . $this->getCommandDesc("topmoney"), -1, "", "TopMoney");
		$form->addButton(TextFormat::RED . "Exit\n" . TextFormat::GRAY . "Tap to close");
		$player->sendForm($form);
	}

	/**
	 * Send GiveMoney form to player
	 * @param Player $player
	 */
	public function giveMoneyForm(Player $player)
	{
		$form = new CustomForm(function (Player $player, array $data = null) {
			if (!is_null($data)) $this->giveMoneySubForm($player, $data["TargetName"], $data["Amount"]);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Give Money");
		$form->addInput(TextFormat::YELLOW . "Enter player's name", "DontTouchMeXD", null, "TargetName");
		$form->addInput(TextFormat::YELLOW . "Enter amount of money", "10000", null, "Amount");
		$player->sendForm($form);
	}

	/**
	 * @param Player $player
	 * @param string $tName
	 * @param float $amount
	 */
	private function giveMoneySubForm(Player $player, string $tName, $amount)
	{
		if (!is_numeric($amount) or trim($tName) === "") {
			$this->mainForm($player);
			return;
		}
		$economy = $this->getEconomyAPI();
		$target = $this->getServer()->getPlayer($tName);
		$pName = $player->getName();
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Give Money");
		$result = $economy->addMoney($tName, floatval($amount));
		switch ($result) {
			case EconomyAPI::RET_INVALID:
				$form->setContent(TextFormat::RED . $economy->getMessage("givemoney-invalid-number", [$amount], $pName));
				break;
			case EconomyAPI::RET_SUCCESS:
				$form->setContent(TextFormat::GREEN . $economy->getMessage("givemoney-gave-money", [$amount, $tName], $pName));
				if ($target instanceof Player) $target->sendMessage($economy->getMessage("givemoney-money-given", [$amount], $pName));
				break;
			case EconomyAPI::RET_CANCELLED:
				$form->setContent(TextFormat::RED . $economy->getMessage("request-cancelled", [], $pName));
				break;
			case EconomyAPI::RET_NO_ACCOUNT:
				$form->setContent(TextFormat::RED . $economy->getMessage("player-never-connected", [$tName], $pName));
				break;
		}
		$form->addButton(TextFormat::RED . "Back");
		$player->sendForm($form);
	}

	/**
	 * Send MyMoney form to player
	 * @param Player $player
	 */
	public function myMoneyForm(Player $player)
	{
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		$name = $player->getName();
		$money = $this->getEconomyAPI()->myMoney($name);
		$form->setTitle(TextFormat::DARK_GREEN . "My Money");
		$form->setContent(TextFormat::GREEN . $this->getEconomyAPI()->getMessage("mymoney-mymoney", [$money], $player->getName()));
		$form->addButton(TextFormat::RED . "Back");
		$player->sendForm($form);
	}

	/**
	 * Send MyStatus form to player
	 * @param Player $player
	 */
	public function myStatusForm(Player $player)
	{
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		$money = $this->getEconomyAPI()->getAllMoney();
		$allMoney = 0;
		foreach ($money as $m) $allMoney += $m;
		$topMoney = 0;
		if ($allMoney > 0) {
			$topMoney = round((($money[strtolower($player->getName())] / $allMoney) * 100), 2);
		}
		$form->setContent(TextFormat::GREEN . $this->getEconomyAPI()->getMessage("mystatus-show", [$topMoney], $player->getName()));
		$form->addButton(TextFormat::RED . "Back");
		$player->sendForm($form);
	}

	/**
	 * Send Pay form to player
	 * @param Player $player
	 */
	public function payForm(Player $player)
	{
		$form = new CustomForm(function (Player $player, array $data = null) {
			if (!is_null($data)) $this->paySubForm($player, $data["TargetName"], $data["Amount"]);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Pay");
		$form->addInput(TextFormat::YELLOW . "Enter player's name", "DontTouchMeXD", null, "TargetName");
		$form->addInput(TextFormat::YELLOW . "Enter amount of money", "10000", null, "Amount");
		$player->sendForm($form);
	}

	/**
	 * @param Player $payer
	 * @param string $tName
	 * @param float $amount
	 */
	private function paySubForm(Player $payer, string $tName, $amount)
	{
		if (!is_numeric($amount) or trim($tName) === "") {
			$this->mainForm($payer);
			return;
		}
		$economy = $this->getEconomyAPI();
		$target = $this->getServer()->getPlayer($tName);
		$pName = $payer->getName();
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Pay");
		if (!($target instanceof Player) and $economy->getConfig()->get("allow-pay-offline", true) === false)
			$form->setContent(TextFormat::RED . $economy->getMessage("player-not-connected", [$tName], $pName));
		elseif (!$economy->accountExists($tName))
			$form->setContent(TextFormat::RED . $economy->getMessage("player-never-connected", [$tName], $pName));
		else {
			$event = new PayMoneyEvent($economy, $pName, $tName, $amount);
			$event->call();
			$result = EconomyAPI::RET_CANCELLED;
			if (!$event->isCancelled()) $result = $economy->reduceMoney($payer, $amount);
			if ($result === EconomyAPI::RET_SUCCESS) {
				$economy->addMoney($tName, $amount, true);
				$form->setContent(TextFormat::GREEN . $economy->getMessage("pay-success", [$amount, $tName], $pName));
				if ($target instanceof Player) $target->sendMessage($economy->getMessage("money-paid", [$pName, $amount], $tName));
			} else $form->setContent(TextFormat::RED . $economy->getMessage("pay-failed", [$tName, $amount], $pName));
		}
		$form->addButton(TextFormat::RED . "Back");
		$payer->sendForm($form);
	}

	/**
	 * Send SeeMoney form to player
	 * @param Player $player
	 */
	public function seeMoneyForm(Player $player)
	{
		$form = new CustomForm(function (Player $player, array $data = null) {
			if (!is_null($data)) $this->seeMoneySubForm($player, $data["TargetName"]);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "See Money");
		$form->addInput(TextFormat::YELLOW . "Enter player's name", "DontTouchMeXD", null, "TargetName");
		$player->sendForm($form);
	}

	/**
	 * @param Player $player
	 * @param string $tName
	 */
	private function seeMoneySubForm(Player $player, string $tName)
	{
		if (trim($tName) === "") {
			$this->mainForm($player);
			return;
		}
		$economy = $this->getEconomyAPI();
		$pName = $player->getName();
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "See Money");
		$money = $economy->myMoney($player);
		if ($money !== false) $form->setContent(TextFormat::GREEN . $economy->getMessage("seemoney-seemoney", [$tName, $money], $pName));
		else $form->setContent(TextFormat::RED . $economy->getMessage("player-never-connected", [$tName], $pName));
		$form->addButton(TextFormat::RED . "Back");
		$player->sendForm($form);
	}

	/**
	 * Send SetLang form to player
	 * @param Player $player
	 */
	public function setLangForm(Player $player)
	{
		$form = new CustomForm(function (Player $player, array $data = null) {
			if (!is_null($data)) $this->setLangSubForm($player, $data["Language"]);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Set Language");
		$languages = ["Chinese (ch)", "Czech", "German", "Default (English)", "French", "Indonesian", "Italian", "Japanese", "Korean", "Dutch", "Russian", "Ukrainian", "Chinese (zh)"];
		$form->addDropdown(TextFormat::YELLOW . "Select language", $languages, 3, "Language");
		$player->sendForm($form);
	}

	/**
	 * @param Player $player
	 * @param int $index
	 */
	private function setLangSubForm(Player $player, int $index)
	{
		$economy = $this->getEconomyAPI();
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Set Language");
		$lang = ["ch", "cs", "de", "def", "fr", "id", "it", "ja", "ko", "nl", "ru", "uk", "zh"];
		$languages = ["Chinese (ch)", "Czech", "German", "Default (English)", "French", "Indonesian", "Italian", "Japanese", "Korean", "Dutch", "Russian", "Ukrainian", "Chinese (zh)"];
		if ($economy->setPlayerLanguage($player->getName(), $lang[$index])) {
			$form->setContent(TextFormat::GREEN . $economy->getMessage("language-set", [$languages[$index]], $player->getName()));
		} else {
			$form->setContent(TextFormat::RED . "There is no language such as {$languages[$index]}");
		}
		$form->addButton(TextFormat::RED . "Back");
		$player->sendForm($form);
	}

	/**
	 * Send SetMoney form to player
	 * @param Player $player
	 */
	public function setMoneyForm(Player $player)
	{
		$form = new CustomForm(function (Player $player, array $data = null) {
			if (!is_null($data)) $this->setMoneySubForm($player, $data["TargetName"], $data["Amount"]);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Set Money");
		$form->addInput(TextFormat::YELLOW . "Enter player's name", "DontTouchMeXD", null, "TargetName");
		$form->addInput(TextFormat::YELLOW . "Enter amount of money", "10000", null, "Amount");
		$player->sendForm($form);
	}

	/**
	 * @param Player $player
	 * @param string $tName
	 * @param float $amount
	 */
	private function setMoneySubForm(Player $player, string $tName, $amount)
	{
		if (!is_numeric($amount) or trim($tName) === "") {
			$this->mainForm($player);
			return;
		}
		$economy = $this->getEconomyAPI();
		$target = $this->getServer()->getPlayer($tName);
		$pName = $player->getName();
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Set Money");
		$result = $economy->setMoney($tName, floatval($amount));
		switch ($result) {
			case EconomyAPI::RET_INVALID:
				$form->setContent(TextFormat::RED . $economy->getMessage("setmoney-invalid-number", [$amount], $pName));
				break;
			case EconomyAPI::RET_NO_ACCOUNT:
				$form->setContent(TextFormat::RED . $economy->getMessage("player-never-connected", [$tName], $pName));
				break;
			case EconomyAPI::RET_CANCELLED:
				$form->setContent(TextFormat::RED . $economy->getMessage("setmoney-failed", [], $pName));
				break;
			case EconomyAPI::RET_SUCCESS:
				$form->setContent(TextFormat::GREEN . $economy->getMessage("setmoney-setmoney", [$tName, $amount], $pName));
				if ($target instanceof Player) $target->sendMessage($economy->getMessage("setmoney-set", [$amount], $tName));
				break;
			default:
				$form->setContent(TextFormat::RED . "WTF");
		}
		$form->addButton(TextFormat::RED . "Back");
		$player->sendForm($form);
	}

	/**
	 * Send TakeMoney form to player
	 * @param Player $player
	 */
	public function takeMoneyForm(Player $player)
	{
		$form = new CustomForm(function (Player $player, array $data = null) {
			if (!is_null($data)) $this->takeMoneySubForm($player, $data["TargetName"], $data["Amount"]);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Take Money");
		$form->addInput(TextFormat::YELLOW . "Enter player's name", "DontTouchMeXD", null, "TargetName");
		$form->addInput(TextFormat::YELLOW . "Enter amount of money", "10000", null, "Amount");
		$player->sendForm($form);
	}

	/**
	 * @param Player $player
	 * @param string $tName
	 * @param float $amount
	 */
	private function takeMoneySubForm(Player $player, string $tName, $amount)
	{
		if (!is_numeric($amount) or trim($tName) === "") {
			$this->mainForm($player);
			return;
		}
		$economy = $this->getEconomyAPI();
		$target = $this->getServer()->getPlayer($tName);
		$pName = $player->getName();
		$form = new SimpleForm(function (Player $player, $data) {
			if (!is_null($data)) $this->mainForm($player);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Take Money");
		if (floatval($amount) < 0) $form->setContent(TextFormat::RED . $economy->getMessage("takemoney-invalid-number", [$amount], $pName));
		else {
			$result = $economy->reduceMoney($tName, floatval($amount));
			switch ($result) {
				case EconomyAPI::RET_INVALID:
					$form->setContent(TextFormat::RED . $economy->getMessage("takemoney-player-lack-of-money", [$tName, $amount, $economy->myMoney($tName)], $pName));
					break;
				case EconomyAPI::RET_SUCCESS:
					$form->setContent(TextFormat::GREEN . $economy->getMessage("takemoney-took-money", [$tName, $amount], $pName));
					if ($target instanceof Player) $target->sendMessage($economy->getMessage("takemoney-money-taken", [$amount], $pName));
					break;
				case EconomyAPI::RET_CANCELLED:
					$form->setContent(TextFormat::RED . $economy->getMessage("takemoney-failed", [], $pName));
					break;
				case EconomyAPI::RET_NO_ACCOUNT:
					$form->setContent(TextFormat::RED . $economy->getMessage("player-never-connected", [$tName], $pName));
					break;
			}
		}
		$form->addButton(TextFormat::RED . "Back");
		$player->sendForm($form);
	}

	/**
	 * Send TopMoney form to player
	 * @param Player $player
	 */
	public function topMoneyForm(Player $player)
	{
		$form = new CustomForm(function (Player $player, array $data = null) {
			if (!is_null($data)) $this->topMoneySubForm($player, $data["Page"]);
		});
		$form->setTitle(TextFormat::DARK_GREEN . "Top Money");
		$form->addInput(TextFormat::YELLOW . "Top Money page", "1", null, "Page");
		$player->sendForm($form);
	}

	/**
	 * @param Player $player
	 * @param int $page
	 */
	private function topMoneySubForm(Player $player, $page)
	{
		if (!is_numeric($page)) {
			$this->mainForm($player);
			return;
		}
		$economy = $this->getEconomyAPI();
		$server = $this->getServer();
		$banned = [];
		foreach ($server->getNameBans()->getEntries() as $entry) {
			if ($economy->accountExists($entry->getName())) {
				$banned[] = $entry->getName();
			}
		}
		$ops = [];
		foreach ($server->getOps()->getAll() as $op) {
			if ($economy->accountExists($op)) {
				$ops[] = $op;
			}
		}
		$this->getServer()->getAsyncPool()->submitTask(new class($player->getName(), $economy->getAllMoney(), $economy->getConfig()->get("add-op-at-rank"), $page, $ops, $banned) extends AsyncTask {
			private $player, $moneyData, $addOp, $page, $ops, $banList;
			private $max = 0;
			private $topList;

			public function __construct(string $player, array $moneyData, bool $addOp, int $page, array $ops, array $banList)
			{
				$this->player = $player;
				$this->moneyData = $moneyData;
				$this->addOp = $addOp;
				$this->page = $page;
				$this->ops = $ops;
				$this->banList = $banList;
			}

			public function onRun()
			{
				$this->topList = serialize((array)$this->getTopList());
			}

			private function getTopList()
			{
				$money = (array)$this->moneyData;
				$banList = (array)$this->banList;
				$ops = (array)$this->ops;
				arsort($money);
				$ret = [];
				$n = 1;
				$this->max = ceil((count($money) - count($banList) - ($this->addOp ? 0 : count($ops))) / 5);
				$this->page = (int)min($this->max, max(1, $this->page));
				foreach ($money as $p => $m) {
					$p = strtolower($p);
					if (isset($banList[$p])) continue;
					if (isset($this->ops[$p]) and $this->addOp === false) continue;
					$current = (int)ceil($n / 5);
					if ($current === $this->page) $ret[$n] = [$p, $m];
					elseif ($current > $this->page) break;
					++$n;
				}
				return $ret;
			}

			public function onCompletion(\pocketmine\Server $server)
			{
				if (($player = $server->getPlayerExact($this->player)) instanceof Player) {
					$economy = EconomyAPI::getInstance();
					$plugin = $economy->getServer()->getPluginManager()->getPlugin("SimpleEconomyUI");
					$title = $economy->getMessage("topmoney-tag", [$this->page, $this->max], $this->player);
					$content = "";
					$message = ($economy->getMessage("topmoney-format", [], $this->player) . "\n");
					foreach (unserialize($this->topList) as $n => $list) {
						$content .= str_replace(["%1", "%2", "%3"], [$n, $list[0], $list[1]], $message);
					}
					$content = substr($content, 0, -1);
					$form = new SimpleForm(function (Player $player, $data) use ($plugin) {
						if (!is_null($data)) $plugin->mainForm($player);
					});
					$form->setTitle(TextFormat::DARK_GREEN . $title);
					$form->setContent($content);
					$form->addButton(TextFormat::RED . "Back");
					$player->sendForm($form);
				}
			}
		});
	}
}