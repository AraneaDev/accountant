# Introduction
This package provides a convenient way to keep track of Eloquent model changes.

Data discrepancies that may indicate business concerns, suspect activities, and other actions that would otherwise pass unnoticed, can now be easily spotted.

[![Latest Stable Version](https://poser.pugx.org/altek/accountant/v/stable)](https://packagist.org/packages/altek/accountant) [![Total Downloads](https://poser.pugx.org/altek/accountant/downloads)](https://packagist.org/packages/altek/accountant) [![Build Status](https://scrutinizer-ci.com/gl/altek/altek/accountant/badges/build.png?b=master&s=d57e0f845b51b5c122f6b8d3069e607316df3feb)](https://scrutinizer-ci.com/gl/altek/altek/accountant/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/gl/altek/altek/accountant/badges/quality-score.png?b=master&s=b863b32db2dc1674d15d7c9396db46a4139db09e)](https://scrutinizer-ci.com/gl/altek/altek/accountant/?branch=master) [![License](https://poser.pugx.org/altek/accountant/license)](https://packagist.org/packages/altek/accountant)

## Version Information
 Version   | Illuminate    | Status             | PHP Version
-----------|---------------|--------------------|-------------
 1.x       | 5.2.x - 5.7.x | Actively supported | >= 7.1.3

## Motivation
For the past two years, I've been actively involved in a similar [project](https://github.com/owen-it/laravel-auditing), where I took over maintenance and did new releases.

Over time, I had new concepts and feature ideas, but implementing some of them would mean breaking backward compatibility.

Due to the nature of that project, and with an increasing number of people relying on it, starting off fresh seemed the best approach.

Given this package leverages on previous work, there's functionality in common, some that was removed, and other completely new!

## Key Features
- Stores **complete snapshots** of `Recordable` models when created, retrieved or modified;
- Ability to recreate `Recordable` model instances in the exact state they were in when recorded;
- Signed `Ledger` records;
- Effortless [data integrity checks](docs/data-integrity-check.md);
- [Recording contexts](docs/configuration.md#recording-contexts);
- Huge support for customisation ([drivers](docs/ledger-drivers.md), [resolvers](docs/resolvers.md), and more);
- Easy to follow documentation and troubleshooting guide;
- Laravel and Lumen 5.2+ support;

## Documentation
The package documentation can be found [here](docs/index.md).

## Changelog
For information on recent changes, check the [CHANGELOG](CHANGELOG.md).

## Contributing
Contributions are always welcome, but before anything else, make sure you get acquainted with the [Contributing](CONTRIBUTING.md) guide.

## Credits
- [Quetzy Garcia](https://gitlab.com/quetzyg)

## Alternative
If this package doesn't suit you, try [Laravel Auditing](https://github.com/owen-it/laravel-auditing), which provides a similar set of features.

## License
The **Accountant** package is open source software licensed under the [MIT LICENSE](LICENSE.md).
