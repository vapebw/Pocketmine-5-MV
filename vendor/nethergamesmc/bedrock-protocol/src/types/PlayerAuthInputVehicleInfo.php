<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;

final class PlayerAuthInputVehicleInfo{

	public function __construct(
		private ?float $vehicleRotationX,
		private ?float $vehicleRotationZ,
		private int $predictedVehicleActorUniqueId
	){}

	public function getVehicleRotationX() : ?float{ return $this->vehicleRotationX; }

	public function getVehicleRotationZ() : ?float{ return $this->vehicleRotationZ; }

	public function getPredictedVehicleActorUniqueId() : int{ return $this->predictedVehicleActorUniqueId; }

	public static function read(ByteBufferReader $in, int $protocolId) : self{
		if($protocolId >= ProtocolInfo::PROTOCOL_1_20_70){
			$vehicleRotationX = LE::readFloat($in);
			$vehicleRotationZ = LE::readFloat($in);
		}
		$predictedVehicleActorUniqueId = CommonTypes::getActorUniqueId($in);

		return new self($vehicleRotationX ?? null, $vehicleRotationZ ?? null, $predictedVehicleActorUniqueId);
	}

	public function write(ByteBufferWriter $out, int $protocolId) : void{
		if($protocolId >= ProtocolInfo::PROTOCOL_1_20_70){
			LE::writeFloat($out, $this->vehicleRotationX ?? throw new \InvalidArgumentException("vehicleRotationX must be set for 1.20.70+"));
			LE::writeFloat($out, $this->vehicleRotationZ ?? throw new \InvalidArgumentException("vehicleRotationZ must be set for 1.20.70+"));
		}
		CommonTypes::putActorUniqueId($out, $this->predictedVehicleActorUniqueId);
	}
}
