# V. Public - Optimized Multiversion Fork

Custom PocketMine-MP 5 fork built for production Bedrock servers. Fully optimized with native multiversion protocol support, Bedrock-native command autocompletion, and per-version data translation layers.

## Supported Versions

| Range | Protocols |
|---|---|
| **1.20.0 - 1.20.80** | 589, 594, 618, 622, 630, 649, 662, 671 |
| **1.21.0 - 1.21.130** | 685, 686, 712, 729, 748, 766, 776, 786, 800, 818, 819, 827, 844, 859, 860, 898 |
| **1.26.0 - 1.26.10** | 924, 944 |

> **26 protocol versions** supported simultaneously. Players on any version from 1.20.0 to 1.26.10 can connect to the same server.

## Optimizations

### Multiversion Protocol Engine
- Per-protocol **block state dictionaries** - each version loads its own `canonical_block_states` and `block_state_meta_map`
- Per-protocol **item type dictionaries** - versioned `required_item_list` ensures correct item IDs per client
- Per-protocol **item tag mappings** - versioned `item_tags` for recipe and tag compatibility
- Per-protocol **item schema downgrades** - `ItemIdMetaDowngrader` handles cross-version item meta translation
- Zero data duplication - newer versions reuse older data files when block/item schemas haven't changed

### Native Bedrock Command Autocompletion
The core features a standalone, Bedrock-native command autocompletion system that allows any plugin to define complex overloads and parameter types without external dependencies.

#### Key Core Classes
- `pocketmine\network\mcpe\command\BedrockCommandRegistry`
- `pocketmine\network\mcpe\command\BedrockOverload`
- `pocketmine\network\mcpe\command\BedrockParameter`
- `pocketmine\network\mcpe\command\SoftEnumManager`

#### How to use in any plugin
Any developer can add autocompletion to their commands by registering overloads in the registry during their plugin's `onEnable()`:

```php
use pocketmine\network\mcpe\command\BedrockCommandRegistry;
use pocketmine\network\mcpe\command\BedrockOverload;
use pocketmine\network\mcpe\command\BedrockParameter;
use pocketmine\network\mcpe\protocol\types\command\CommandParameterTypes;

$overloads = [
    new BedrockOverload([
        new BedrockParameter("action", CommandParameterTypes::ID, false, ["stats", "info"]),
        new BedrockParameter("target", CommandParameterTypes::TARGET, true)
    ])
];
BedrockCommandRegistry::getInstance()->register("mycommand", $overloads);
```

#### Dynamic Autocompletion (Soft Enums)
Use the `SoftEnumManager` to register or update dynamic lists (like custom mini-game arenas or teams) that update in real-time for all connected clients:
```php
use pocketmine\network\mcpe\command\SoftEnumManager;

SoftEnumManager::getInstance()->registerEnum("arenas", ["peak", "valley", "reef"]);
// Later, to update:
SoftEnumManager::getInstance()->addValue("arenas", "volcano");
```

### Network Layer
- Protocol-aware packet encoding/decoding — `encode($serializer, $protocolId)` and `decode($stream, $protocolId)`
- Per-session `TypeConverter` with protocol-specific block/item translators
- Compressed batch processing with async compression support
- ACK receipt tracking for reliable packet delivery
- Noisy packet filtering and rate limiting

### Entity System
- `Ageable` interface with `isBaby()` / `setBaby()` for generic baby entity control
- Per-entity `EntitySizeInfo` with baby scale support
- Network metadata sync via `EntityMetadataFlags::BABY`
- `SpawnEgg` baby spawning — using a spawn egg on a matching mob creates the baby variant

### Block & Item System
- `ProtocolSingletonTrait` for per-protocol singleton instances (block translators, item tag maps)
- Dynamic block/item ID allocation via `BlockTypeIds::newId()` and `ItemTypeIds::newId()`
- Full block state serialization/deserialization pipeline with fallback to `info_update`

## Drop 1 2026 (1.26.10)

Latest update includes:
- **Protocol 944** — Registered with full multiversion backward compatibility
- **Golden Dandelion** — New flower block with standard placement rules
- **Spawn Egg Baby Behavior** — Using a spawn egg on a matching ageable mob spawns the baby variant

## Architecture

```
vendor/nethergamesmc/bedrock-protocol/
├── src/ProtocolInfo.php              # Protocol constants & accepted versions
├── src/packets/                       # All Bedrock protocol packets
└── src/types/entity/                  # Entity metadata flags & properties

src/network/mcpe/convert/
├── BlockTranslator.php                # Per-protocol block state translation
├── ItemTranslator.php                 # Per-protocol item ID translation
├── ItemTypeDictionaryFromDataHelper.php  # Per-protocol item type loading
└── TypeConverter.php                  # Session-level type conversion

src/entity/
├── Ageable.php                        # Baby entity interface
├── Entity.php                         # Base entity with network sync
└── EntitySizeInfo.php                 # Bounding box with baby scale

src/item/
├── SpawnEgg.php                       # Baby spawn on entity interaction
└── VanillaItems.php                   # Item registry
```

## Credits
Forked by: **sxvape - funao - dvyskz**
discord.gg/vespermc
