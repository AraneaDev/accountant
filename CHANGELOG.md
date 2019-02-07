# Changelog
Relevant changes to the Accountant package will be documented here.

## v1.1.3 (2019-02-07)
### Fixed
- Fetching a user returns `null` when using a custom prefix

## v1.1.2 (2019-01-26)
### Added
- Documented migration requirements ([#2](https://gitlab.com/altek/accountant/issues/2))

### Fixed
- Calling `extract()` and `getData()` from a `Ledger` of a deleted `Recordable` model causes an error ([#3](https://gitlab.com/altek/accountant/issues/3))

### Changed
- Enable opcode optimisations via native function invocations

## v1.1.1 (2019-01-04)
### Added
- Migration to disable `NULL` values in the `properties`, `modified` and `extra` columns

## v1.1.0 (2019-01-01)
### Added
- Many-to-many (`BelongsToMany` and `MorphToMany`) relation support ([#1](https://gitlab.com/altek/accountant/merge_requests/1))
- `forceDeleted` event support
- `make:cipher` command

## v1.0.0 (2018-12-16)

- Initial release
