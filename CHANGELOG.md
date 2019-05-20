# Changelog
Relevant changes to the Accountant package will be documented here.

## v1.2.0 (2019-05-20)
### Changed
- Update artisan command prefixes to `accountant:*`.

## v1.1.7 (2019-03-16)
### Changed
- Ignore `existingPivotUpdated`, `attached` and `detached` events when toggling or syncing. Fixes ([#6](https://gitlab.com/altek/accountant/issues/6))

## v1.1.6 (2019-03-15)
### Changed
- Merged migration files into one, properly solving ([#2](https://gitlab.com/altek/accountant/issues/2)) and ([#5](https://gitlab.com/altek/accountant/issues/5))

## v1.1.5 (2019-02-26)
### Added
- Support version 5.8 of the Illuminate components

## v1.1.4 (2019-02-12)
### Added
- Ability to use a different value for the `recordable_id` when creating a `Ledger`

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
