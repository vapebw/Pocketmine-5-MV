# PocketMine-MP 5 — Optimized Multiversion Fork
Custom PocketMine-MP 5 fork built for production Bedrock servers. Fully optimized with native multiversion protocol support, Bedrock-native command autocompletion, and per-version data translation layers.
## Supported Versions
| Range | Protocols |
|---|---|
| **1.20.0 — 1.20.80** | 589, 594, 618, 622, 630, 649, 662, 671 |
| **1.21.0 — 1.21.130** | 685, 686, 712, 729, 748, 766, 776, 786, 800, 818, 819, 827, 844, 859, 860, 898 |
| **1.26.0 — 1.26.10** | 924, 944 |
> **26 protocol versions** supported simultaneously. Players on any version from 1.20.0 to 1.26.10 can connect to the same server.
## Optimizations
### Multiversion Protocol Engine
- Per-protocol **block state dictionaries** — each version loads its own `canonical_block_states` and `block_state_meta_map`
- Per-protocol **item type dictionaries** — versioned `required_item_list` ensures correct item IDs per client
- Per-protocol **item tag mappings** — versioned `item_tags` for recipe and tag compatibility
- Per-protocol **item schema downgrades** — `ItemIdMetaDowngrader` handles cross-version item meta translation
- Zero data duplication — newer versions reuse older data files when block/item schemas haven't changed
### Bedrock-Native Command Autocompletion
- Custom `BedrockCommandRegistry` for registering commands with full Bedrock parameter types
- `SoftEnumManager` for dynamic enum autocompletion (player names, faction names, etc.)
- Native support for `HardEnum`, `SoftEnum`, and `CommandOverload` with chaining
- Per-protocol `convertArg()` ensures argument types are correctly encoded for each client version
- Full parameter type support: `TARGET`, `INT`, `STRING`, `RAWTEXT`, `POSITION`, `FLOAT`, and more
### Network Layer
- Protocol-aware packet encoding/decoding — [encode($serializer, $protocolId)](cci:1://file:///c:/Users/Admin/Documents/Vesper/Pocketmine-5/src/network/mcpe/NetworkSession.php:637:1-649:2) and `decode($stream, $protocolId)`
- Per-session [TypeConverter](cci:1://file:///c:/Users/Admin/Documents/Vesper/Pocketmine-5/src/network/mcpe/NetworkSession.php:695:1-695:83) with protocol-specific block/item translators
- Compressed batch processing with async compression support
- ACK receipt tracking for reliable packet delivery
- Noisy packet filtering and rate limiting
### Entity System
- [Ageable](cci:2://file:///c:/Users/Admin/Documents/Vesper/Pocketmine-5/src/entity/Ageable.php:25:0-32:1) interface with [isBaby()](cci:1://file:///c:/Users/Admin/Documents/Vesper/Pocketmine-5/src/entity/Villager.php:87:1-89:2) / [setBaby()](cci:1://file:///c:/Users/Admin/Documents/Vesper/Pocketmine-5/src/entity/Ageable.php:28:1-31:51) for generic baby entity control
- Per-entity `EntitySizeInfo` with baby scale support
- Network metadata sync via `EntityMetadataFlags::BABY`
- [SpawnEgg](cci:2://file:///c:/Users/Admin/Documents/Vesper/Pocketmine-5/src/item/SpawnEgg.php:18:0-73:1) baby spawning — using a spawn egg on a matching mob creates the baby variant
### Block & Item System
- `ProtocolSingletonTrait` for per-protocol singleton instances (block translators, item tag maps)
- Dynamic block/item ID allocation via `BlockTypeIds::newId()` and `ItemTypeIds::newId()`
- Full block state serialization/deserialization pipeline with fallback to `info_update`
## Drop 1 2026 (1.26.10)
Latest update includes:
- **Protocol 944** - Registered with full multiversion backward compatibility
- **Golden Dandelion** - New flower block with standard placement rules
- **Spawn Egg Baby Behavior** — Using a spawn egg on a matching ageable mob spawns the baby variant
## Architecture
vendor/nethergamesmc/bedrock-protocol/ ├── src/ProtocolInfo.php # Protocol constants & accepted versions ├── src/packets/ # All Bedrock protocol packets └── src/types/entity/ # Entity metadata flags & properties

src/network/mcpe/convert/ ├── BlockTranslator.php # Per-protocol block state translation ├── ItemTranslator.php # Per-protocol item ID translation ├── ItemTypeDictionaryFromDataHelper.php # Per-protocol item type loading └── TypeConverter.php # Session-level type conversion

src/entity/ ├── Ageable.php # Baby entity interface ├── Entity.php # Base entity with network sync └── EntitySizeInfo.php # Bounding box with baby scale

src/item/ ├── SpawnEgg.php # Baby spawn on entity interaction └── VanillaItems.php # Item registry

## Requirements
- PHP 8.1+
- PocketMine-MP API 5
