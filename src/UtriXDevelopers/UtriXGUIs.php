<?php

namespace UtriXDevelopers;

use pocketmine\player\Player;
use pocketmine\Server;
use UtriXDevelopers\UtriX;
use pocketmine\player\GameMode;
use pocketmine\math\Vector3;
use skymin\skin\SkinTool;
use skymin\skin\ImageTool;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\types\ActorEvent;

class UtriXGUIs {

    // Make the Status DM Me the word (UtriX) and On the Other Hand you tell them to put the status for 1 week and give them server link

    public function StaffGUI($player) {
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            if($data == null){
                return true;
            } 

            switch($data){
                case 1:
                    $utrix = UtriX::getPluginInstance();
                    if($utrix->fly == false){
                        $utrix->fly = true;
                        $player->setAllowFlight(true);
                        $player->sendMessage("§7| §6UtriX §7» §aEnabled Fly Successfuly");
                    } else {
                        $utrix->fly = false;
                        $player->setAllowFlight(false);
                        $player->sendMessage("§7| §6UtriX §7» §cDisabled Fly Successfuly");
                    }
                break;

                case 2:
                    $utrix = UtriX::getPluginInstance();
                    if($utrix->vanish == false){
                        $utrix->vanish = true;
                        $player->setInvisible(true);
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly Enabled Vanish");
                    } else {
                        $utrix->vanish == false;
                        $player->setInvisible(false);
                        $player->sendMessage("§7| §6UtriX §7» §cSuccessfuly Disabled Vanish");
                    }
                break;  

                case 3:
                    $this->StaffSpectate($player);
                break;

                case 4:
                    $this->StaffFreeze($player);
                break;
            }
        
        
        
        });

        $form->setTitle("§6UtriX §7- §3StaffSystem");
        $form->addButton("§7» §cExit From StaffSystem");
        $form->addButton("§7» §3Enable§7/§4Disable §2Fly ");
        $form->addButton("§7» §3Enable§7/§4Disable §6Vanish");
        $form->addButton("§7» §eSpectate on a Player");
        $form->addButton("§7» §3Freeze a Player");
        $form->sendToPlayer($player);
        return $form;
    }



    public function StaffSpectate($player) {
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createCustomForm(function (Player $player, array $data = null){
            $utrix = UtriX::getPluginInstance();
            if(!isset($data[0])){
                $player->sendMessage("§7| §6UtriX §7» §cSorry, Please Choose a Player");
                return false;
            } else {
                $target = $utrix->getServer()->getPlayerExact($data[0]);
                $player->setGameMode(GameMode::SPECTATOR());
                if($target->getWorld()->getFolderName() !== "world"){
                    $world_name = $target->getWorld()->getFolderName();
                    $player->teleport($utrix->getServer()->getWorldManager()->getWorldByName($world_name)->getSafeSpawn());
                    $player->teleport(new Vector3($target->getPosition()->getX(), $target->getPosition()->getY(), $target->getPosition()->getZ()));
                } else {
                    $player->teleport(new Vector3($target->getPosition()->getX(), $target->getPosition()->getY(), $target->getPosition()->getZ()));
                }
                $player->sendMessage("§7| §6UtriX §7» §aYou're Now Spectating the Player " . $target->getName() . " , To Exit Spectating, /unspectate");
            }
        });

        $form->setTitle("§6UtriX §7- §eSpectate a Player");
        $form->addInput("§7» §cType a Player Name to Spectate");
        $form->sendToPlayer($player);
        return $form;
    }



    public function StaffFreeze($player){
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createCustomForm(function (Player $player, Array $data = null){
            $utrix = UtriX::getPluginInstance();
            if(!isset($data[0])){
                $player->sendMessage("§7| §6UtriX §7» §cSorry, Please Choose a Player to Freeze");
                return false;
            } else {
                $target = $utrix->getServer()->getPlayerExact($data[0]);

                if($target == null) {
                    $player->sendMessage("§7| §6UtriX §7» §cYou Can'teloper, Founder, Father, #UCV5");
                    return false;
                }
                if($target->getName() == "oPinqzz"){
                    // UCV5 = UtriXCorev5
                    $player->sendMessage("§7| §6UtriX §7» §cYou Can't Freeze UtriX Founder, He Is my Programmer, Developer, Founder, Father, #UCV5");
                    return false;
                } else {
                    if($target->hasPermission("utrix.playerfrozen")){
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly UnFreezed The Player");
                        $utrix->setPermission($target)->unsetPermission("utrix.playerfrozen");
                    } else {
                        $utrix->freeze = true;
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly Freezed The Player");
                        $utrix->setPermission($target)->setPermission("utrix.playerfrozen", true);
                        if($target->getWorld()->getFolderName() !== "world"){
                            $target_pos = new Vector3($target->getPosition()->getX(), $target->getPosition()->getY(), $target->getPosition()->getZ());
                            $player->teleport($target_pos);
                        } else {
                            $target_pos = new Vector3($target->getPosition()->getX(), $target->getPosition()->getY(), $target->getPosition()->getZ());
                            $player->teleport($target_pos);
                        }
                    }
                }
            }

            

        });


        $form->setTitle("§6UtriX §7- §3Staff Freeze");
        $form->addInput("§7» §3Choose a Player To Freeze");
        $form->sendToPlayer($player);
        return $form;
    }

    public function GamesGUI($player){
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            if($data == null){
                return true;
            }

            switch($data){
                case 1:
                    $player->getInventory()->clearAll();
                    $utrix = UtriX::getPluginInstance();
                    $player->teleport($utrix->getServer()->getWorldManager()->getWorldByName("kitpvp")->getSafeSpawn());
                    $kitpvp = $utrix->getServer()->getPluginManager()->getPlugin("UtriXKitPvP");
                    $kitpvp->KitManager($player);
                    $utrix->remove($player);
                    $kitpvp->KitPvPScoreboard($player);
                    $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Reached KitPvP Server");
                    $player->sendTitle("§l§7<---§6UtriX§7--->", "§l§cfdeet men ygld :D");
    

                break;

                case 2:
                    $player->getInventory()->clearAll();
                    $utrix = UtriX::getPluginInstance();
                    $utrix->remove($player);
                    $player->teleport($utrix->getServer()->getWorldManager()->getWorldByName("fist")->getSafeSpawn());
                    $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Reached Fist Server");
                break;

                /*case 3:
                    $player->getInventory()->clearAll();
                    $utrix = UtriX::getPluginInstance();
                    $utrix->remove($player);
                    $player->teleport($utrix->getServer()->getWorldManager()->getWorldByName("redstonepvp")->getSafeSpawn());
                    $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Reached RedstonePvP Server");
                break;*/

            }


        });
        
        $form->setTitle("§6UtriX §7- §eGames");
        $form->setContent("Choose Whatever You Want !");
        $form->addButton("§7» §cExit", 0, "textures/gamesui/exit");
        $form->addButton("§7» §aKit§4PvP", 0, "textures/gamesui/pvp");
        $form->addButton("§7» §5Fist", 0, "textures/gamesui/fist");
        //$form->addButton("§7» §4Redstone§CPvP \n §7» §l§5NEW !");
        $form->sendToPlayer($player);
        return $form;
    }

    public function Gadgets($player) {
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            if($data == null){
                return true;
            }

            switch($data){

                case 1:
                    $this->CapesGUI($player);
                break;

                case 2:
                    if($player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $this->NickGUI($player);
                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, Please Upgrade Your Rank To Use This, It Should Be A Rank From Tier 2 at Least");
                        return false;
                    }
                break;

                case 3:
                    $this->FlyGUI($player);
                break;
                
                case 4:
                    if($player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $this->DailyGUI($player);
                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, Please Upgrade Your Rank To Use This, It Should Be A Rank From Tier 2 at Least");
                        return false;
                    }
                break;

                case 5:
                    $utrix = UtriX::getPluginInstance();
                    $utrix->ChangePlayerName($player, $player->getName());
                    $utrix->setCape($player, "");
                    $player->setAllowFlight(false);
                    $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Reseted All the Gadgets");
                break;

            }
        });
        $no = 0;
        $available = "§cUnAvailable";
        if($player->hasPermission("utrix.tier1")){
            $no = 5;
        }
        if($player->hasPermission("utrix.tier2")){
            $no = 10;
        }
        if($player->hasPermission("utrix.tier3")){
            $no = 15;
        }

        if($player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
            $available = "§aAvailable";
        }

        $form->setTitle("§6UtriX §7- §eGadgets");
        $form->addButton("§7» §cExit");
        $form->addButton("§7» §3Capes\n§a$no §7/ §c15 §aAvailable");
        $form->addButton("§7» §2Nickname\n$available");
        $form->addButton("§7» §6Fly\n§aAvailable");
        $form->addButton("§7» §eDaily\n$available");
        $form->addButton("§7» §4Reset All The Gadgets\n§aAvailable");
        $form->sendToPlayer($player);
        return $form;
    }

    public function CapesGUI($player){
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            if($data == null){
                return true;
            }
            $utrix = UtriX::getPluginInstance();


            switch($data){
                case 1:
                    if($player->hasPermission("utrix.tier1") || $player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "bluecreeper");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly Equiped the Blue Creeper Cape !");
                    } 
                break;

                case 2:
                    if($player->hasPermission("utrix.tier1") || $player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "enderman");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly Equiped The Enderman Cape");

                    }
                break;

                case 3:
                    if($player->hasPermission("utrix.tier1") || $player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "founder");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped Founder Cape");
                    }
                break;

                case 4:
                    if($player->hasPermission("utrix.tier1") || $player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "turtle");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped Turtle Cape");
                    }
                break;

                case 5:
                    if($player->hasPermission("utrix.tier1") || $player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "smile");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly Equiped Smile Cape");
                    }
                break;

                case 6:
                    if($player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "sword");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped Sword Cape");
                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have Permissions for That");
                        return false;
                    }
                break;

                case 7:
                    if($player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "skymines");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped SkyMines Cape");
                    }else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have Permissions for That");
                        return false;
                    }
                break;

                case 8:
                    if($player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "console");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped Console Cape");
                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have Permissions for That");
                        return false;
                    }
                break;

                case 9:
                    if($player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "prison");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped Prison Cape");
                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have Permissions for That");
                        return false;
                    }
                break;

                case 10:
                    if($player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "grass");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped Grass Cape");
                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have Permissions for That");
                        return false;
                    }
                break;

                case 11:
                    if($player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "watch");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly , Equiped Watch Cape");

                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have Permissions for That");
                        return false;
                    }
                break;

                case 12:
                    if($player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "sand");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped Sand Cape");
                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have Permissions for That");
                        return false;
                    }
                break;

                case 13:
                    if($player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "sky");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped Sky Cape");
                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have Permissions for That");
                        return false;
                    }
                break;

                case 14:
                    if($player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "rails");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped Rails Cape");
                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have Permissions for That");
                        return false;
                    }
                break;

                case 15:
                    if($player->hasPermission("utrix.tier3")){
                        $utrix->createCape($player, "forset");
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Equiped Forest Cape");
                    } else {
                        $player->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have Permissions for That");
                        return false;
                    }
                break;
                
                case 16:
                    $utrix->setCape($player, "");
                    $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly Reseted");
                break;
            }
        });

        $form->setTitle("§6UtriX §7- §3Capes");
        $form->addButton("§7» §cExit");
        if($player->hasPermission("utrix.tier1")){
            $form->addButton("§7» §3Blue Creeper\n§aAvailable");
            $form->addButton("§7» §dEnderman\n§aAvailable");
            $form->addButton("§7» §6Founder\n§aAvailable");
            $form->addButton("§7» §2Turtle\n§aAvailable");
            $form->addButton("§7» §eSmile\n§aAvailable");
            $form->addButton("§7» §4Sword\n§cUnAvailable");
            $form->addButton("§7» §aSkyMines\n§cUnAvailable");
            $form->addButton("§7» §6Console\n§cUnAvailable");
            $form->addButton("§7» §7Prison\n§cUnAvailable");
            $form->addButton("§7» §2Grass\n§cUnAvailable");
            $form->addButton("§7» §3Watch\n§cUnAvailable");
            $form->addButton("§7» §eSand\n§cUnAvailable");
            $form->addButton("§7» §bSky\n§cUnAvailable");
            $form->addButton("§7» §7Rails\n§cUnAvailable");
            $form->addButton("§7» §2Forest\n§cUnAvailable");
        } else {
            if($player->hasPermission("utrix.tier2")){
                $form->addButton("§7» §3Blue Creeper\n§aAvailable");
                $form->addButton("§7» §dEnderman\n§aAvailable");
                $form->addButton("§7» §6Founder\n§aAvailable");
                $form->addButton("§7» §2Turtle\n§aAvailable");
                $form->addButton("§7» §eSmile\n§aAvailable");
                $form->addButton("§7» §4Sword\n§aAvailable");
                $form->addButton("§7» §aSkyMines\n§aAvailable");
                $form->addButton("§7» §6Console\n§aAvailable");
                $form->addButton("§7» §7Prison\n§aAvailable");
                $form->addButton("§7» §2Grass\n§aAvailable");
                $form->addButton("§7» §3Watch\n§cUnAvailable");
                $form->addButton("§7» §eSand\n§cUnAvailable");
                $form->addButton("§7» §bSky\n§cUnAvailable");
                $form->addButton("§7» §7Rails\n§cUnAvailable");
                $form->addButton("§7» §2Forest\n§cUnAvailable");
            } else {
                if($player->hasPermission("utrix.tier3")){
                    $form->addButton("§7» §3Blue Creeper\n§aAvailable");
                    $form->addButton("§7» §dEnderman\n§aAvailable");
                    $form->addButton("§7» §6Founder\n§aAvailable");
                    $form->addButton("§7» §2Turtle\n§aAvailable");
                    $form->addButton("§7» §eSmile\n§aAvailable");
                    $form->addButton("§7» §4Sword\n§aAvailable");
                    $form->addButton("§7» §aSkyMines\n§aAvailable");
                    $form->addButton("§7» §6Console\n§aAvailable");
                    $form->addButton("§7» §7Prison\n§aAvailable");
                    $form->addButton("§7» §2Grass\n§aAvailable");
                    $form->addButton("§7» §3Watch\n§aAvailable");
                    $form->addButton("§7» §eSand\n§aAvailable");
                    $form->addButton("§7» §bSky\n§aAvailable");
                    $form->addButton("§7» §7Rails\n§aAvailable");
                    $form->addButton("§7» §2Forest\n§aAvailable");
                }
            }
        }
        $form->addButton("§7» §cReset");
        $form->sendToPlayer($player);
        return $form;
    }


    public function NickGUI($player){
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createCustomForm(function (Player $player, array $data = null){
            if(!isset($data[0])){
                $player->sendMessage("§7| §6UtriX §7» §cSorry, But You Should Provide a Name");
                return false;
            } else {
                $utrix = UtriX::getPluginInstance();
                $nick_name = strval($data[0]);
                $rank_prefix = $utrix->ranks->get($utrix->getData($player, null)->get("Rank"));
                $rank_color = substr($rank_prefix, 0, -intval(strlen(substr($rank_prefix, 3, 0))));
                
                if($nick_name == "oPinqzz"){
                    $player->sendMessage("§7| §6UtriX §7» §cSorry, This is UtriX Founder & CEO & Lead Developer Name, You Can't Use it ");
                    return false;
                } else {
                
                    if($player->hasPermission("utrix.tier2") || $player->hasPermission("utrix.tier3")){
                        $utrix->ChangePlayerName($player, $nick_name);
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Changed Your Name to §e$nick_name, §aWith Your Rank Prefix.");
                        // echo substr($rank_prefix, 3, 0);
                        
                    }
                }
            }
        });

        $form->setTitle("§6UtriX §7- §2Nick");
        $form->addInput("§7» Please Enter a Name");
        $form->sendToPlayer($player);
        return $form;
    }


    public function FlyGUI($player){
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            if($data == null){
                return true;
            }
            switch($data){
                case 1:
                    $utrix = UtriX::getPluginInstance();
                    
                    if(!in_array($player->getName(), $utrix->rfly)){
                        $player->setAllowFlight(true);
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Enabled Fly");
                        array_push($utrix->rfly, $player->getName());
                    } else {
                        $player->setAllowFlight(false);
                        $player->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Disabled Fly");
                        unset($utrix->rfly[array_search($player->getName(), $utrix->rfly)]);
                    }
                break;
                
            }
        });

        $form->setTitle("§6UtriX §7- §6Fly");
        $form->addButton("§7» §cExit");
        $form->addButton("§7» §aEnable§7/§cDisable §6Fly");
        $form->sendToPlayer($player);
        return $form;
    }

    public function DailyGUI($player){
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            if($data == null){
                return true;
            } 
            switch($data){
                case 1:
                    $utrix = UtriX::getPluginInstance();
                    if($player->hasPermission("utrix.tier3")){
                        $number = rand(200, 800);
                        $player_data = UtriX::getPluginInstance()->getData(null, $player->getName());
                        if($player_data->get("DailyStatus") !== "Null"){
                            if($player_data->get("DailyStatus") < date("d/m/y:h:i:s")){
                                $period = strtotime("+ 1 day");
                                $player_data->set("DailyStatus", date("d/m/y:h:i:s", $period));
                                $player_data->set("UCoins", $player_data->get("UCoins") + $number);
                                $player_data->save();
                                $player->sendMessage("§7| §6UtriX §7» §aYou Have Got $number$ UCoins, Here You Go");
    
                            } else {
                                $player->sendMessage("§7| §6UtriX §7» §cSorry, This Command Is Only Used Every 24 Hours");
                                return false;
                            }
                        } else {
                            $period = strtotime("+ 1 day");
                            $player_data->set("DailyStatus", date("d/m/y:h:i:s", $period));
                            $player_data->set("UCoins", $player_data->get("UCoins") + $number);
                            $player_data->save();
                            $player->sendMessage("§7| §6UtriX §7» §aYou Have Got $number$ UCoins, Here You Go");
                        }
                    } else {
                        if($player->hasPermission("utrix.tier2")){
                            $number = rand(100, 400);
                            $player_data = UtriX::getPluginInstance()->getData(null, $player->getName());
                            if($player_data->get("DailyStatus") !== "Null"){
                                if($player_data->get("DailyStatus") < date("d/m/y:h:i:s")){
                                    $period = strtotime("+ 1 day");
                                    $player_data->set("DailyStatus", date("d/m/y:h:i:s", $period));
                                    $player_data->set("UCoins", $player_data->get("UCoins") + $number);
                                    $player_data->save();
                                    $player->sendMessage("§7| §6UtriX §7» §aYou Have Got $number$ UCoins, Here You Go");
        
                                } else {
                                    $player->sendMessage("§7| §6UtriX §7» §cSorry, This Command Is Only Used Every 24 Hours");
                                    return false;
                                }
                            } else {
                                $period = strtotime("+ 1 day");
                                $player_data->set("DailyStatus", date("d/m/y:h:i:s", $period));        
                                $player_data->set("UCoins", $player_data->get("UCoins") + $number);
                                $player_data->save();
                                $player->sendMessage("§7| §6UtriX §7» §aYou Have Got $number$ UCoins, Here You Go");
                            }
                        }
                    }
                break;
            }
        });
        $form->setTitle("§6UtriX §7- §eDaily");
        $form->addButton("§7» §cExit");
        $form->addButton("§7» §aGet Your Daily");
        $form->sendToPlayer($player);
        return $form;
    }


    public function ProfileGUI($player){
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            if($data == null){
                return true;
            }

            switch($data) {

                case 1:
                    $this->ShowYourRankStatusGUI($player);
                break;

            }
        });

        $form->setTitle("§6UtriX §7- §bProfile");
        $form->addButton("§7» §cExit");
        $rank = $utrix->ranks->get($utrix->getData($player, null)->get("Rank"));
        $ucoins = $utrix->getData($player, null)->get("UCoins");
        $network_level = $utrix->NetworkLevel($player);
        $clan = $utrix->getData($player)->get("Clan");
        $form->setContent("§7» §6Your Profile » \n§7» §6Your Rank§7: $rank\n§r§7» §6Your Network Level§7: §6$network_level\n§7» §6Your UCoins§7: §6$ucoins\n§7» §6Your Clan§7: §6$clan");
        $form->addButton("§7» §bShow Your Rank Status");
        $form->sendToPlayer($player);
        return $form;
    }


  

    public function ShowYourRankStatusGUI($player){
        $utrix = UtriX::getPluginInstance();
        $api = $utrix->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createCustomForm(function (Player $player, array $data = null){
            if(!isset($data[0])){
                return true;
            }
        });
        $player_rank = $utrix->ranks->get($utrix->getData($player, null)->get("Rank"));
        $form->setTitle("§6UtriX §7- §bRank Status");
        $form->addLabel("§6Your Rank §7» $player_rank");
        $form->addLabel("§6Your Rank Period §7» §aLifetime");
        $form->addLabel("§6Your Rank Stats §7» §aActive");
        $form->sendToPlayer($player);
        return $form;
    }



    

   
















}