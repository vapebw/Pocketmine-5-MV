<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\command;

use pocketmine\utils\SingletonTrait;

class BedrockCommandRegistry {
    use SingletonTrait;

    /** @var BedrockOverload[][] */
    private array $overloads = [];

    /**
     * @param BedrockOverload[] $overloads
     */
    public function register(string $label, array $overloads): void {
        $this->overloads[strtolower($label)] = $overloads;
    }

    /**
     * @return BedrockOverload[]|null
     */
    public function get(string $label): ?array {
        return $this->overloads[strtolower($label)] ?? null;
    }

    /**
     * @return BedrockOverload[][]
     */
    public function getAll(): array {
        return $this->overloads;
    }

    public function has(string $label): bool {
        return isset($this->overloads[strtolower($label)]);
    }
}
