<?php

namespace UtriXDevelopers;

use pocketmine\player\Player;
use pocketmine\Server;


use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\world\particle\HeartParticle;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\RedstoneParticle;
use pocketmine\world\particle\SmokeParticle;
use pocketmine\particle\Particle;
use pocketmine\utils\Config;
use JaxkDev\DiscordBot\Bot;
use JaxkDev\DiscordBot\Communication;
use JaxkDev\DiscordBot\Plugin\Events\MessageSent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use JaxkDev\DiscordBot\Plugin\Api;
use JaxkDev\DiscordBot\Plugin\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\world\World;
use pocketmine\world\WorldManager;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\utils\TextFormat;
use pocketmine\item\VanillaItems;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\server\DataPacketReceiveEvent; 
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\entity\EntityDamageEvent;
use UtriXDevelopers\UtriXGUIs;
use pocketmine\player\chat\LegacyRawChatFormatter;

class UtriXEvents implements Listener
{
	public $sitems = ["Potion of Strength", "Potion of Fire Resisitance", "Potion of Regeneration", "Potion of Swiftness"];
	public function onJoin(PlayerJoinEvent $e) : void 
	{
		$player = $e->getPlayer();
		$utrix = UtriX::getPluginInstance();
		$player->setHealth(20);
		$player->getArmorInventory()->clearAll();
		$player->teleport($utrix->getServer()->getWorldManager()->getWorldByName("world")->getSafeSpawn());
		$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
			"Name" => $player->getName(),
			"Rank" => "Member",
			"ChatLevel" => 1,
			"JoinMSG" => "NONE",
			"BanReason" => "NONE",
			"AppealCode" => "NONE",
			"UtriXFriends" => "NONE",
			"MuteStatus" => "FALSE",
			"UnMutedIn" => "NONE",
			"MutedBy" => "NoOne",
			"MuteReason" => "NONE",
			"UCoins" => 0,
			"UEmeralds" => 0,
			"LeaveMSG" => "NONE",
			"Clan" => "NONE",
			"UtriXCode" => $utrix->GenerateUtriXCode(),
			"DiscordAccountID" => "None",
			"LinkedStatus" => "unlinked",
			"BannedOn" => "NONE",
			"UnBannedIn" => "NONE",
			"Banned_By" => "NoOne",
			"BanStatus" => "Not",
			"DailyStatus" => "Null",
			
		));
		if($utrix->config->get("JoinMSG") == "NONE")
		{
			$e->setJoinMessage("§7| §a+ §7| §a" . $utrix->ranks->get($utrix->getData(null, $player->getName())->get("Rank")) . " " . $player->getName());
		} else {
			$e->setJoinMessage("§7| §a+ §7| §a" . $utrix->ranks->get($utrix->getData(null, $player->getName())->get("Rank")) . " " . $player->getName() . " ," . $utrix->config->get("JoinMSG"));
		}
		$player->sendTitle("§l§7<---§6UtriX§7--->", "§l§cfdeet men yl3b :D");
		if(!$player->hasPlayedBefore())
		{
			$player->sendMessage("§7| §6UtriX » §aYou Have Gained 50 UCoins & 10 UEmeralds As It's Your First time to Join Our Network !");
			$utrix->config->set("UCoins", $utrix->config->get("UCoins") + 50);
			$utrix->config->set("UEmerald", $utrix->config->get("UEmeralds") + 10);
			$utrix->config->save();
		} 
		$utrix->usc->set($utrix->getUtriXCode($player), $player->getName());
		$utrix->usc->save();
		$utrix->LobbyCoreItems($player);
		$utrix->getLogger()->info("§6Done, Created USC And Logged Into Player Session, the Player " . $player->getName());
		$player->getEffects()->clear();
		if(!$utrix->ranks->exists($utrix->getData($player)->get("Rank"), true)){
			$utrix->getData($player)->set("Rank", "Member");
		} else {
			foreach($utrix->permissions->get($utrix->getData(null, $player->getName())->get("Rank")) as $perms){
				$utrix->setPermission($player)->setPermission($perms, true);
			}
		}
		$utrix->ChangePlayerName($player, $player->getName() );	
		$utrix->UtriXScoreBoard($player);
		
	}

	


	

	public function PlayerHunger(PlayerExhaustEvent $e)
	{
		$player = $e->getPlayer();

		$player->getHungerManager()->setFood(20);
		$e->cancel();
	}

	public function onQuit(PlayerQuitEvent $e) : void
	{
		$player = $e->getPlayer();
		$utrix = UtriX::getPluginInstance();
		$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
			"Name" => $player->getName(),
			"Rank" => "Member",
			"ChatLevel" => 0,
			"JoinMSG" => "NONE",
			"Reason_Of_Ban" => "NONE",
			"BAN_APPEAL_CODE" => "NONE",
			"UtriXFriends" => "NONE",
			"UtriXMutes" => "FALSE",
			"UCoins" => 0,
			"UEmeralds" => 0,
			"LeaveMSG" => "NONE",
			"Club" => "NONE",
			"DiscordAccountID" => "None",
			"LinkedStatus" => "unlinked",
		));
		if($utrix->config->get("LeaveMSG") == "NONE")
		{
			$e->setQuitMessage("§7| §c- §7| §a" . $utrix->ranks->get($utrix->getData(null, $player->getName())->get("Rank")) . " " . $player->getName());
		} else {
			$e->setQuitMessage("§7| §c- §7| §a" . $utrix->ranks->get($utrix->getData(null, $player->getName())->get("Rank")) . " " . $player->getName() . " ," . $utrix->config->get("LeaveMSG"));
		}
		if(isset($utrix->scoreboards[($player = $e->getPlayer()->getName())])) unset($utrix->scoreboards[$player]);
	
	}



	


	public function onChat(PlayerChatEvent $e) : void
	{
		$player = $e->getPlayer();
		$msg = $e->getMessage();
		$utrix = UtriX::getPluginInstance();
		$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
			"Name" => $player->getName(),
			"Rank" => "Member",
			"ChatLevel" => 0,
			"Banned" => "FALSE",
			"Reason_Of_Ban" => "NONE",
			"Num_of_words" => 0,
			"Generated_BY" => "UTRIX AI V2.0",
			"UtriXMutes" => "FALSE",

		));
		$utrix->config->set("Num_of_words", $utrix->config->get("Num_of_words") + 1);
		$utrix->config->save();
        // Chat LEVELS
        $chatlevel = $utrix->config->get("ChatLevel");
        $numofwords = $utrix->config->get("Num_of_words");
     
        if($numofwords == 100)
        {
        	$utrix->config->set("ChatLevel", $chatlevel + 1);
        	$utrix->config->save();
        }
        if($numofwords == 300)
        {
        	$utrix->config->set("ChatLevel", $chatlevel + 1);
        	$utrix->config->save();
        }
        if($numofwords == 500)
        {
        	$utrix->config->set("ChatLevel", $chatlevel + 1);
        	$utrix->config->save();
        }
        if($numofwords == 700)
        {
        	$utrix->config->set("ChatLevel", $chatlevel + 1);
        	$utrix->config->save();
        }if($numofwords == 900)
        {
        	$utrix->config->set("ChatLevel", $chatlevel + 1);
        	$utrix->config->save();
        }
        if($numofwords == 1100)
        {
        	$utrix->config->set("ChatLevel", $chatlevel + 1);
        	$utrix->config->save();
        }
        if($numofwords == 1300)
        {
        	$utrix->config->set("ChatLevel", $chatlevel + 1);
        	$utrix->config->save();
        }
        if($numofwords == 1500)
        {
        	$utrix->config->set("ChatLevel", $chatlevel + 1);
        	$utrix->config->save();
        }
        if($numofwords == 1700)
        {
        	$utrix->config->set("ChatLevel", $chatlevel + 1);
        	$utrix->config->save();
        }
        if($numofwords == 1900)
        {
        	$utrix->config->set("ChatLevel", $chatlevel + 1);
        	$utrix->config->save();
        }
        if($player->getWorld()->getFolderName() == "world")
		{
			$utrix->UtriXScoreBoard($player);
		} else {
			// Do Nothing 
		}
		
		$message = "| " . $player->getName() . " » " . $e->getMessage();
		
		$pdata = $utrix->getData(null, $player->getName());
		if($pdata->get("MuteStatus") == "True"){
			if($pdata->get("UnMutedIn") < date("d/m/y:h:i:s")){
				$pdata->set("UnMutedIn", "False");
				$pdata->set("MuteStatus", "False");
				$pdata->save();
			} else {
				$reason = $pdata->get("MuteReason");
				$sender_name = $pdata->get("MutedBy");
				$unmuted_on = $pdata->get("UnMutedIn");
				$appealcode = $pdata->get("AppealCode");
				$e->cancel();
				$player->sendMessage("§7| §6UtriX §7» §cYou Have Muted , The Reason §a$reason, §cMuted By : §3$sender_name, §cUnMuted On : §4$unmuted_on, §cThink This is an Error ?, Please Appeal At Our Discord : /discord, Your Appeal Code : §6$appealcode");
			}
		}

		$e->setFormatter(new LegacyRawChatFormatter($utrix->getPlayerChatName($player, $player->getName(), $e->getMessage())));	
		

		

        // chat format fot the ranks

	}

	/** @handleCancelled true */
	public function onInteract(PlayerItemUseEvent $e)
	{

		$player = $e->getPlayer();
		$item = $e->getItem();
		$utrix = UtriX::getPluginInstance();
		$itemname = $player->getInventory()->getItemInHand()->getName();
		
		if($item->getTypeId() == VanillaItems::COMPASS()->getTypeId() || $itemname == "§cGames")
		{
			$utrixguis = new UtriXGUIs;
			$utrixguis->GamesGUI($player);
			$e->cancel();

			return true;

		}

		if($item->getTypeId() == 369 || $itemname == "§r§l§6Gadgets")
		{
			$e->cancel();
			$urank = $utrix->config->get("Rank");
			if($player->hasPermission("utrix.gadgets"))
			{	
				$utrixguis = new UtriXGUIs;
				$utrixguis->Gadgets($player);
			} else {
				$player->sendMessage("§7| §6UtriX §r§7» §cYou Don't Have Permission To Do That !");
			}
			return true;
		}

	

		if($itemname == "§r§l§aProfile"){
			$e->cancel();
			$utrixguis = new UtriXGUIs;
			$utrixguis->ProfileGUI($player);
			return true;
		}


	}

	

	public function onMove(PlayerMoveEvent $e)
	{
		$player = $e->getPlayer();
		$utrix = UtriX::getPluginInstance();
		$name = TextFormat::clean($player->getName());
		if($player->hasPermission("utrix.playerfrozen")){
			$e->cancel();
			$player->sendMessage("§7| §6UtriX §7» §3You're Frozen");
		}

		
		
	}

	public function onBlockPlace(BlockPlaceEvent $e)
	{
		$player = $e->getPlayer();
		$world = $player->getWorld();
		if($world->getFolderName() !== "kbpvp")
		{
			if($player->hasPermission("utrix.build"))
			{
				return true;
			} else {
				$e->cancel();
				$player->sendMessage("§7| §l§6UtriX §7» §cError, You Can't Place Blocks Here !");
			}
		} else {
			return true;
		}

	}

	

	public function ProtectArea(EntityDamageEvent $e)
    {
        $player = $e->getEntity();
        $cause = $e->getCause();
		$utrix = UtriX::getPluginInstance();
        if($player instanceof Player)
        {
            if($cause == EntityDamageEvent::CAUSE_FALL && $player->getWorld()->getFolderName() == "world")
            {
				$e->cancel();
				
			}

			if($cause == EntityDamageEvent::CAUSE_VOID && $player->getWorld()->getFolderName() == "world")
            {
				$e->cancel();
				$player->teleport($utrix->getServer()->getWorldManager()->getWorldByName("world")->getSafeSpawn());
				
			}

			if($cause == EntityDamageEvent::CAUSE_SUFFOCATION){
                if($player->getWorld()->getFolderName() == "kbpvp"){
                    $e->cancel();
                    $player->teleport($utrix->getServer()->getWorldManager()->getWorldByName("kbpvp")->getSafeSpawn());
                }
				if($player->getWorld()->getFolderName() == "kitpvp"){
                    $e->cancel();
                    $player->teleport($utrix->getServer()->getWorldManager()->getWorldByName("kitpvp")->getSafeSpawn());
                }
				if($player->getWorld()->getFolderName() == "fist"){
                    $e->cancel();
                    $player->teleport($utrix->getServer()->getWorldManager()->getWorldByName("fist")->getSafeSpawn());
                }
				
            }
        }
    }

	public function ProtectSpawn(EntityDamageByEntityEvent $e){
		$entity = $e->getEntity();
		$killer = $e->getDamager();
		if($entity instanceof Player){
			if($entity->getWorld()->getFolderName() == "world"){
				$e->cancel();
				$killer->sendMessage("§7| §l§6UtriX §7» §cError, You Can't PvP Here !");
			} else {
				return true;
			}
		}
	}

	public function onBlockBreak(BlockBreakEvent $e)
	{
		$player = $e->getPlayer();
		if($player->hasPermission("utrix.build"))
		{
			return true;
		} else {
			if($player->getWorld()->getFolderName() == "skymines"){
				return true;
			} else {
				$e->cancel();
				$player->sendMessage("§7| §l§6UtriX §7» §cError, You Can't Break Blocks Here !");
			}

		}
	}

	public function onCraft(CraftItemEvent $e)
	{
		$e->cancel();
	}

	public function onDrop(PlayerDropItemEvent $e)
	{
		$player = $e->getPlayer();
		if($player->hasPermission("utrix.build"))
		{
			return true;
		} else {
			$e->cancel();
		}
	}


	public static function GenerateDisCode() 
	{
		$chars = "discordutrix123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '';
		while($i <= 8)
		{
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++; 

		}
		return $pass;
	}

	// DDOS Protection, Fuck CloudyMC

	public function CheckBan(PlayerLoginEvent $e){
		
		$utrix = UtriX::getPluginInstance();
		$player = $e->getPlayer();
		$pdata = $utrix->getData($player, null);
		if($player->hasPlayedBefore()){
			if($pdata->get("BanStatus") == "True"){
				// echo "BANNED";
				if($pdata->get("UnBannedIn") < date("d/m/y:h:i:s")){
					// $utrix->getServer()->getNameBans()->remove($player);
					// echo $pdata->get("UnBannedIn");
					//echo "STILL Banned";
					$pdata->set("UnBannedIn", "NONE");
					$pdata->set("BanStatus", "Not");
					$pdata->set("BanReason", "NONE");
					$pdata->set("Banned_By", "NoOne");
					$pdata->save();
	
		
				} else {
					
					// echo strtotime($pdata->get("UnBannedIn"));
					$usc = $pdata->get("UtriXCode");
					$appealcode = $pdata->get("AppealCode");
					$reason = $pdata->get("BanReason");
					$banned_by = $pdata->get("Banned_By");
					$unbanned_on = $pdata->get("UnBannedIn");
					$e->setKickMessage("§7| §6UtriX Network §7» §aYou Have Been Banned Until §4$unbanned_on, §aReason : §c$reason, §aBanned_By : §c$banned_by, §6Think This Is A Wrong, Appeal At Minecraft Support At Our Discord : https://discord.gg/GMbKFSpPs9, §aYour Appeal Code : §c$appealcode, §aYour UtriX Security Code : §c$usc");
					$player->kick("§7| §6UtriX Network §7» §aYou Have Been Banned Until §4$unbanned_on, §aReason : §c$reason, §aBanned_By : §c$banned_by, §6Think This Is A Wrong, Appeal At Minecraft Support At Our Discord : https://discord.gg/GMbKFSpPs9, §aYour Appeal Code : §c$appealcode, §aYour UtriX Security Code : §c$usc");
					// $e->cancel();
					
	
				}
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

	

	





}







	
	




	
