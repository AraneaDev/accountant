# Introduction
This package provides a convenient way to keep track of Eloquent model changes.

Discrepancies that may indicate business concerns or suspect activities and other actions which would otherwise pass unnoticed, are now easily spotted.

[![Latest Stable Version](https://poser.pugx.org/altek/accountant/v/stable)](https://packagist.org/packages/altek/accountant) [![Total Downloads](https://poser.pugx.org/altek/accountant/downloads)](https://packagist.org/packages/altek/accountant) [![Build Status](https://scrutinizer-ci.com/gl/altek/altek/accountant/badges/build.png?b=master&s=d57e0f845b51b5c122f6b8d3069e607316df3feb)](https://scrutinizer-ci.com/gl/altek/altek/accountant/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/gl/altek/altek/accountant/badges/quality-score.png?b=master&s=b863b32db2dc1674d15d7c9396db46a4139db09e)](https://scrutinizer-ci.com/gl/altek/altek/accountant/?branch=master) [![License](https://poser.pugx.org/altek/accountant/license)](https://packagist.org/packages/altek/accountant)

## Version Information
 Version   | Illuminate    | Status             | PHP Version
:----------|:--------------|:-------------------|:------------
 1.x       | 5.2.x - 5.7.x | Actively supported | >= 7.1.3

## Key Features
- Stores **complete snapshots** of `Recordable` models when created, retrieved and modified;
- Recreates `Recordable` model instances in the exact state they were in at a given point in time;
- Signed `Ledger` records;
- Effortless [data integrity checks](docs/data-integrity-check.md);
- [Recording contexts](docs/configuration.md#recording-contexts);
- Huge support for customisation ([drivers](docs/ledger-drivers.md), [resolvers](docs/resolvers.md), and more);
- Easy to follow documentation and troubleshooting guide;
- Laravel and Lumen 5.2+ support;

## Documentation
* [Installation](docs/installation.md)
* [Configuration](docs/configuration.md)
* Recordable
  * [Model Setup](docs/recordable-model-setup.md)
  * [Configuration](docs/recordable-configuration.md)
* Ledger
  * [Retrieval](docs/ledger-retrieval.md)
  * [Migration](docs/ledger-migration.md)
  * [Implementation](docs/ledger-implementation.md)
  * [Drivers](docs/ledger-drivers.md)
  * [Events](docs/ledger-events.md)
* Advanced
  * [Data Integrity Check](docs/data-integrity-check.md)
  * [Ledger Extra](docs/ledger-extra.md)
  * [Ledger Extract](docs/ledger-extract.md)
  * [Resolvers](docs/resolvers.md)
  * [Ciphers](docs/ciphers.md)
  * [Accountant](docs/accountant.md)
* Help
  * [Troubleshooting](docs/troubleshooting.md)

## Changelog
For information on recent changes, check the [CHANGELOG](CHANGELOG.md).

## Contributing
Contributions are always welcome, but before anything else, make sure you get acquainted with the [Contributing](CONTRIBUTING.md) guide.

## Credits
- [Quetzy Garcia](https://gitlab.com/quetzyg)

## Alternatives
Here are other packages that provide similar features:
- [Laravel Auditing](https://github.com/owen-it/laravel-auditing)
- [Revisionable](https://packagist.org/packages/venturecraft/revisionable)

## License
The **Accountant** package is open source software licensed under the [MIT LICENSE](LICENSE.md).
