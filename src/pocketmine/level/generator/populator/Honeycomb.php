<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\block\Liquid;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\Honeycomb as ObjectHoney;
use pocketmine\utils\Random;

class Honeycomb extends Populator{
	/** @var ChunkManager */
	private $level;
	private $randomAmount;
	private $baseAmount;

	private $id;
	private $data;

	public $disable = [
		Block::AIR => true,
		Block::EMERALD_BLOCK => true,
		Block::YELLOW_GLAZED_TERRACOTTA => true
	];

	public function setRandomAmount($amount){
		$this->randomAmount = $amount;
	}

	public function setBaseAmount($amount){
		$this->baseAmount = $amount;
	}

	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		$this->level = $level;
		$amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y === -1){
				continue;
			}
			//ObjectTree::growTree($this->level, $x, $y, $z, $random, $this->type);
			//$log, $leaves, $leafType, $type, $treeHeight = 8
			$comb = new ObjectHoney();
			if($comb->canPlaceObject($level, $x, $y, $z)){
				$comb->placeObject($level, $x, $y, $z);
			}
		}
	}

	private function getHighestWorkableBlock($x, $z){
		for($y = 127; $y > 0; --$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			$b2 = $this->level->getBlockIdAt($x, $y+1, $z);
			$bl = Block::get($b2);
			if($b === 0 && $bl->isSolid() && !isset($this->disable[$b2])){
				break;
			}elseif($y < 50){//under waterHeight
				return -1;
			}
		}

		return $y;
	}
}
