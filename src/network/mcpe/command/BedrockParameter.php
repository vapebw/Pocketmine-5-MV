<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\command;

class BedrockParameter {

    public function __construct(
        private string $name,
        private int $type,
        private bool $optional = false,
        private ?array $enumValues = null,
        private ?string $softEnumName = null,
        private int $flags = 0
    ) {}

    public function getName(): string {
        return $this->name;
    }

    public function getType(): int {
        return $this->type;
    }

    public function isOptional(): bool {
        return $this->optional;
    }

    public function getFlags(): int {
        return $this->flags;
    }

    public function getEnumValues(): ?array {
        return $this->enumValues;
    }

    public function getSoftEnumName(): ?string {
        return $this->softEnumName;
    }

    public function hasHardEnum(): bool {
        return $this->enumValues !== null;
    }

    public function hasSoftEnum(): bool {
        return $this->softEnumName !== null;
    }
}
