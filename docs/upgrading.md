# Upgrading
This document provides the necessary steps to successfully upgrade to the latest version.

## Upgrade from version 1.0.x to version 1.1.x
Version **1.1.x** introduces pivot event recording, which stores data in a new `pivot` column.

Make sure to publish the new migration file

```sh
php artisan vendor:publish --tag="accountant-migrations"
```

and execute the migration afterward

```sh
php artisan migrate
```

For additional information about pivot event recording, refer to the [Events](recordable-configuration.md#events) section of the `Recordable` configuration.
