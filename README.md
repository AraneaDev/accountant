# Introduction
This package provides a convenient way to keep track of Eloquent model changes.

Data discrepancies that may indicate business concerns, suspect activities, and other actions that would otherwise pass unnoticed, can now be easily spotted.

[![Latest Stable Version](https://poser.pugx.org/altek/accountant/v/stable)](https://packagist.org/packages/altek/accountant) [![Total Downloads](https://poser.pugx.org/altek/accountant/downloads)](https://packagist.org/packages/altek/accountant) [![pipeline status](https://gitlab.com/altek/accountant/badges/master/pipeline.svg)](https://gitlab.com/altek/accountant/commits/master) [![coverage report](https://gitlab.com/altek/accountant/badges/master/coverage.svg)](https://gitlab.com/altek/accountant/commits/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/gl/altek/altek/accountant/badges/quality-score.png?b=master&s=b863b32db2dc1674d15d7c9396db46a4139db09e)](https://scrutinizer-ci.com/gl/altek/altek/accountant/?branch=master) [![License](https://poser.pugx.org/altek/accountant/license)](https://packagist.org/packages/altek/accountant)

## Version Information
 Version   | Illuminate    | Status             | PHP Version
-----------|---------------|--------------------|-------------
 1.x       | 5.2.x - 5.7.x | Actively supported | >= 7.1.3

## Key Features
- Many-to-many (`BelongsToMany` and `MorphToMany`) relation support;
- Event source style approach, by keeping **complete snapshots** of `Recordable` models when created, modified or retrieved;
- Ability to recreate `Recordable` model instances in the exact state they were in when recorded;
- Signed `Ledger` records for data integrity;
- Effortless [data integrity checks](docs/data-integrity-check.md);
- [Recording contexts](docs/configuration.md#recording-contexts);
- Huge customisation support ([drivers](docs/ledger-drivers.md), [resolvers](docs/resolvers.md), and more);
- Easy to follow [documentation](docs/index.md) and [troubleshooting](docs/troubleshooting.md) guide;
- Laravel and Lumen 5.2+ support;

## Documentation
The package documentation can be found [here](docs/index.md).

## Changelog
For information on recent changes, check the [CHANGELOG](CHANGELOG.md).

## Contributing
Contributions are always welcome, but before anything else, make sure you get acquainted with the [CONTRIBUTING](CONTRIBUTING.md) guide.

## Credits
- [Quetzy Garcia](https://gitlab.com/quetzyg)

## License
The **Accountant** package is open source software licensed under the [MIT LICENSE](LICENSE.md).
