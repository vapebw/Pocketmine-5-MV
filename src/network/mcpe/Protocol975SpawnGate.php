<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;

final class Protocol975SpawnGate{
	public const STATE_INITIALIZING = 0;
	public const STATE_SENDING_CHUNKS = 1;
	public const STATE_BARRIER_REQUESTED = 2;
	public const STATE_BARRIER_ACKED = 3;
	public const STATE_PLAYER_SPAWN_SENT = 4;
	public const STATE_CLIENT_INITIALIZED = 5;

	private int $state = self::STATE_INITIALIZING;
	private ?int $pendingLatencyTimestamp = null;

	public function getState() : int{
		return $this->state;
	}

	public function startSendingChunks() : void{
		$this->state = self::STATE_SENDING_CHUNKS;
	}

	public function beginBarrierLatencyHandshake(int $timestamp) : NetworkStackLatencyPacket{
		$this->state = self::STATE_BARRIER_REQUESTED;
		$this->pendingLatencyTimestamp = $timestamp;
		return NetworkStackLatencyPacket::request($timestamp);
	}

	public function isAwaitingBarrierResponse() : bool{
		return $this->state === self::STATE_BARRIER_REQUESTED;
	}

	public function acceptBarrierResponse(NetworkStackLatencyPacket $packet) : bool{
		if($this->state !== self::STATE_BARRIER_REQUESTED || $this->pendingLatencyTimestamp === null || $packet->needResponse || $packet->timestamp !== $this->pendingLatencyTimestamp){
			return false;
		}

		$this->pendingLatencyTimestamp = null;
		$this->state = self::STATE_BARRIER_ACKED;
		return true;
	}

	public function markPlayerSpawnSent() : void{
		$this->state = self::STATE_PLAYER_SPAWN_SENT;
	}

	public function markClientInitialized() : void{
		$this->state = self::STATE_CLIENT_INITIALIZED;
	}

	public function isClientInitialized() : bool{
		return $this->state === self::STATE_CLIENT_INITIALIZED;
	}

	public function shouldPausePostSpawnChunkStream() : bool{
		return $this->state === self::STATE_PLAYER_SPAWN_SENT;
	}

	public function isBlockingOptionalPackets() : bool{
		return $this->state < self::STATE_CLIENT_INITIALIZED;
	}

	public function canSendPlayerSpawn() : bool{
		return $this->state === self::STATE_BARRIER_ACKED;
	}

	public function reset() : void{
		$this->state = self::STATE_INITIALIZING;
		$this->pendingLatencyTimestamp = null;
	}
}
