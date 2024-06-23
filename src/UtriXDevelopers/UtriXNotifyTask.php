<?php

namespace UtriXDevelopers;

use UtriXDevelopers\UtriX;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Concrete;
use pocketmine\event\BlockPlaceEvent;

class UtriXNotifyTask extends Task {

    public $time = 220;
    public $plugin;

    public function __construct(UtriX $plugin){

        $this->plugin = $plugin;

    }


    public function onRun() : void{

        // echo "yessir";
        if($this->time == 0){
            $msgarr = ["§7| §6UtriX §7» §eDo You Know that Hydrogen is Our Upcoming AntiCheat ?", "§7| §6UtriX §7» §eDid You Know that Our Mute and Ban Systems is Developed Under Name Hydrogen Integrated in the Core ?", "§7| §6UtriX §7» §eDid You Know That the Developer of UtriX Is Only One Developer, Called oPinqz", "§7| §6UtriX §7» §eThe Owner of UtriX is a Python Developer ! and Learned Php For Developing his Server !", "§7| §6UtriX §7» §eDo You Know that You Can Earn UCoins By Playing in KitPvP !", "§7| §6UtriX §7» §eDo You Know that You Can Purchase UCoins !" , "§7| §6UtriX §7» §eJoin Our Discord, Join the UtriX Community , Report Bugs and Hackers , Report and Staff Abusing, Know Latest Updates, tell us Your FeadBack and Suggestion at https://discord.gg/TqDFxC4xbm", "§7| §6UtriX §7» §eYou Can Get Ranks from Purchasing from Our Shop, https//:utrix-network.tebex.io !"];
            $randommsg = $msgarr[array_rand($msgarr)];
            $this->plugin->getServer()->broadcastMessage($randommsg);
            $this->time = 220;
        }

        $this->time--;
    }



}