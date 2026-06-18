# Changelog

All notable changes to this project are documented in this file.

## [2.0.0] - Unreleased

Version 2 is a modernisation of the library. **The generated AFB160 / AFB320
file content is byte-for-byte identical to version 1** (locked by tests); only
the surrounding code, packaging and namespaces changed.

### Changed (breaking)

- **PHP requirement raised to `^8.1`.** The whole codebase now uses
  `declare(strict_types=1)`, typed properties, typed signatures, constructor
  promotion, `match`, etc.
- **Namespaces consolidated under a single PSR-4 root `Ladina\CFONB\`:**
  | v1 | v2 |
  | --- | --- |
  | `Ladina\CFONB\AFB160` / `AFB320` | unchanged |
  | `Ladina\Factory\CFONBFactory` | `Ladina\CFONB\Factory\CFONBFactory` |
  | `Tools\Str`, `Tools\TimeZone` | `Ladina\CFONB\Support\Str`, `…\Support\TimeZone` |
  | `Library\Exception\*` | `Ladina\CFONB\Exception\*` |
- Source files moved from `src/CFONB/`, `src/Tools/` to `src/` and `src/Support/`.
- Example scripts moved to `examples/`.

### Added

- Simple factory shortcuts: `CFONBFactory::afb160($data)` and
  `CFONBFactory::afb320($data)`.
- `Ladina\CFONB\Exception\ExceptionInterface` so all library errors can be
  caught with a single `catch`.
- A PHPUnit 10 test suite (golden-master + unit tests) run with
  `failOnWarning` / `failOnDeprecation`.
- `.gitattributes` to keep the Composer dist archive small.

### Fixed

- **`composer.json` could not be installed cleanly from Packagist:** removed the
  stray `minimum-stability: dev` and the self-referencing `repositories` VCS
  entry, and repaired the corrupted (null-byte) description.
- **Fixed-width overflow:** values are now sanitised *before* being truncated to
  their field length, so a transliteration that grows a string (e.g. `Ä` → `AE`)
  can no longer push a record past its fixed width.
- Typo `Cache-Controlq` → `Cache-Control` in the download headers.
- `ob_clean()` is only called when an output buffer is active (no more notice).
- `CFONBFactory::generateAFB()` now throws on an unknown norm instead of
  silently falling back to AFB160.
- Removed the duplicated required-fields validation in the factory (the
  constructor already performs it).

## [1.x]

- Initial AFB160 / AFB320 generation.
