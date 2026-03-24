<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\command;

use pocketmine\network\mcpe\protocol\types\command\CommandSoftEnum;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class SoftEnumManager {
    use SingletonTrait;

    /** @var string[][] */
    private array $enums = [];

    public function registerEnum(string $name, array $values = []): void {
        $this->enums[$name] = $values;
    }

    public function getEnum(string $name): ?CommandSoftEnum {
        if (!isset($this->enums[$name])) {
            return null;
        }
        return new CommandSoftEnum($name, array_values($this->enums[$name]));
    }

    public function getValues(string $name): array {
        return $this->enums[$name] ?? [];
    }

    public function setValues(string $name, array $values): void {
        $this->enums[$name] = $values;
        $this->broadcastUpdate($name, UpdateSoftEnumPacket::TYPE_SET);
    }

    public function addValue(string $name, string $value): void {
        if (!isset($this->enums[$name])) {
            return;
        }
        if (in_array($value, $this->enums[$name], true)) {
            return;
        }
        $this->enums[$name][] = $value;
        $this->broadcastUpdate($name, UpdateSoftEnumPacket::TYPE_ADD, [$value]);
    }

    public function removeValue(string $name, string $value): void {
        if (!isset($this->enums[$name])) {
            return;
        }
        $key = array_search($value, $this->enums[$name], true);
        if ($key === false) {
            return;
        }
        unset($this->enums[$name][$key]);
        $this->enums[$name] = array_values($this->enums[$name]);
        $this->broadcastUpdate($name, UpdateSoftEnumPacket::TYPE_REMOVE, [$value]);
    }

    public function getAllEnums(): array {
        $result = [];
        foreach ($this->enums as $name => $values) {
            $result[$name] = new CommandSoftEnum($name, array_values($values));
        }
        return $result;
    }

    private function broadcastUpdate(string $name, int $type, ?array $values = null): void {
        $packet = UpdateSoftEnumPacket::create(
            $name,
            $values ?? array_values($this->enums[$name] ?? []),
            $type
        );
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $player->getNetworkSession()->sendDataPacket($packet);
        }
    }
}
