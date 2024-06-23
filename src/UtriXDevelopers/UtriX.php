<?php 

namespace UtriXDevelopers;

use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\scheduler\Task;
use JaxkDev\DiscordBot\Bot;
use JaxkDev\DiscordBot\Communication;
use pocketmine\world\World;
use pocketmine\world\WorldManager;
use pocketmine\utils\TextFormat;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\network\mcpe\protocol\types\NetworkSession;
use pocketmine\utils\Config;
use pocketmine\world\sound\NoteSound;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\Command\CommandMap;
use JaxkDev\DiscordBot\Models\Member;
use pocketmine\Command\Command;
use JaxkDev\DiscordBot\Models\Activity;
use pocketmine\world\sound\NoteInstrument;
use mysqli;
use pocketmine\permission\PermissionAttachment;

class UtriX extends PluginBase 
{
	private static $instance;
	public $heart = [];
	public $flame = [];
	// -----------------
	public $config;
	/** @var array $scoreboards */
	public $scoreboards = [];
	public $buildmodon = false;
	public $fly = false;
	public $vanish = false;
	public $freeze = false;
	public $rank_fly = false;
    public $code = [];
	public $authorid = [];
	public $perms = [];
	// -------------------
	public $redstone = [];
	public $friends = [];
	public $Rule = array();
	public $ddos_time = 110;
	public $channel_id = [];
	public $specialranks = ["Builder","Owner", "Co-Owner", "Manager", "StaffManager","HeadAdmin", "SrAdmin","Admin","MrMod","SrMod", "Mod", "Helper", "Developer", "Friendly", "Platinum", "Premium", "Donator"];
	// ------------
	public $clanowners;
	public $clantags;
	public $clanmembers;
	public $discord;
	public $clanrequests;
	public $appealcodes;
	public $usc;
	public $re_usc;
	public $permissions;
	public $ranks;
	public $staffdata;
	public $protection;
	public $aquire1 = false;
	public $aquire2 = false;
	public $aquire3 = false;
	// ---------------
	public $rfly = [];
	

	public function onEnable() : void 
	{
		@mkdir($this->getDataFolder() . "playerdata/");
		@mkdir($this->getDataFolder() . "playerdiscord/");
		@mkdir($this->getDataFolder() . "clandata/");
		@mkdir($this->getDataFolder() . "BanData/");
		@mkdir($this->getDataFolder() . "USC/");
		@mkdir($this->getDataFolder() . "RanksData/");
		@mkdir($this->getDataFolder() . "StaffData/");
		@mkdir($this->getDataFolder() . "PetsData/");
		@mkdir($this->getDataFolder() . "playertempskin/");
		@mkdir($this->getDataFolder() . "PlayerTempGeo/");
		@mkdir($this->getDataFolder() . "ProtectionSystem/");

		$this->clanowners = new Config($this->getDataFolder() . "clandata/clanowners.yml", 2);
		$this->clantags = new Config($this->getDataFolder() . "clandata/clantags.yml", 2);
		$this->clanrequests = new Config($this->getDataFolder() . "clandata/clanrequests.yml", 2);
		$this->clanmembers = new Config($this->getDataFolder() . "clandata/clanmembers.yml", 2);
		$this->discord = new Config($this->getDataFolder() . "playerdiscord/discordaccounts.yml", 2);
		$this->appealcodes = new Config($this->getDataFolder() . "BanData/AppealCodes.yml", 2);
		$this->usc = new Config($this->getDataFolder() . "USC/utrix_security_code.yml", 2);
		$this->permissions = new Config($this->getDataFolder() . "RanksData/permissions.yml", 2);
		$this->ranks = new Config($this->getDataFolder() . "RanksData/ranks.yml", 2);
		$this->staffdata = new Config($this->getDataFolder() . "StaffData/staff.yml", 2);
		$this->protection = new Config($this->getDataFolder() . "ProtectionSystem/protection.yml", 2);
		// $this->re_usc = new Config($this->getDataFolder() . "USC/utrix_names_code.yml", 2);

		$this->saveResource("cape.png");
		$this->saveResource("cape2.png");
		$this->saveResource("cape3.png");
		$this->saveResource("cape4.png");
		$this->saveResource("testcape.gif");
		$this->saveResource("instacape.png");
		$this->saveResource("utrixcape.png");
		$this->saveResource("discordcape.png");
		$this->saveResource("PetsData/fox.png");
        $this->saveResource("NPCData/npc.steve.png");
		
		// $this->ConnectToFTP();
		$this->getLogger()->info("§6UtriXCore | ENABLED");
		
		// $discordbot = $this->getServer()->getPluginManager()->getPlugin("DiscordBot");
        // $api = $discordbot->getApi();
		// $api->updateBotPresence(new \JaxkDev\DiscordBot\Models\Activity("UtriX", 3, null, null, null, null, "973707944527024258", "Developed AI By oPinqz | UtriX 2020-2022", null, null, null, null, null, null, null, "Developed AI By oPinqz | UtriX 2020-2022", null, "Developed AI By oPinqz | UtriX 2020-2022", true, null)); 

		
		self::$instance = $this;
		$this->Notifications();
		// $this->getServer()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("ban"));
		$this->getServer()->getWorldManager()->loadWorld("skymines", true);
		$this->getServer()->getWorldManager()->loadWorld("skypvp", true);
		$this->getServer()->getWorldManager()->loadWorld("boxpvp", true);
		$this->getServer()->getWorldManager()->loadWorld("redstonepvp", true);
		$this->getServer()->getWorldManager()->loadWorld("kitpvp", true);
		$this->getServer()->getWorldManager()->loadWorld("fist", true);
		$this->getServer()->getPluginManager()->registerEvents(new UtriXEvents, $this);
		$this->getCommand("rank")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("staff")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("report")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("uban")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("build")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("hub")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("lobby")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("fly")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("umute")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("unmute")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("uworldtp")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("link")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("setucoins")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("uemerald")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("setuemeralds")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("rules")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("setjoinmsg")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("setleavemsg")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("clan")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("ping")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("getusc")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("suggest")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("unspectate")->setExecutor(new UtriXCommands, $this);
		$this->getCommand("protection")->setExecutor(new UtriXCommands, $this);
		$this->getServer()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("version"));
		$this->getServer()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("ban"));
		$this->getServer()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("kill"));

	
	}


	public function onDisable() : void
	{
		$this->getLogger()->info("§6UtriXCore | DISABLED");
	}

	public static function getPluginInstance() : self
	{
		return self::$instance;
	}

	

	public function new(Player $player, string $objectiveName, string $displayName): void{
		if(isset($this->scoreboards[$player->getName()])){
			$this->remove($player);
		}
		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = "sidebar";
		$pk->objectiveName = $objectiveName;
		$pk->displayName = $displayName;
		$pk->criteriaName = "dummy";
		$pk->sortOrder = 1;
		
		// $player->sendDataPacket($pk);
		$player->getNetworkSession()->sendDataPacket($pk);
		$this->scoreboards[$player->getName()] = $objectiveName;
	}

	public function remove(Player $player){
		$objectiveName = $this->getObjectiveName($player);
		$pk = new RemoveObjectivePacket();
		if($objectiveName == null || $objectiveName == ""){
			return true;
		} else {
			$pk->objectiveName = $objectiveName;
			$player->getNetworkSession()->sendDataPacket($pk);
			unset($this->scoreboards[$player->getName()]);
		}
		
	}

	public function setPermission(Player $player){
		if(!isset($this->perms[$player->getId()])){
			return $this->perms[$player->getId()] = $player->addAttachment($this);
		}
		return $this->perms[$player->getId()];
	}

	public function getData(Player $player = null, String $playername = null){
		$utrix = $this;
		if($player !== null && $playername == null){
			$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
				"Name" => $player->getName(),
				"Rank" => "Member",
				"ChatLevel" => 0,
				"JoinMSG" => "NONE",
				"BanReason" => "NONE",
				"AppealCode" => "NONE",
				"UtriXFriends" => "NONE",
				"MuteStatus" => "FALSE",
				"UCoins" => 0,
				"UEmeralds" => 0,
				"LeaveMSG" => "NONE",
				"Clan" => "NONE",
				"DiscordAccountID" => "None",
				"LinkedStatus" => "unlinked",
				"Banned_On" => "NONE",
				"UnBannedIn" => "NONE",
			));
		} 
		
		if($playername !== null && $player == null){
			$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($playername) . ".yml", Config::YAML, array(
				"Name" => $playername,
				"Rank" => "Member",
				"ChatLevel" => 0,
				"JoinMSG" => "NONE",
				"BanReason" => "NONE",
				"AppealCode" => "NONE",
				"UtriXFriends" => "NONE",
				"MuteStatus" => "FALSE",
				"UCoins" => 0,
				"UEmeralds" => 0,
				"LeaveMSG" => "NONE",
				"Clan" => "NONE",
				"DiscordAccountID" => "None",
				"LinkedStatus" => "unlinked",
				"BannedOn" => "NONE",
				"UnBannedIn" => "NONE",
				"BanStatus" => "Not",
			));
		}

		return $utrix->config;
	}

	
	public function setCape(Player $player, string $cape)
	{
		$oldSkin = $player->getSkin();
		$newSkin = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $cape , $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
		$player->setSkin($newSkin);
		$player->sendSkin();
	}

	public function createCape(Player $player, string $file){
        $ex = '.png';
        $path = $this->getDataFolder() . $file . $ex;
        if (!file_exists($path)){
            $this->getLogger()->info("CAPE NOT FOUND");
            return true;
        }
            $img = imagecreatefrompng($path);
            $rgba = "";
            for ($y = 0; $y < imagesy($img); $y++) {
                for ($x = 0; $x < imagesx($img); $x++) {
                    $argb = imagecolorat($img, $x, $y);
                    $rgba .= chr(($argb >> 16) & 0xff) . chr(($argb >> 8) & 0xff) . chr($argb & 0xff) . chr(((~((int)($argb >> 24))) << 1) & 0xff);
                }
            }
            if (!strlen($rgba) === 8192) {
                if (!$sender === null) {
                    $sender->sendMessage(TextFormat::RED . "Invalid cape");
                    return true;
                }
                $player->sendMessage(TextFormat::RED . "Invalid cape");
                return true;
            }
            $this->setCape($player, $rgba);
            return true;
    }

	public function setLine(Player $player, int $score, string $message): void{
		if(!isset($this->scoreboards[$player->getName()])){
			$this->getLogger()->error("Cannot set a score to a player with no scoreboard");
			return;
		}
		
		$objectiveName = $this->getObjectiveName($player);
		$entry = new ScorePacketEntry();
		$entry->objectiveName = $objectiveName;
		$entry->type = $entry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = $score;
		$entry->scoreboardId = $score;
		$pk = new SetScorePacket();
		$pk->type = $pk::TYPE_CHANGE;
		$pk->entries[] = $entry;
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function UtriXScoreBoard(Player $player)
	{
		$api = $this;
		$ucoins = $this->getData($player, null);
		$uemerald = $this->getData($player, null);
		$online = count($this->getServer()->getOnlinePlayers());
		$rank = $this->ranks->get($this->getData($player, null)->get("Rank"));
		$rankco = mb_chr(0xE0FF);
		$coinco = mb_chr(0xE0FE);
		$levelco = mb_chr(0xE0FD);
		$pingco = mb_chr(0xE0FC);
		$api->new($player, "§l§6UtriX Network", "utrix.scoreboard.logo");
		$api->setLine($player, 16, "  ");
		$api->setLine($player, 15,"§7» $rankco ");
		$api->setLine($player, 14, " §7| $rank          ");
		$api->setLine($player, 13, " ");
		$api->setLine($player, 12, "§7» $coinco ");
		$api->setLine($player, 11, " §7| §c" . $this->getUCoins($player) . " §6UCoins       ");
		$api->setLine($player, 10, "");
		$api->setLine($player, 6, "§7» $pingco");
		$api->setLine($player, 5, " §7| ". $this->getPing($player) . " §7ms        ");
		$api->setLine($player, 4, "          ");
	}

	public function getObjectiveName(Player $player): ?string{
		return isset($this->scoreboards[$player->getName()]) ? $this->scoreboards[$player->getName()] : null;
	}

	public function Notifications(){
		$this->getScheduler()->scheduleRepeatingTask(new UtriXNotifyTask($this), 20);
	}
	

	public function getOnlineNames($list){
		foreach($list as $p){
			$final[] = $p->getName();
			$array = array($final);
		}

		return $array;
	}


	public function GenerateAppealCode() 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		intval(srand((double)microtime()*1000000));
		$i = 0;
		$pass = '';
		while($i <= 5)
		{
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;

		}
		return $pass;
	}

	public function GenerateUtriXCode() 
	{
		$chars = "utrix123456789";
		srand(intval((double)microtime()*1000000));
		$i = 0;
		$pass = '';
		while($i <= 5)
		{
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++; 

		}
		return $pass;
	}




	public function getUtriXCode(Player $player)
	{
		return $this->getData($player)->get("UtriXCode");
	}
	

	public function getRank(Player $player)
	{
		$this->config = new Config($this->getDataFolder() . "playerdata/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
			"Name" => $player->getName(),
			"Rank" => "Member",
			"ChatLevel" => 0,
			"Banned" => "FALSE",
			"Reason_Of_Ban" => "NONE",
			"UtriXCode" => $this->GenerateUtriXCode(),

		));

		$this->config->get("Rank");
	}




	public function getChatProgress($player)
	{
		$this->config = new Config($this->getDataFolder() . "playerdata/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
			"Name" => $player->getName(),
			"Rank" => "Member",
			"ChatLevel" => 0,
			"Banned" => "FALSE",
			"Reason_Of_Ban" => "NONE",
			"Num_of_words" => 0,
			"Generated_BY" => "UTRIX AI V2.0",
			"UtriXMutes" => "FALSE",

		));

		$uwords = $this->config->get("Num_of_words");
		$chatlevel = $this->config->get("ChatLevel");
		$chatprogress = "";
		if($chatlevel == 1)
		{
			$chatprogress = "§a" . $uwords . "§7/§3100";
		}
		if($chatlevel == 2)
		{
			$chatprogress = "§a" . $uwords . "§7/§3300";
		}
		if($chatlevel == 3)
		{
			$chatprogress = "§a" . $uwords . "§7/§3500";
		}
		if($chatlevel == 4)
		{
			$chatprogress = "§a" . $uwords . "§7/§3700";
		}
		if($chatlevel == 5)
		{
			$chatprogress = "§a" . $uwords . "§7/§3900";
		}
		if($chatlevel == 6)
		{
			$chatprogress = "§a" . $uwords . "§7/§31100";
		}
		if($chatlevel == 7)
		{
			$chatprogress = "§a" . $uwords . "§7/§31300";
		}
		if($chatlevel == 8)
		{
			$chatprogress = "§a" . $uwords . "§7/§31500";
		}
		if($chatlevel == 9)
		{
			$chatprogress = "§a" . $uwords . "§7/§31700";
		}
		if($chatlevel == 10)
		{
			$chatprogress = "§a" . $uwords . "§7/§3MAX";
		}

		return $chatprogress;

	}

	public function LobbyCoreItems($player) {
		$player->getInventory()->clearAll();

		// Games
		$item1 = VanillaItems::COMPASS();
		// Gadgets
		$item2 = VanillaItems::BLAZE_ROD();
	
		// Profile
		$item4 = VanillaItems::PAPER();
		// Settings

		$item1->setCustomName("§r§l§cGames");
		$item2->setCustomName("§r§l§6Gadgets");
		$item4->setCustomName("§r§l§aProfile");

		$player->getInventory()->setItem(0, $item1);
		$player->getInventory()->setItem(1, $item2);
		$player->getInventory()->setItem(8, $item4);

	}

	public function ChangePlayerName(Player $player, String $name) { 
		$rank_prefix = $this->ranks->get($this->getData($player, null)->get("Rank"));
		$clantag = "";
		if($this->clantags->get($this->getData($player, null)->get("Clan")) !== null) {
			$clan_tag = $this->clantags->get($this->getData($player, null)->get("Clan"));
		} else {
			$clan_tag = "§7None";
		}
		if(strpos($rank_prefix, "§l") !== false) {
			$rank_color = substr($rank_prefix, 0, -strlen(substr($rank_prefix, 6)));
		
		} else {
			$rank_color = substr($rank_prefix, 0, -strlen(substr($rank_prefix, 3)));
		
		}
		$player->setNameTag($rank_prefix . " §r§7| " . $rank_color . $name . " §8[ $clan_tag §8]");
		// print_r($rank_prefix . " §r§7| " . $rank_color . $name);
		//print_r(strlen(substr(strval($rank_prefix), 6)));
	}
	
	public function getPlayerChatName(Player $player, String $name, String $msg) { 
		$rank_prefix = $this->ranks->get($this->getData($player, null)->get("Rank"));
		if(strpos($rank_prefix, "§l") !== false) {
			$rank_color = substr($rank_prefix, 0, -strlen(substr($rank_prefix, 6)));
		
		} else {
			$rank_color = substr($rank_prefix, 0, -strlen(substr($rank_prefix, 3)));
		
		}
		$chatlevel = $this->getData($player)->get("ChatLevel");
		$c = "";
		if($player->hasPermission("utrix.tier1")){
			$c = "§7";
			return "§e" . $chatlevel . " §7| " . $rank_prefix . " §r§7| " . $rank_color . $name . " §r§7»$c $msg";
		} else if($player->hasPermission("utrix.tier2")){
			$c = "§3";
			return "§e" . $chatlevel . " §7| " . $rank_prefix . " §r§7| " . $rank_color . $name . " §r§7»$c $msg";
		} else if($player->hasPermission("utrix.tier3")){
			$c = "§6";
			return "§e" . $chatlevel . " §7| " . $rank_prefix . " §r§7| " . $rank_color . $name . " §r§7»$c $msg";
		} else {
			$c = "§7";
			return "§e" . $chatlevel . " §7| " . $rank_prefix . " §r§7| " . $rank_color . $name . " §r§7»$c $msg";

		}
		// print_r($rank_prefix . " §r§7| " . $rank_color . $name);
		//print_r(strlen(substr(strval($rank_prefix), 6)));
	}

	public function NetworkLevel($player){
		$data = $this->getData($player, null);
		
		$network_level = intval(round(floatval($data->get("ChatLevel") + $data->get("UCoins") * 10 / 1000)));
		
		return $network_level;
	}


	public function getPing($player)
	{
		$ping = $player->getNetworkSession()->getPing();
		$ping2 = "";
		if($ping <= 99 || $ping == 0)
		{
			$ping2 = "§a" . $ping;

		}
		if($ping == 100 || $ping2 < 200)
		{
			$ping2 = "§e" . $ping;

		}
		if($ping > 200)
		{
			$ping2 = "§c" . $ping;

		}
		return $ping2;

	}

	public function getUCoins($player) {
		$data = $this->getData($player);
		$ucoins = $data->get("UCoins");
		$final = "";
		if($ucoins >= 1000 && $ucoins < 1000000) {
			return strval($ucoins / 1000) . "K";
		} 
	
		if($ucoins >= 1000000 && $ucoins < 1000000000) {
			return strval($ucoins / 1000000) . "M";
		}

		if($ucoins >= 10000000000) {
			return strval($ucoins / 1000000000) . "B";
		}


		else {
			return strval($ucoins);
		}
		
		

		
	}

	public static function GenerateDisCode() 
	{
		$chars = "discordutrix123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '';
		while($i <= 6)
		{
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++; 

		}
		return $pass;
	}



   



	


	

	




}




