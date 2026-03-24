<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\entity\Ageable;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\Utils;
use pocketmine\world\World;

/**
 * Base class for all spawn egg items.
 * In Bedrock 1.26.10+, using a spawn egg on a matching mob spawns the baby form.
 */
abstract class SpawnEgg extends Item{

	/**
	 * Creates the entity associated with this spawn egg
	 */
	abstract protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity;

	/**
	 * Places a new entity into the world from a spawn egg
	 */
	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, array &$returnedItems) : ItemUseResult{
		$entity = $this->createEntity($player->getWorld(), $blockReplace->getPosition()->add(0.5, 0, 0.5), Utils::getRandomFloat() * 360, 0);

		if($this->hasCustomName()){
			$entity->setNameTag($this->getCustomName());
		}
		$this->pop();
		$entity->spawnToAll();
		return ItemUseResult::SUCCESS;
	}

	/**
	 * Spawns a new baby entity when used on a matching ageable mob (Bedrock 1.26.10+)
	 */
	public function onInteractEntity(Player $player, Entity $entity, Vector3 $clickVector) : bool{
		if(!($entity instanceof Ageable)){
			return false;
		}

		$testEntity = $this->createEntity($player->getWorld(), $entity->getPosition(), 0, 0);
		if($testEntity::getNetworkTypeId() !== $entity::getNetworkTypeId()){
			$testEntity->flagForDespawn();
			return false;
		}
		$testEntity->flagForDespawn();

		$baby = $this->createEntity(
			$player->getWorld(),
			$entity->getPosition()->add(0.5, 0, 0.5),
			Utils::getRandomFloat() * 360,
			0
		);

		if($baby instanceof Ageable){
			$baby->setBaby();
		}

		if($this->hasCustomName()){
			$baby->setNameTag($this->getCustomName());
		}

		$this->pop();
		$baby->spawnToAll();
		return true;
	}
}
