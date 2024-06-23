<?php

namespace UtriXDevelopers;

use pocketmine\player\Player;
use pocketmine\Server;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\utils\Config;
use pocketmine\world\World;
use pocketmine\world\WorldManager;
use pocketmine\utils\TextFormat;
use pocketmine\player\GameMode;
use UtriXDevelopers\UtriXGUIs;
use UtriXDevelopers\UtriX;

class UtriXCommands implements CommandExecutor
{
	public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args) : bool
	{
		if($cmd->getName() == "rank")
		{
			
				if($sender->hasPermission("utrix.highstaff"))
				{
					$utrix = UtriX::getPluginInstance();
					if(!isset($args[0])){
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, Usage : §e/rank help");
						return false;
					} else {
						if($args[0] == "help"){
							$sender->sendMessage("§7| §6UtriX §7» §3Rank System Help List");
							$sender->sendMessage("§7| §6UtriX §7» §aFor Creating a Rank : §3/rank create {rank name} {rank prefix}");
							$sender->sendMessage("§7| §6UtriX §7» §aFor Adding Permissions for Ranks : §3/rank setperms {rank name} {permission}");
							$sender->sendMessage("§7| §6UtriX §7» §aFor Removing Permissions for Ranks : §3/rank removeperms {rank name} {permission}");
							$sender->sendMessage("§7| §6UtriX §7» §aFor §4Deleting §aThe Rank : §3/rank delete");
							$sender->sendMessage("§7| §6UtriX §7» §aFor Editing Rank Prefix : §3/rank editprefix {rank name} {new prefix}");
							$sender->sendMessage("§7| §6UtriX §7» §aFor Setting SomeOne a Rank : §3/rank set {player name} {rank name}");
						}


						if($args[0] == "create"){
							if(!isset($args[1]) || !isset($args[2])){
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, Please Use The Command : §e/rank help");
								return false;
							} else {
								if($utrix->ranks->exists($args[1], true)){
									$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Rank Is Created Already");
									return true;
								} else {
									$rank_name = strval($args[1]);
									$rank_prefix = strval($args[2]);
									$utrix->ranks->set($rank_name, $rank_prefix);
									$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Created The Rank By Name : $rank_name & Prefix : $rank_prefix");
									$utrix->ranks->save();
								}
							}
						}


						if($args[0] == "setperms"){
							if(!isset($args[1]) || !isset($args[2])){
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, Please Use the Command : §3/rank help");
							} else {

								$rank_name = strval($args[1]);
								$permission = strval($args[2]);
								if(!$utrix->ranks->exists($args[1], true)){
									$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Rank Doesn't Exist");
									return false;
								} else {
									if($utrix->permissions->exists($rank_name, true)){
										if(in_array($permission, $utrix->permissions->get($rank_name))){
											$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Permission Already Exists");
											return false;
										} else {
											$perms = $utrix->permissions->get($rank_name, []);
											$perms[] = $permission;
											// $perms[] = $utrix->permissions->get($rank_name);
											$utrix->permissions->set($rank_name, $perms);
											$utrix->permissions->save();
											$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Edited & Added Permissions for the Rank " . $utrix->ranks->get($rank_name));
											$online = $utrix->getServer()->getOnlinePlayers();
											foreach($online as $p){
												$p_data = $utrix->getData(null, $p->getName());
												if($p_data->get("Rank") == $rank_name){
													foreach($utrix->permissions->get($rank_name) as $perm){
														$utrix->setPermission($p)->setPermission($perm, true);
													}
												}
											}
										}
									} else {
										$perms = $utrix->permissions->set($rank_name, []);
										$perms[] = $permission;
										$utrix->permissions->set($rank_name, $perms);
										$utrix->permissions->save();
										$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Created & Added Permissions for the Rank " . $utrix->ranks->get($rank_name));
									}
								}
							
							}
						}

						if($args[0] == "removeperms"){
							if(!isset($args[1]) || !isset($args[2])){
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, Please Use the Command : §3/rank help");
								return false;
							} else {
								$utrix = UtriX::getPluginInstance();
								$rank_name = strval($args[1]);
								$permission = strval($args[2]);

								if(!$utrix->ranks->exists($rank_name, true)){
									$sender->sendMessage("§7| §6UtriX §7» §cSorry, The Rank Doesn't Exist");
								} else {

									if(!$utrix->permissions->exists($rank_name, true)){
										$sender->sendMessage("§7| §6UtriX §7» §cSorry , This Rank Doesn't Have Any Permissions");
										return false;
									} else {
										if(!in_array($permission, $utrix->permissions->get($rank_name))){
											$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Permission Doesn't Exist");
											return false;
										} else {
											$ranks = array_values(array_diff($utrix->permissions->get($rank_name), [$permission]));
											$utrix->permissions->set($rank_name, $ranks);
											$utrix->permissions->save();
											$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Removed the Permission $permission from $rank_name Rank");
											$online = $utrix->getServer()->getOnlinePlayers();
											foreach($online as $p){
												$p_data = $utrix->getData(null, $p->getName());
												if($p_data->get("Rank") == $rank_name){
													$utrix->setPermission($p)->unsetPermission($permission);
													
												}
											}
										}
									}

								}
							}
						}

						if($args[0] == "delete"){
							$utrix = UtriX::getPluginInstance();
							if(!isset($args[1])){
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, Invaild Input, Please Use the Command : §3/rank help");
								return false;
							} else {
								$rank_name = strval($args[1]);
								if(!$utrix->ranks->exists($rank_name, true)){
									$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Rank Doesn't Exists To Delete it");
									return false;
								} else {
									if($rank_name == "Member"){
										$sender->sendMessage("§7| §6UtriX §7» §cSorry, Can't Delete a Default Rank");
										return false;
									} else {
										if(!$utrix->permissions->exists($rank_name, true)){
											$sender->sendMessage("§7| §6UtriX §7» §aNo Permissions Found, Skipping....");
											foreach($utrix->getServer()->getOnlinePlayers() as $p){
												$p_data = $utrix->getData(null, $p->getName());
												if($p_data->get("Rank") == $rank_name){
													$p_data->set("Rank", "Member");
													$p_data->save();
													$permissions = $utrix->permissions->get("Member");
													$new_perms = array_values(array_diff($utrix->permissions->get($rank_name), $permissions));
													foreach($new_perms as $perm){
														$utrix->setPermission($p)->unsetPermission($perm);
													}
												}
											}
											$utrix->ranks->remove($rank_name);
											$utrix->permissions->remove($rank_name);
											$utrix->ranks->save();
											$utrix->permissions->save();

											$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Deleted the Rank");
										} else {
											$sender->sendMessage("§7| §6UtriX §7» §aPermissions Found, Deleting....");
											foreach($utrix->getServer()->getOnlinePlayers() as $p){
												$p_data = $utrix->getData(null, $p->getName());
												if($p_data->get("Rank") == $rank_name){
													$p_data->set("Rank", "Member");
													$p_data->save();
													$permissions = $utrix->permissions->get("Member");
													$new_perms = array_values(array_diff($utrix->permissions->get($rank_name), $permissions));
													foreach($new_perms as $perm){
														$utrix->setPermission($p)->unsetPermission($perm);
													}
												}
											}
											$utrix->ranks->remove($rank_name);
											$utrix->permissions->remove($rank_name);
											$utrix->ranks->save();
											$utrix->permissions->save();

											$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Deleted the Rank");

											
										}
									}
								}
							}
						}


						if($args[0] == "editprefix"){
							$utrix = UtriX::getPluginInstance();
							if(!isset($args[1]) || !isset($args[2])){
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, Invaild Input, Please Use the Command : §3/rank help");
								return false;
							} else {
								$rank_name = strval($args[1]);
								$new_prefix = strval($args[2]);

								if(!$utrix->ranks->exists($rank_name, true)){
									$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Rank Doesn't Exist or Have Been Deleted");
									return false;
								} else {
									$utrix->ranks->set($rank_name, $new_prefix);
									$utrix->ranks->save();

									$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Changed $rank_name's Prefix to $new_prefix");
								}
							}
						}

						if($args[0] == "set"){
							if(!isset($args[1]) || !isset($args[2])){
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, Invaild Input, Please Use the Command : §3/rank help");
								return false;
							} else {
								$pname = strval($args[1]);
								$rank_name = strval($args[2]);
								if(!$utrix->ranks->exists($rank_name, true)){
									$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Rank Doesn't Exists");
									return false;
								} else {
									$target = $utrix->getServer()->getPlayerExact($pname);
									if($target == null){
										$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Player is Offline");
										return false;
									} else {

										if(!$utrix->ranks->exists($rank_name, true)){
											$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Rank Does not Exist");
											return false;
										} else {
											if(!$utrix->permissions->exists($rank_name, true)){
												$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Rank Doesn't Have Any Permissions");
												return false;
											} else {
												$target_data = $utrix->getData(null, $target->getName());
												if($target_data->get("Rank") !== "Member"){
													foreach($utrix->permissions->get($target_data->get("Rank")) as $pe){
														$utrix->setPermission($target)->unsetPermission($pe);

													}
													
													//echo "DONE";
													$utrix->ChangePlayerName($target, $target->getName());
													$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Changed the Rank For the Player " . $target->getName() . " To $rank_name");
												} else {
													foreach($utrix->permissions->get($rank_name) as $perm){
														$utrix->setPermission($target)->setPermission($perm, true);
														
													}
												}												
												$target_data = $utrix->getData(null, $target->getName());
												$target_data->set("Rank", $rank_name);
												$target_data->save();
												foreach($utrix->permissions->get($target_data->get("Rank")) as $perm){
													$utrix->setPermission($target)->setPermission($perm, true);

												}
												// echo "K";
											}
										}
									}
								}
							}
						}





						

					}
					
					
				}
			
		}


		if($cmd->getName() == "clan"){
			if(!isset($args[0])){
				// echo "MEOW";
				$sender->sendMessage("§7| §6UtriX §7» §cSorry An Error Occured, Please Use the Command : §e/clan help");
				return false;
			} else {
				if($args[0] == "help") {
					$sender->sendMessage("§7| §6UtriX §7» §e Clan Help List");
					$sender->sendMessage("§7| §6UtriX §7» §aFor Creating a Clan : §e/clan create {clan name} {clan tag}");
					$sender->sendMessage("§7| §6UtriX §7» §aFor Editing Clan's Tag : §e/clan edittag {new tag}");
					$sender->sendMessage("§7| §6UtriX §7» §aFor §4Deleting §aThe Clan : §e/clan delete");
					$sender->sendMessage("§7| §6UtriX §7» §aFor Inviting Someone to Your Clan : §e/clan invite {player name}");
					$sender->sendMessage("§7| §6UtriX §7» §aFor Transferring Ownership of The Clan : /clan transfer {player name}");
					$sender->sendMessage("§7| §6UtriX §7» §aFor Reading the Rules Before Creating a Clan : §e/clan rules");
					$sender->sendMessage("§7| §6UtriX §7» §aFor Accepting a Clan Invite : §e/clan accept {clan name}");
					$sender->sendMessage("§7| §6UtriX §7» §aFor Declining a Clan Invite : §e/clan decline {clan name}");
					$sender->sendMessage("§7| §6UtriX §7» §aFor Quiting a Clan : §e/clan quit");
					$sender->sendMessage("§7| §6UtriX §7» §aFor Kicking Some One from the Clan : §e/clan kick {player name}");
					//$sender->sendMessage("§7| §6UtriX §7» §aFor Listing Members In Your Clan  : §e/clan list");

				}

				if($args[0] == "create"){
					$utrix = UtriX::getPluginInstance();
					if(!isset($args[1])){
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Occured, Please Use the Command : §e/clan help");
						return false;
					} else {
						if(!isset($args[2])){
							$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Occured, Please Use the Command : §e/clan help");
							return false;
						} else {
							if($utrix->clanowners->exists($args[1], true)){
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Clan Already Exists");
								return false;
							} else {
								$clan_name = strval($args[1]);
								$clan_tag = strval($args[2]);
	
								$sender_data = $utrix->getData(null, $sender->getName());
								$sender_data->set("Clan", $clan_name);
								$utrix->clanowners->set($clan_name, $sender->getName());
								$utrix->clantags->set($clan_name, $clan_tag);
								$utrix->clanmembers->set($clan_name, $sender->getName() . ", ");
	
								$sender_data->save();
								$utrix->clanowners->save();
								$utrix->clantags->save();
								$utrix->clanmembers->save();
	
								$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Created Your Clan, By Name : $clan_name, By Tag : $clan_tag");
								$utrix->ChangePlayerName($sender, $sender->getName());	

							}

						}
					}
				}

				if($args[0] == "edittag"){
					$utrix = UtriX::getPluginInstance();

					if(!isset($args[1])){
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Occured, Please Use the Command : §e/clan help");
						return false;
					} else {
						$sender_data = $utrix->getData(null, $sender->getName());
						if($utrix->clanowners->get($sender_data->get("Clan")) == $sender->getName()){
							if($sender_data !== "NONE"){
								$new_clan_tag = strval($args[1]);
								
								$utrix->clantags->set($sender_data->get("Clan"), $new_clan_tag);
								$utrix->clantags->save();
								$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Edited Your Clan Tag to : $new_clan_tag");
								$utrix->ChangePlayerName($sender, $sender->getName());	
								
							} else {
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Occured, Please Create A Clan At First To Use this Command");
								return false;
							}
						} else {
							$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Occured Because You aren't the Owner of this Clan");
						}

					}
				}

				if($args[0] == "delete"){
					$utrix = UtriX::getPluginInstance();

					$sender_data = $utrix->getData(null, $sender->getName());
					if($utrix->clanowners->get($sender_data->get("Clan")) == $sender->getName()) {
						$utrix->clanowners->remove($sender_data->get("Clan"));
						$utrix->clantags->remove($sender_data->get("Clan"));
						$utrix->clanmembers->remove($sender_data->get("Clan"));
						$utrix->clanrequests->remove($sender_data->get("Clan"));

						$utrix->clanowners->save();
						$utrix->clantags->save();
						$utrix->clanmembers->save();
						$utrix->clanrequests->save();
						$utrix->ChangePlayerName($sender, $sender->getName());	

						$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly §4Deleted §aYour Clan, This Action Cannot Be Undone");
					} else {
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Occured, You Don't Own This Clan to Delete It");
						return false;
					}
				}

				if($args[0] == "invite"){
					$utrix = UtriX::getPluginInstance();

					$sender_data = $utrix->getData(null, $sender->getName());

					if(!isset($args[1])){
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Occured, Please Use the Command : §e/clan help");
						return false;
					} else {
						if($utrix->clanowners->get($sender_data->get("Clan")) == $sender->getName()){
							$pname = $args[1];

							$target = $utrix->getServer()->getPlayerExact($pname);

							if($target == null){
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Occured as the Player is Offline or Not Found at the Database!");
								return false;
							} else {
								$target_data = $utrix->getData(null, $target->getName());

								if($target_data->get("Clan") !== "NONE"){
									$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Player is At Other Clan");
									return false;
								} else {
									if($target->getName() == $sender->getName()){
										$sender->sendMessage("§7| §6UtriX §7» §cError 404, What are you trying to Do ?!, You Can't Invite Your Self !");
										return false;
									} else {
										$members = explode(", ", $utrix->clanmembers->get($sender_data->get("Clan")));
										
										$no_of_members = count($members);

										if($sender->hasPermission("utrix.tier1") || $sender->hasPermission("utrix.tier2") || $sender->hasPermission("utrix.tier3")){
											if($no_of_members < 15){
												$utrix->clanrequests->set($sender_data->get("Clan"), $utrix->clanrequests->get($sender_data->get("Clan")) . $target->getName() . ", ");
												$utrix->clanrequests->save();
												$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Sent a Clan Invite to The Player : §c" . $target->getName());
												$target->sendMessage("§7| §6UtriX §7» §ean Invite from The Player §c" . $sender->getName() . " §e To Join Clan : §b" . $sender_data->get("Clan"));
											} else {
												$sender->sendMessage("§7| §6UtriX §7» §cSorry, You Have Reached the Maximum Limit oF Members in your Clan");
											}
										} else {
											if($no_of_members = 8){
												$sender->sendMesssage("§7| §6UtriX §7» §cSorry. You Have Reached the Limit For Members In You Clan, Please If You Want to Increase the Number Purchase a Rank from our Discord");
												return false;
											} else {
												$utrix->clanrequests->set($sender_data->get("Clan"), $utrix->clanrequests->get($sender_data->get("Clan")) . $target->getName() . ", ");
												$utrix->clanrequests->save();
												$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Sent a Clan Invite to The Player : §c" . $target->getName());
												$target->sendMessage("§7| §6UtriX §7» §ean Invite from The Player §c" . $sender->getName() . " §e To Join Clan : §b" . $sender_data->get("Clan"));
											}
										}
										
										
									}
								}
							}
						} else {
							$sender->sendMessage("§7| §6UtriX §7» §cSorry, You aren't the Clan Owner To Have the Permission to Invite.");
							return false;
						}
					}
				}

				if($args[0] == "transfer"){
					$utrix = UtriX::getPluginInstance();
					
					$sender_data = $utrix->getData(null, $sender->getName());
					if(!isset($args[1])){
						$sender->sendMessage("§7| §6UtriX §7» §cError, Please Use the Command : §e/clan help");
						return false;
					} else {
						$pname = strval($args[1]);

						$target = $utrix->getServer()->getPlayerExact($pname);

						if($target == null){
							$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Player is Offline");
							return false;
						} else {
							$target_data = $utrix->getData(null, $target->getName());
							if($target_data->get("Clan") == "NONE" || $target_data->get("Clan") == $sender_data->get("Clan")){
								$utrix->clanowners->set($sender_data->get("Clan"), $target->getName());
								$utrix->clanowners->save();

								$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Moved Clan OwnerShip to The Other Player");
								$target->sendMessage("§7| §6UtriX §7» §aSuccessfuly, You Have Gained the OwnerShip of Clan " . $utrix->clantags->get($sender_data->get("Clan")));
								$target_data->set("Clan", $sender_data->get("Clan"));
								$target_data->save();
								$utrix->ChangePlayerName($sender, $sender->getName());	
								$utrix->ChangePlayerName($target, $target->getName());	
							} else {
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, You Can't Transfer the OwnerShip of a Clan to One in Another Clan");
								return false;
							}
						}
					}
				}

				if($args[0] == "rules"){
					$sender->sendMessage("§7| §6UtriX §7» §eClan Creation Rules");
					$sender->sendMessage("§7| §6UtriX §7» §eFirst, Don't Use Bad Names or Bad Tags");
					$sender->sendMessage("§7| §6UtriX §7» §eSecond, Don't Curse in the Clan Chat");
					$sender->sendMessage("§7| §6UtriX §7» §eThird, Don't Kick a Member from the Clan With Bad Reason, Every Thing is Under Survillance !");

				}

				if($args[0] == "accept"){
					if(!isset($args[1])){
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Occured, Please use Command : §e/clan help");
						return false;
					} else {
						$utrix = UtriX::getPluginInstance();
						$sender_data = $utrix->getData(null, $sender->getName());
						$clan_name = strval($args[1]);

						if($utrix->clanrequests->get($clan_name) == null){
							$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Clan Don't Exist");
							return false;
						} else {
							$exploded_requestes = explode(", ", $utrix->clanrequests->get($clan_name));
							foreach($exploded_requestes as $requests){
								if(strpos($requests, $sender->getName()) !== false){
									$utrix->clanrequests->set($clan_name, str_replace($sender->getName() . ", ", '', $utrix->clanrequests->get($clan_name)));
									$utrix->clanrequests->save();
									$utrix->clanmembers->set($clan_name, $utrix->clanmembers->get($clan_name) . $sender->getName() . ", ");
									$utrix->clanmembers->save();
									$sender_data->set("Clan", $clan_name);
									$sender_data->save();
									$utrix->ChangePlayerName($sender, $sender->getName());	
									$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Accepted the Clan Invitation");
								} else {
									$sender->sendMessage("§7| §6UtriX §7» §cSorry , You Aren't Invited to this Clan");
									return false;

								}
							}
						}
						
					}
				}

				if($args[0] == "decline"){
					if(!isset($args[1])){
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Has Occured, Please Use Command : §e/clan help");
						return false;
					} else {
						$utrix = UtriX::getPluginInstance();
						$sender_data = $utrix->getData(null, $sender->getName());
						$clan_name = strval($args[1]);

						if($utrix->clanrequests->get($clan_name) == null){
							$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Clan Don't Exist");
							return false;
						} else {
							$exploded_requestes = explode(", ", $utrix->clanrequests->get($clan_name));
							foreach($exploded_requestes as $requests){
								if(strpos($requests, $sender->getName()) !== false){
									$utrix->clanrequests->set($clan_name, str_replace($sender->getName() . ", ", '', $utrix->clanrequests->get($clan_name)));
									$utrix->clanrequests->save();
									$utrix->ChangePlayerName($sender, $sender->getName());	
									$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Declined the Clan Invitation");
								} else {
									$sender->sendMessage("§7| §6UtriX §7» §cSorry , You Aren't Invited to this Clan");
									return false;

								}
							}
						}
					}
				}

				if($args[0] == "quit"){
					$utrix = UtriX::getPluginInstance();

					$sender_data = $utrix->getData(null, $sender->getName());

					if($sender_data->get("Clan") !== "NONE"){
						$members_of_the_clan = $utrix->clanmembers->get($sender_data->get("Clan"));
						$each_member = explode(", ", $members_of_the_clan);
						if($utrix->clanowners->get($sender_data->get("Clan")) == $sender->getName()){
							$sender->sendMessaage("§7| §6UtriX §7» §cSorry, You Can't Quit a Clan That You Own");
							return false;

						} else {
							foreach($each_member as $m){
								if(strpos($m, $sender->getName()) !== false){
									$utrix->clanmembers->set($sender_data->get("Clan"), str_replace($sender->getName() . ", ", '', $utrix->clanmembers->get($sender_data->get("Clan"))));
									$utrix->clanmembers->save();
									
									$sender_data->set("Clan", "NONE");
									$sender_data->save();
									$utrix->ChangePlayerName($sender, $sender->getName());	

									$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Quited the Clan");
								}
							}
						}
					} else {
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Have A Clan or Joined a Clan to Do this Command");
						return false;
					}
					
				}

				if($args[0] == "kick"){
					if(!isset($args[1])){
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, An Error Occured, Please Use Command : §e/clan help");
						return false;
					} else {
						$utrix = UtriX::getPluginInstance();
						$pname = strval($args[1]);
						$target = $utrix->getServer()->getPlayerExact($pname);

						if($target == null){
							$sender->sendMessage("§7| §6UtriX §7» §cSorry , This Player is Offline");
							return false;
						} else {
							$sender_data = $utrix->getData(null, $sender->getName());
							$target_data = $utrix->getData(null, $sender->getName());

							if($target_data->get("Clan") == $sender_data->get("Clan") && $utrix->clanowners->get($sender_data->get("Clan")) == $sender->getName()){
								$utrix->clanmembers->set($sender_data->get("Clan"), str_replace($target->getName() . ", ", '', $utrix->clanmembers->get($sender_data->get("Clan"))));
								$utrix->clanmembers->save();
								$target_data->set("Clan", "NONE");
								$target_data->save();
								$utrix->ChangePlayerName($sender, $sender->getName());	
								$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Kicked this Member out of You Clan !");
							} else {
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Member is not at Your Clan or You Don't Have the OwnerShip of this Clan");
								return false;
							}
						}
					}
				}

				


				




				

				
				
			}

		}

		

		if($cmd->getName() == "discord"){
			$sender->sendMessage("§7| §6UtriX §7» §6Here You Go , This Is Our Discord Link !, https://discord.gg/TqDFxC4xbm");
		}


		if($cmd->getName() == "ping"){
			$sender->sendMessage("§7| §6UtriX §7» §6Pong !, Your Ping is : " . UtriX::getPluginInstance()->getPing($sender));
		}
		

		
		if($cmd->getName() == "staff")
		{
			if($sender->hasPermission("utrix.staff"))
			{
				$utrixgui = new UtriXGUIs;
				$utrixgui->StaffGUI($sender);
			} 
		}

		
		if($cmd->getName() == "report")
		{
			$utrix = UtriX::getPluginInstance();
			
			if(!isset($args[1]))
			{
				$sender->sendMessage("§7| §6UtriX §7» §cError, Please Enter a Reason As Hacking");
				return false;
			}
			if(!isset($args[0]))
			{
				$sender->sendMessage("§7| §6UtriX §7» §cError, Please Enter a Vaild Player Name");
				return false;
			} else {
				$targetnames = $args[0];
				$playername = $utrix->getServer()->getPlayerExact($targetnames);
				$reason = $args[1];
				if($playername == null){
					$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Player is Offline");
				} else {
					$sender->sendMessage("§7| §6UtriX §7» §aSent Your Report Successfuly to UtriX Moderators");
					$ubot = new UtriXBot;
					$ubot->SendReports($reason, $playername->getName(), $sender->getName());
					foreach($utrix->getServer()->getOnlinePlayers() as $staff)
					{
						if($staff instanceof Player)
						{
							if($staff->hasPermission("utrix.staff"))
							{
								$staff->sendMessage("§7| §6UtriX §7» §a" . $sender->getName() . " §6Reported §c" . $playername->getName() . " §6For §c" . $reason);
							}
						}
					}
				}
			}
			
		}

		if($cmd->getName() == "protection") { 
			if(!isset($args[0]) || !isset($args[1])){
				$sender->sendMessage("§7| §6UProtection §7» §aSorry, /protection {worldname} {number of spawn blocks}");
				return false;
			} else {
				$worldname = $args[0];
				$no_of_blocks = $args[1];

				$utrix = UtriX::getPluginInstance();
				$utrix->protection->set(strtolower($worldname), $no_of_blocks);
				$utrix->protection->save();
				$sender->sendMessage("§7| §6UProtection §7» §aDone, Sir !");
			}
		}

		if($cmd->getName() == "uban")
		{
			$utrix = UtriX::getPluginInstance();
			

			if($sender->hasPermission("utrix.staff"))
			{
				
				
				if(!isset($args[0]) || !isset($args[1]) || !isset($args[2]))
				{
					$sender->sendMessage("§7| §6UtriX §7» §cError, Usage : §4/ban {player name} {period} {reason}");
					return false;
				} else {
					$player = $args[0];
					$target = $utrix->getServer()->getPlayerExact($player);
					if($player == null){
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Player Seems To Be Offline");
						return false;
					} else {
						$period = $args[1];
						$reason = $args[2];
						
						
						if(strpos($period, "w") !== false){
							$period = strtotime("+". strval(intval($args[1]) . " weeks"));
							$unbanned_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnBannedIn", $unbanned_on);
							$target_data->set("BanStatus", "True");

							$target_data->save();
							$sender_name = $sender->getName();
							$appealcode = $utrix->GenerateAppealCode();
							$target_data->set("AppealCode", $appealcode);
							$target_data->set("Banned_By", $sender->getName());
							$target_data->set("BanReason", $reason);
							$target_data->save();
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();
							$target->kick("§7| §6UtriX Network §7» §aYou Have Been Banned Until §4$unbanned_on §aBecause Of §e$reason §aAnd Banned By §3$sender_name");
							// $utrix->getServer()->getNameBans()->addBan($target->getName(), $reason, null);
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Banned The Player §c$target §aFor The Reason §3$reason");

								}
							}
						}
	
						if(strpos($period, "m") !== false){
							$period = strtotime("+". strval(intval($args[1]) . " months"));
							$unbanned_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnBannedIn", $unbanned_on);
							$target_data->set("BanStatus", "True");

							$target_data->save();
							$sender_name = $sender->getName();
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$target_data->set("Banned_By", $sender->getName());
							$target_data->set("BanReason", $reason);
							$target_data->save();
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();
							$target->kick("§7| §6UtriX Network §7» §aYou Have Been Banned Until §4$unbanned_on §aBecause Of §e$reason §aAnd Banned By §3$sender_name");
							// $utrix->getServer()->getNameBans()->addBan($target->getName(), $reason, null);
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Banned The Player §c$target §aFor The Reason §3$reason");

								}
							}
						}
						if(strpos($period, "y") !== false){
							$period = strtotime("+". strval(intval($args[1]) . " years"));
							$unbanned_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnBannedIn", $unbanned_on);
							$target_data->set("BanStatus", "True");

							$target_data->save();
							$sender_name = $sender->getName();
							$target_data->set("Banned_By", $sender->getName());
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$target_data->set("BanReason", $reason);
							$target_data->save();
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();
							$target->kick("§7| §6UtriX Network §7» §aYou Have Been Banned Until §4$unbanned_on §aBecause Of §e$reason §aAnd Banned By §3$sender_name");
							// $utrix->getServer()->getNameBans()->addBan($target->getName(), $reason, null);
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Banned The Player §c$target §aFor The Reason §3$reason");

								}
							}
						}
	
						if(strpos($period, "h") !== false){
							$period = strtotime("+". strval(intval($args[1]) . " hours"));
							$unbanned_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnBannedIn", $unbanned_on);
							$target_data->set("BanStatus", "True");

							$target_data->save();
							$sender_name = $sender->getName();
							$target_data->set("Banned_By", $sender->getName());
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$target_data->set("BanReason", $reason);
							$target_data->save();
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();

							$target->kick("§7| §6UtriX Network §7» §aYou Have Been Banned Until §4$unbanned_on §aBecause Of §e$reason §aAnd Banned By §3$sender_name");
							// $utrix->getServer()->getNameBans()->addBan($target->getName(), $reason, null);
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Banned The Player §c$target §aFor The Reason §3$reason");

								}
							}
						}
	
						if(strpos($period, "s") !== false){
							$period = strtotime("+". strval(intval($args[1]) . " seconds"));
							$unbanned_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnBannedIn", strval($unbanned_on));
							$target_data->set("BanStatus", "True");
							$target_data->save();
							$sender_name = $sender->getName();
							$target_data->set("Banned_By", $sender->getName());
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$target_data->set("BanReason", $reason);
							$target_data->save();
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$utrix->appealcodes->save();
							$target->kick("§7| §6UtriX Network §7» §aYou Have Been Banned Until §4$unbanned_on §aBecause Of §e$reason §aAnd Banned By §3$sender_name");
							// $utrix->getServer()->getNameBans()->addBan($target->getName(), $reason, null);
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aSuccessfuly, Banned The Player §c$target §aFor The Reason §3$reason");

								}
							}
						}
					
						
					}

					
				}
				
				

				
			}
		}

		if($cmd->getName() == "suggest"){
			if(!isset($args[0])){
				$sender->sendMessage("§7| §6UtriX §7» §cSorry, The Usage of the Command : §a/suggest 'your Suggestion Between Quotations'");
				return false;
			} else {
				$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Sent Your Suggestion To UtriX Founder, You Will Get a Reply Soon On The Discord");
			}
		}


		if($cmd->getName() == "daily"){
			if($sender->hasPermission("utrix.daily")){
				if($sender->hasPermission("utrix.platinum")){
					$number = rand(100, 600);
					$sender_data = UtriX::getPluginInstance()->getData(null, $sender->getName());
					if($sender_data->get("DailyStatus") !== "Null"){
						if($sender_data->get("DailyStatus") < date("d/m/y:h:i:s")){
							$sender_data->set("DailyStatus", date("d/m/y:h:i:s"));
							$sender_data->set("UCoins", $sender_data->get("UCoins") + $number);
							$sender_data->save();
							$sender->sendMessage("§7| §6UtriX §7» §aYou Have Got $number$ UCoins, Here You Go Man");

						} else {
							$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Command Is Only Used Every 24 Hours");
							return false;
						}
					} else {
						$sender_data->set("DailyStatus", date("d/m/y:h:i:s"));
						$sender_data->set("UCoins", $sender_data->get("UCoins") + $number);
						$sender_data->save();
						$sender->sendMessage("§7| §6UtriX §7» §aYou Have Got $number$ UCoins, Here You Go Man");
					}
				} else {
					if($sender->hasPermission("utrix.premium")){
						$number = rand(50, 300);
						$sender_data = UtriX::getPluginInstance()->getData(null, $sender->getName());
						if($sender_data->get("DailyStatus") !== "Null"){
							if($sender_data->get("DailyStatus") < date("d/m/y:h:i:s")){
								$sender_data->set("DailyStatus", date("d/m/y:h:i:s"));
								$sender_data->set("UCoins", $sender_data->get("UCoins") + $number);
								$sender_data->save();
								$sender->sendMessage("§7| §6UtriX §7» §aYou Have Got $number$ UCoins, Here You Go Man");
	
							} else {
								$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Command Is Only Used Every 24 Hours");
								return false;
							}
						} else {
							$sender_data->set("DailyStatus", date("d/m/y:h:i:s"));
							$sender_data->set("UCoins", $sender_data->get("UCoins") + $number);
							$sender_data->save();
							$sender->sendMessage("§7| §6UtriX §7» §aYou Have Got $number$ UCoins, Here You Go Man");
						}
					} else {
						if($sender->hasPermission("utrix.donator")){
							$number = rand(25, 150);
							$sender_data = UtriX::getPluginInstance()->getData(null, $sender->getName());
							if($sender_data->get("DailyStatus") !== "Null"){
								if($sender_data->get("DailyStatus") < date("d/m/y:h:i:s")){
									$sender_data->set("DailyStatus", date("d/m/y:h:i:s"));
									$sender_data->set("UCoins", $sender_data->get("UCoins") + $number);
									$sender_data->save();
									$sender->sendMessage("§7| §6UtriX §7» §aYou Have Got $number$ UCoins, Here You Go Man");
		
								} else {
									$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Command Is Only Used Every 24 Hours");
									return false;
								}
							} else {
								$sender_data->set("DailyStatus", date("d/m/y:h:i:s"));
								$sender_data->set("UCoins", $sender_data->get("UCoins") + $number);
								$sender_data->save();
								$sender->sendMessage("§7| §6UtriX §7» §aYou Have Got $number$ UCoins, Here You Go Man");
							}
						} else {
							$sender->sendMessage("§7| §6UtriX §7» §cSorry, You Don't Own A Rank Higher Than Member, Please If You Hope to Use this Command, Buy a Rank From Our Discord , /discord, Our Prices Start off 1.99$ !");
							return false;
						}
					}
				}
			}
		}


		if($cmd->getName() == "build")
		{
			$utrix = UtriX::getPluginInstance();
			if($sender->hasPermission("utrix.builder"))
			{
				if(count($args) < 1)
				{
					$sender->sendMessage("§7| §l§6UtriX §7» §cError, Usage : /build on/off");
					return false;
				} else {
					if($args[0] == "on")
					{
						$utrix->setPermission($sender)->setPermission("utrix.build", true);

						$sender->sendMessage("§7| §l§6UtriX §7» §aTurned On Build Mode");
					}
					if($args[0] == "off")
					{
						$utrix->setPermission($sender)->unsetPermission("utrix.build");

						$sender->sendMessage("§7| §l§6UtriX §7» §cTurned Off Build Mode");
					}
				}
			
				
				
			} else {
				$sender->sendMessage("§7| §l§6UtriX §7» §cYou Don't Have Permission to Do this Command");
			}
		}

		if($cmd->getName() == "hub" || $cmd->getName() == "lobby")
		{
			$utrix = UtriX::getPluginInstance();
			if($sender->getWorld()->getFolderName() == "kitpvp")
			{
				$kitpvp = $utrix->getServer()->getPluginManager()->getPlugin("UtriXKitPvP");
				$kitpvp->remove($sender);
			} 
			if($sender->getWorld()->getFolderName() == "kbpvp")
			{
				$kbpvp =  $utrix->getServer()->getPluginManager()->getPlugin("UtriXKnockBackPvP");
				$kbpvp->remove($sender);
			}
			if($sender->getWorld()->getFolderName() == "skymines")
			{
				$skymines = $utrix->getServer()->getPluginManager()->getPlugin("UtriXSkyMines");
				$skymines->savePlayerInventory($sender);
			}
			if($sender->getWorld()->getFolderName() == "redstonepvp")
			{

			}
			$sender->teleport($utrix->getServer()->getWorldManager()->getWorldByName("world")->getSafeSpawn());
			$sender->sendMessage("§7| §6UtriX §7» §aSent You to Lobby Server");
			$utrix->LobbyCoreItems($sender);
			$sender->sendTitle("§l§7<---§6UtriX§7--->", "§l§cfdeet men yl3b :D");
			$sender->getOffHandInventory()->clearAll();
			$utrix->UtriXScoreBoard($sender);
			$sender->getArmorInventory()->clearAll();
			$sender->getEffects()->clear();
			

		}
		
		if($cmd->getName() == "fly")
		{
			if($sender->hasPermission("utrix.ranksfly"))
			{
				if($sender->getWorld()->getFolderName() == "world")
				{
					if(count($args) < 1)
					{
						$sender->sendMessage("§7| §l§6UtriXFlight §7» §cError, Usage : /fly on/off");
						return false;
					} else {
						if($args[0] == "on")
						{
							$sender->sendMessage("§7| §l§6UtriXFlight §7» §aEnabled Flight");
							$sender->setAllowFlight(true);
						}
						if($args[0] == "off")
						{
							$sender->sendMessage("§7| §l§6UtriXFlight §7» §aDisabled Flight");
							$sender->setAllowFlight(false);
						}
					}
				}  else {
					return true;
				}
			}
		}

		if($cmd->getName() == "umute")
		{
			$utrix = UtriX::getPluginInstance();
			if($sender->hasPermission("utrix.staff"))
			{
				if(!isset($args[0]) || !isset($args[1]) || !isset($args[2]))
				{
					$sender->sendMessage("§7| §6UtriX §7» §cError, Usage : /umute [playername] [period] [reason]");
					return false;
				} else {
					$player = $args[0];
					$target = $utrix->getServer()->getPlayerExact($player);
					if($player == null){
						$sender->sendMessage("§7| §6UtriX §7» §cSorry, This Player Seems To Be Offline");
						return false;
					} else {
						$period = $args[1];
						$reason = $args[2];
						if(strpos($period, "s") !== false){
							$period = strtotime("+" . strval(intval($args[1])) . " seconds");
							$unmuted_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnMutedIn", strval($unmuted_on));
							$target_data->set("MuteStatus", "True");
							$target_data->set("MutedBy", $sender->getName());
							$target_data->set("MuteReason", $reason);
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();

							$target_data->save();
							$appealcode = $target_data->get("AppealCode");
							$sender_name = $sender->getName();
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$target->sendMessage("§7| §6UtriX §7» §cYou Have Muted , The Reason §a$reason, §cMuted By : §3$sender_name, §cUnMuted On : §4$unmuted_on, §cThink This is an Error ?, Please Appeal At Our Discord : /discord, Your Appeal Code : §6$appealcode");
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aThe Player §3$player , §aHas Been Muted By §c$sender_name, §aReason : §e$reason, §6Until : §44$unmuted_on");

								} else {

								}
							}
							
						}
						if(strpos($period, "mi") !== false){
							$period = strtotime("+" . strval(intval($args[1])) . " minutes");
							$unmuted_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnMutedIn", strval($unmuted_on));
							$target_data->set("MuteStatus", "True");
							$target_data->set("MutedBy", $sender->getName());
							$target_data->set("MuteReason", $reason);
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();

							$target_data->save();
							$appealcode = $target_data->get("AppealCode");
							$sender_name = $sender->getName();
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$target->sendMessage("§7| §6UtriX §7» §cYou Have Muted , The Reason §a$reason, §cMuted By : §3$sender_name, §cUnMuted On : §4$unmuted_on, §cThink This is an Error ?, Please Appeal At Our Discord : /discord, Your Appeal Code : §6$appealcode");
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aThe Player §3$player , §aHas Been Muted By §c$sender_name, §aReason : §e$reason, §6Until : §44$unmuted_on");

								} else {

								}
							}
						}
						if(strpos($period, "h") !== false){
							$period = strtotime("+" . strval(intval($args[1])) . " hours");
							$unmuted_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnMutedIn", strval($unmuted_on));
							$target_data->set("MuteStatus", "True");
							$target_data->set("MutedBy", $sender->getName());
							$target_data->set("MuteReason", $reason);
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();

							$target_data->save();
							$appealcode = $target_data->get("AppealCode");
							$sender_name = $sender->getName();
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$target->sendMessage("§7| §6UtriX §7» §cYou Have Muted , The Reason §a$reason, §cMuted By : §3$sender_name, §cUnMuted On : §4$unmuted_on, §cThink This is an Error ?, Please Appeal At Our Discord : /discord, Your Appeal Code : §6$appealcode");
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aThe Player §3$player , §aHas Been Muted By §c$sender_name, §aReason : §e$reason, §6Until : §44$unmuted_on");

								} else {

								}
							}
						}
						if(strpos($period, "d") !== false){
							$period = strtotime("+" . strval(intval($args[1])) . " days");
							$unmuted_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnMutedIn", strval($unmuted_on));
							$target_data->set("MuteStatus", "True");
							$target_data->set("MutedBy", $sender->getName());
							$target_data->set("MuteReason", $reason);
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();

							$target_data->save();
							$appealcode = $target_data->get("AppealCode");
							$sender_name = $sender->getName();
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$target->sendMessage("§7| §6UtriX §7» §cYou Have Muted , The Reason §a$reason, §cMuted By : §3$sender_name, §cUnMuted On : §4$unmuted_on, §cThink This is an Error ?, Please Appeal At Our Discord : /discord, Your Appeal Code : §6$appealcode");
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aThe Player §3$player , §aHas Been Muted By §c$sender_name, §aReason : §e$reason, §6Until : §44$unmuted_on");

								} else {

								}
							}
						}
						if(strpos($period, "w") !== false){
							$period = strtotime("+" . strval(intval($args[1])) . " weeks");
							$unmuted_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnMutedIn", strval($unmuted_on));
							$target_data->set("MuteStatus", "True");
							$target_data->set("MutedBy", $sender->getName());
							$target_data->set("MuteReason", $reason);
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();

							$target_data->save();
							$appealcode = $target_data->get("AppealCode");
							$sender_name = $sender->getName();
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$target->sendMessage("§7| §6UtriX §7» §cYou Have Muted , The Reason §a$reason, §cMuted By : §3$sender_name, §cUnMuted On : §4$unmuted_on, §cThink This is an Error ?, Please Appeal At Our Discord : /discord, Your Appeal Code : §6$appealcode");
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aThe Player §3$player , §aHas Been Muted By §c$sender_name, §aReason : §e$reason, §6Until : §44$unmuted_on");

								} else {

								}
							}
						}
						if(strpos($period, "m") !== false){
							$period = strtotime("+" . strval(intval($args[1])) . " months");
							$unmuted_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnMutedIn", strval($unmuted_on));
							$target_data->set("MuteStatus", "True");
							$target_data->set("MutedBy", $sender->getName());
							$target_data->set("MuteReason", $reason);
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();

							$target_data->save();
							$appealcode = $target_data->get("AppealCode");
							$sender_name = $sender->getName();
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$target->sendMessage("§7| §6UtriX §7» §cYou Have Muted , The Reason §a$reason, §cMuted By : §3$sender_name, §cUnMuted On : §4$unmuted_on, §cThink This is an Error ?, Please Appeal At Our Discord : /discord, Your Appeal Code : §6$appealcode");
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aThe Player §3$player , §aHas Been Muted By §c$sender_name, §aReason : §e$reason, §6Until : §44$unmuted_on");

								} else {

								}
							}
						}
						if(strpos($period, "y") !== false){
							$period = strtotime("+" . strval(intval($args[1])) . " years");
							$unmuted_on = date("d/m/y:h:i:s", $period);
							$target_data = $utrix->getData(null, $target->getName());
							$target_data->set("UnMutedIn", strval($unmuted_on));
							$target_data->set("MuteStatus", "True");
							$target_data->set("MutedBy", $sender->getName());
							$target_data->set("MuteReason", $reason);
							$target_data->set("AppealCode", $utrix->GenerateAppealCode());
							$utrix->appealcodes->set($target_data->get("AppealCode"), $target->getName());
							$utrix->appealcodes->save();
							$target_data->save();
							$appealcode = $target_data->get("AppealCode");
							$sender_name = $sender->getName();
							
							$utrix->staffdata->set($sender_name, $utrix->staffdata->get($sender_name) + 1);
							$utrix->staffdata->save();
							$target->sendMessage("§7| §6UtriX §7» §cYou Have Muted , The Reason §a$reason, §cMuted By : §3$sender_name, §cUnMuted On : §4$unmuted_on, §cThink This is an Error ?, Please Appeal At Our Discord : /discord, Your Appeal Code : §6$appealcode");
							foreach($utrix->getServer()->getOnlinePlayers() as $p){
								if($p->hasPermission("utrix.staff")){
									$p->sendMessage("§7| §6UtriX §7» §aThe Player §3$player , §aHas Been Muted By §c$sender_name, §aReason : §e$reason, §6Until : §44$unmuted_on");

								} else {

								}
							}
						}
					}
				}
			}
		}

		if($cmd->getName() == "unspectate"){
			if($sender->hasPermission("utrix.staff")){
				$sender->setGameMode(GameMode::SURVIVAL());
				$sender->sendMessage("§7| §6UtriX §7» §aSuccessfuly Disabled Spectator Mode");
			}
		}

		if($cmd->getName() == "unmute")
		{
			if($sender->hasPermission("utrix.staff"))
			{
				if(!isset($args[0]))
				{
					$sender->sendMessage("§7| §6UtriX §7» §cError, Usage : /unmute [playername]");
					return false;
				} else {
					$utrix = UtriX::getPluginInstance();
					$player = $utrix->getServer()->getPlayerExact($args[0]);
					$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
						"Name" => $player->getName(),
						"Rank" => "Member",
						"ChatLevel" => 0,
						"Banned" => "FALSE",
						"Reason_Of_Ban" => "NONE",
						"Discord Linked Account" => "NONE",
						"BAN_APPEAL_CODE" => "NONE",
						"UtriXFriends" => "NONE",
						"UtriXMutes" => "FALSE",
					));

					$sender->sendMessage("§7| §6UtriX §7» §aSuccess UnMuted Player :- " . $player->getName() . "");
					$utrix->config->set("MuteStatus", "FALSE");
					$utrix->config->save();
				}
			}
		}


		if($cmd->getName() == "uworldtp")
		{
			$utrix = UtriX::getPluginInstance();
			if(!isset($args[0]))
			{
				$sender->sendMessage("§7| §l§6UtriXGames §7» §cError, Usage: /uworldtp [servername]");
			} else {
				if($sender->getWorld()->getFolderName() == "world")
				{
					if($args[0] == "knockbackpvp")
					{
						$name = TextFormat::clean($sender->getName());
						$sender->getInventory()->clearAll();
						$sender->getInventory()->clearAll();
						$utrix->remove($sender);
						$kbpvp =  $utrix->getServer()->getPluginManager()->getPlugin("UtriXKnockBackPvP");
						$kbpvp->KnockKit($sender);
						$sender->teleport($utrix->getServer()->getWorldManager()->getWorldByName("kbpvp")->getSafeSpawn());
						$sender->sendMessage("§7| §l§6UtriXGames §7» §aYou Have Joined KnockBackPvP");
						
						if(isset($utrix->heart[$name]) || isset($utrix->flame[$name]) || isset($utrix->redstone[$name]))
						{
							unset($utrix->heart[$name]);
							unset($utrix->flame[$name]);
							unset($utrix->redstone[$name]);
						}
						$utrix->setCape($sender, "");
					}
	
					if($args[0] == "fist")
					{
						$name = TextFormat::clean($sender->getName());
						$sender->getInventory()->clearAll();
						$utrix->remove($sender, "UtriX Network");
						$sender->teleport($utrix->getServer()->getWorldManager()->getWorldByName("fist")->getSafeSpawn());
						$sender->sendMessage("§7| §l§6UtriXGames §7» §aYou Have Joined Fist");
						$sender->getInventory()->clearAll();
						if(isset($utrix->heart[$name]) || isset($utrix->flame[$name]) || isset($utrix->redstone[$name]))
						{
							unset($utrix->heart[$name]);
							unset($utrix->flame[$name]);
							unset($utrix->redstone[$name]);
						}
					}
	
	
					if($args[0] == "skymines")
					{
						$name = TextFormat::clean($sender->getName());
						$sender->getInventory()->clearAll();
						$utrix->remove($sender, "UtriX Network");
						$sender->teleport($utrix->getServer()->getWorldManager()->getWorldByName("skymines")->getSafeSpawn());
						$sender->sendMessage("§7| §l§6UtriXGames §7» §aYou Have Joined SkyMines");
						$sender->getInventory()->clearAll();
						$skymines = $utrix->getServer()->getPluginManager()->getPlugin("UtriXSkyMines");
						$skymines->DefaultKit($sender);
						if(isset($utrix->heart[$name]) || isset($utrix->flame[$name]) || isset($utrix->redstone[$name]))
						{
							unset($utrix->heart[$name]);
							unset($utrix->flame[$name]);
							unset($utrix->redstone[$name]);
						}
						$utrix->setCape($sender, "");
					}
	
					if($args[0] == "kitpvp")
					{
						$name = TextFormat::clean($sender->getName());
						$sender->getInventory()->clearAll();
						$utrix->remove($sender);
						$kitpvp = $utrix->getServer()->getPluginManager()->getPlugin("UtriXKitPvP");
						$sender->getInventory()->clearAll();
						$kitpvp->KitPvPScoreboard($sender);
						$kitpvp->KitManager($sender);
						$sender->teleport($utrix->getServer()->getWorldManager()->getWorldByName("kitpvp")->getSafeSpawn());
						$sender->sendMessage("§7| §l§6UtriXGames §7» §aYou Have Joined KitPvP");
						if(isset($utrix->heart[$name]) || isset($utrix->flame[$name]) || isset($utrix->redstone[$name]))
						{
							unset($utrix->heart[$name]);
							unset($utrix->flame[$name]);
							unset($utrix->redstone[$name]);
						}
						$sender->sendTitle("§l§7<---§6UtriX§7--->", "§l§cfdeet men ygld :D");
					}
				}


			}
		}

		if($cmd->getName() == "link")
		{
			$utrix = UtriX::getPluginInstance();
			$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($sender->getName()) . ".yml", Config::YAML, array(
				"Name" => $sender->getName(),
				"Rank" => "Member",
				"ChatLevel" => 0,
				"Banned" => "FALSE",
				"Reason_Of_Ban" => "NONE",
				"BAN_APPEAL_CODE" => "NONE",
				"UtriXFriends" => "NONE",
				"UtriXMutes" => "FALSE",
				"DiscordAccountID" => "NONE",
				"LinkedStatus" => "unlinked",
			));
			
			if(!isset($args[0]))
			{
				$sender->sendMessage("§7| §6UtriX §7» §cError, Enter a Vaild Link Code !");
				return false;
			}
			if(!isset($utrix->code[strtolower($sender->getName())])){
				$sender->sendMessage("§7| §6UtriX §7» §cSorry , Hmm This Code Looks UnVaild or Expired , Retype the Link Command");
				return false;
			} else {
				if(in_array($args[0], $utrix->code[strtolower($sender->getName())]))
				{
					$discordbot = $utrix->getServer()->getPluginManager()->getPlugin("DiscordBot");
					$api = $discordbot->getApi();
					$sender->sendMessage("§7| §6UtriX §7» §aSuccessfully Linked Your Account !");
					$utrix->config->set("LinkedStatus", "linked");
					$utrix->config->set("DiscordAccountID", json_encode($utrix->authorid[strtolower($sender->getName())]));
					//$api->addRole(json_encode($utrix->authorid[strtolower($sender->getName())]), "1000449378663792690");
					$utrix->discord->set(json_encode($utrix->authorid[strtolower($sender->getName())]), $sender->getName());
					$utrix->discord->save();
					unset($utrix->code[strtolower($sender->getName())]);
					unset($utrix->authorid[strtolower($sender->getName())]);
					$utrix->config->save();
					
				} else {
					$sender->sendMessage("§7| §6UtriX §7» §cError, Enter a Vaild Link Code !");
				}
			}
		}
		if($cmd->getName() == "setjoinmsg")
		{
			if($sender->hasPermission("utrix.tier3") || $sender->hasPermission("utrix.tier2"))
			{
				$utrix = UtriX::getPluginInstance();
				$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($sender->getName()) . ".yml", Config::YAML, array(
					"Name" => $sender->getName(),
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
				));

				if(!isset($args[0]))
				{
					$sender->sendMessage("§7| §l§6UtriX §7» §cError , No Paraments Provided");
					return false;
				} else {

					$utrix->config->set("JoinMSG", $args[0]);
					$utrix->config->save();
					$sender->sendMessage("§7| §l§6UtriX §7» §aSuccessfuly Set a Join Message");

				}
			}
		}

		if($cmd->getName() == "setleavemsg")
		{
			if($sender->hasPermission("utrix.tier3") || $sender->hasPermission("utrix.tier2"))
			{
				$utrix = UtriX::getPluginInstance();
				$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($sender->getName()) . ".yml", Config::YAML, array(
					"Name" => $sender->getName(),
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
				));

				if(!isset($args[0]))
				{
					$sender->sendMessage("§7| §l§6UtriX §7» §cError , No Paraments Provided");
					return false;
				} else {
					$utrix->config->set("LeaveMSG", $args[0]);
					$utrix->config->save();
					$sender->sendMessage("§7| §l§6UtriX §7» §aSuccessfuly Set a Left Message");

				}
			}
		}

		if($cmd->getName() == "setucoins")
		{

			if(!isset($args[0]))
			{
				$sender->sendMessage("§7| §l§6UtriXCoins §7» §cError , No Paraments Provided");
				return false;
			}
			
			if(!isset($args[1]))
			{
				$sender->sendMessage("§7| §l§6UtriXCoins §7» §cError , No Paraments Provided");
				return false;
			}	else {
				if($sender->hasPermission("utrix.ucoinsset") || $sender->hasPermission("utrix.founders") || $sender->hasPermission("utrix.highstaff"))
				{
					$utrix = UtriX::getPluginInstance();
					$player = $utrix->getServer()->getPlayerExact($args[0]);
					$ucoins = $args[1];
					$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
						"Name" => $player->getName(),
						"Rank" => "Member",
						"ChatLevel" => 0,
						"Banned" => "FALSE",
						"Reason_Of_Ban" => "NONE",
						"BAN_APPEAL_CODE" => "NONE",
						"UtriXFriends" => "NONE",
						"UtriXMutes" => "FALSE",
						"UCoins" => 0,
						"UEmeralds" => 0,
						"LinkedStatus" => "unlinked",
					));

					$utrix->config->set("UCoins", $utrix->config->get("UCoins") + $ucoins);
					$utrix->config->save();
					$player->sendMessage("§7| §l§6UtriXCoins §7» §aDone , Added §b" . $ucoins . " §aTo Your UCoins Balance !");
					if($player->getWorld()->getFolderName() == "world")
					{
						$utrix->UtriXScoreBoard($player);
					} else {
						return true;
					}

				}
			}
			if($cmd->getName() == "setuemeralds")
			{
	
				if(!isset($args[0]))
				{
					$sender->sendMessage("§7| §l§6UtriXEmeralds §7» §cError , No Paraments Provided");
					return false;
				}else {
					if($sender->hasPermission("utrix.uemerladsset") || $sender->hasPermission("utrix.founders") || $sender->hasPermission("utrix.highstaff"))
					{
						$utrix = UtriX::getPluginInstance();
						$player = $utrix->getServer()->getPlayerExact($args[0]);
						$uemerald = $args[1];
						$utrix->config = new Config($utrix->getDataFolder() . "playerdata/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
							"Name" => $player->getName(),
							"Rank" => "Member",
							"ChatLevel" => 0,
							"Banned" => "FALSE",
							"Reason_Of_Ban" => "NONE",
							"BAN_APPEAL_CODE" => "NONE",
							"UtriXFriends" => "NONE",
							"UtriXMutes" => "FALSE",
							"UCoins" => 0,
							"UEmeralds" => 0,
							"LinkedStatus" => "unlinked",
						));
	
						$utrix->config->set("UEmeralds", $utrix->config->get("UEmeralds") + $uemerald);
						$utrix->config->save();
						$player->sendMessage("§7| §l§6UtriXCoins §7» §aDone , Added §b" . $uemerald . " §aTo Your UEmeralds Balance !");
						if($player->getWorld()->getFolderName() == "world")
						{
							$utrix->UtriXScoreBoard($player);
						} else {
							return true;
						}
	
					}
				}
	
	
	
	
			}




		}
		if($cmd->getName() == "uemerald")
		{
			$utrix = UtriX::getPluginInstance();
			$utrix->ConvertIntoUEmerald($sender);
		}
		
		if($cmd->getName() == "getusc"){
			$utrix = UtriX::getPluginInstance();

			if($sender instanceof Player){
				$usc = $utrix->getUtriXCode($sender);
				$sender->sendMessage("§7| §6UtriX §7» §aDone, Got Your Data, Your USC Is $usc, Don't Give it To Any One !");
			} else {
				return false;
			}
		}

		return true;

		
	}

	
}