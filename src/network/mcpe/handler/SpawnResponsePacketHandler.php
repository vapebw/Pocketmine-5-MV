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

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\PlayerSkinPacket;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\ServerboundLoadingScreenPacket;
use pocketmine\network\mcpe\protocol\SetLocalPlayerAsInitializedPacket;
use pocketmine\network\mcpe\protocol\UpdateClientOptionsPacket;

final class SpawnResponsePacketHandler extends PacketHandler{
	/**
	 * @phpstan-param \Closure() : void $responseCallback
	 */
	public function __construct(
		private NetworkSession $session,
		private \Closure $responseCallback
	){}

	public function handleSetLocalPlayerAsInitialized(SetLocalPlayerAsInitializedPacket $packet) : bool{
		($this->responseCallback)();
		return true;
	}

	public function handlePlayerSkin(PlayerSkinPacket $packet) : bool{
		//TODO: REMOVE THIS
		//As of 1.19.60, we receive this packet during pre-spawn for no obvious reason. The skin is still sent in the
		//login packet, so we can ignore this one. If unhandled, this packet makes a huge debug spam in the log.
		return true;
	}

	public function handlePlayerAuthInput(PlayerAuthInputPacket $packet) : bool{
		return true;
	}

	public function handleNetworkStackLatency(NetworkStackLatencyPacket $packet) : bool{
		return $this->session->handlePreSpawnNetworkStackLatency($packet);
	}

	public function handleServerboundLoadingScreen(ServerboundLoadingScreenPacket $packet) : bool{
		return $this->session->handlePreSpawnLoadingScreen();
	}

	public function handleUpdateClientOptions(UpdateClientOptionsPacket $packet) : bool{
		return $this->session->handlePreSpawnClientOptions();
	}

	public function handleRequestChunkRadius(RequestChunkRadiusPacket $packet) : bool{
		return true;
	}
}
