<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\command;

class BedrockOverload {

    /**
     * @param BedrockParameter[] $parameters
     */
    public function __construct(
        private array $parameters,
        private bool $chaining = false
    ) {}

    /**
     * @return BedrockParameter[]
     */
    public function getParameters(): array {
        return $this->parameters;
    }

    public function isChaining(): bool {
        return $this->chaining;
    }
}
