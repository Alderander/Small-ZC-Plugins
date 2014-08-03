<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pemapmodder\worldeditart\Main;

abstract class FilePlayerDataProvider extends PlayerDataProvider{
	private $path;
	public function __construct(Main $main, $path){
		parent::__construct($main);
		$this->path = $path;
	}
	public function getPath($name){
		$file = str_replace("<name>", strtolower($name), $this->path);
		@mkdir(dirname($file), 0777, true);
		return $file;
	}
	public function isAvailable(){
		return true;
	}
	public function close(){

	}
	public function offsetGet($name){
		if(is_file($this->getPath($name))){
			$data = $this->parseFile($this->getPath($name));
			return new PlayerData($this->getMain(), $name, $data["wand-id"], $data["wand-damage"]);
		}
		return new PlayerData($this->getMain(), $name);
	}
	public function offsetSet($name, $data){
		if(!($data instanceof PlayerData)){
			throw new \InvalidArgumentException("Player data passed to FilePlayerDataProvider must be instance of PlayerData, ".
					(is_object($data) ? get_class($data):gettype($data))." given");
		}
		$this->emitFile($this->getPath($name), [
			"wand-id" => $data->getWandID(),
			"wand-damage" => $data->getWandDamage()
		]);
	}
	public function offsetUnset($name){
		@unlink($this->getPath($name));
	}
	protected abstract function parseFile($file);
	protected abstract function emitFile($file, $data);
}