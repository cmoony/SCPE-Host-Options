<?php

namespace ColorText;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player; //<- forgot to include Player library
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;

class ColorText extends PluginBase implements Listener{

   public function __construct(){
	     $this->config = "";
	     $this->coloredChatPlayers=[];
	     $this->discolorPlayers=[];
   }

   public function onEnable(){
	     @mkdir($this->getDataFolder()); //<-Create plugin directory
	     $this->saveDefaultConfig(); //<-Save default config
	     $this->config = $this->getConfig()->getAll(); //<- Get config
	     if($this->config["enabled"] != true){
		       $this->getLogger()->info("Plugin disabled");
		       $this->getPluginLoader()->disablePlugin($this);
		    }else{
         $this->getServer()->getPluginManager()->registerEvents($this, $this);
      }
   }
   /**
    * @priority MONITOR
    * @ignoreCancelled true
    */
   public function onJoin(PlayerJoinEvent $ev){
	     if(isset($this->discolorPlayers[$ev->getPlayer()->getName()])){
		       $ev->getPlayer()->setRemoveFormat(false);
		    }
   }
   /**
     * @param PlayerChatEvent $event
     *
     * @priority LOWEST
     * @ignoreCancelled false
     */
   public function onChat(PlayerChatEvent $event){
      $player = $event->getPlayer();
      $message = $event->getMessage();
      foreach($this->getServer()->getOnlinePlayers() as $players){
         if(isset($this->coloredChatPlayers[$players->getName()])){
            $players->sendMessage($this->config["name"]."<".$player->getName().">d ".$this->config["message"].$message);
         }else{
            $players->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "" . TextFormat::DARK_GRAY . "] " . TextFormat::BLUE . $player->getName() . TextFormat::GREEN . " > " . TextFormat::RED . $message);
         }
      }
      $event->setCancelled(true);
   }
   
   public function onCommand(CommandSender $sender, Command $command, $label, array $args){
      $cmd = strtolower($command); //<-Changed Code
      switch($cmd){
	        case "color":
	          if($sender instanceof Player){
		              if(isset($this->coloredChatPlayers[$sender->getName()])){ //<-you can't use in_array with this type of array
                 unset($this->coloredChatPlayers[$sender->getName()]);
                 $sender->sendMessage(TextFormat::RED . "You have disabled color chat!");
                 break; //<-break is required to stop command execution
              }else{
                 $this->coloredChatPlayers[$sender->getName()] = "";
                 $sender->sendMessage(TextFormat::GREEN . "");
                 break;
              }
           }
           return true;
         case "discolor":
           if($sender instanceof Player){
              if(isset($this->discolorPlayers[$sender->getName()])){ //<-you can't use in_array with this type of array
                 unset($this->discolorPlayers[$sender->getName()]);
                 $sender->sendMessage("");
                 $sender->sendMessage("");
                 $sender->setRemoveFormat(true);
                 break;
              }else{
                 $this->discolorPlayers[$sender->getName()] = "";
                 $sender->sendMessage("");
				 $sender->sendMessage("");
                 $sender->setRemoveFormat(false);
              }
           }
           return true;
      }
   }
}
