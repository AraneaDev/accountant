# Ledger retrieval
`Ledger` records can be easily fetched, through typical [Eloquent](https://laravel.com/docs/5.7/eloquent) relations.

## Getting Ledgers
Example with an `Article` model:

```php
// Get the fourth Article created
$article = Article::find(4);

// Get the first Ledger
$ledger = $article->ledgers()->first();

// Get the last Ledger
$ledger = $article->ledgers()->latest()->first();

// Get the third Ledger
$ledger = $article->ledgers()->find(3);

// Get all Ledgers and traverse them
foreach ($article->ledgers()->get() as $ledger) {
    // ...
}
```

> **NOTICE:** If no custom ordering is applied, `Ledger` records will be returned by `created_at` in ascending order.

## Getting Ledgers with the associated User model
```php
// Get all the Ledgers and traverse them
$ledgers = $article->ledgers()->with('user')->get();

foreach ($ledgers as $ledger) {
    // ...
}
```

> **TIP:** Make sure the `User` [prefix](configuration.md#prefix) and [guards](configuration.md#auth-guards) are properly set.

## Getting the Ledger metadata
Retrieving an `array` with the `Ledger` metadata.

### Usage example
```php
// Get the first available Article
$article = Article::first();

// Get the latest Ledger
$ledger = $article->ledgers()->latest()->first();

var_dump($ledger->getMetadata());
```

### Output example
```php
array(11) {
  'ledger_id' =>
  int(2)
  'ledger_context' =>
  int(1)
  'ledger_event' =>
  string(8) "attached"
  'ledger_url' =>
  string(29) "http://example.com/articles/1"
  'ledger_ip_address'=>
  string(9) "127.0.0.1"
  'ledger_user_agent'=>
  string(68) "Mozilla/5.0 (X11; Linux x86_64; rv:63.0) Gecko/20100101 Firefox/63.0"
  'ledger_created_at' =>
  string(19) "2012-06-14 15:03:03"
  'ledger_updated_at' =>
  string(19) "2012-06-14 15:03:03"
  'ledger_signature' =>
  string(128) "7952b14f8a7eba08dec629a2292582e81d4e1b62d8fc843290c095eaad4fc17d71dd05dafff1a5c81b579c4324957c7f7df2608a5f0908e82e3bf94fc97631e2"
  'user_id' =>
  int(4)
  'user_type' =>
  string(15) "App\Models\User"
}
```

## Getting modified only Recordable properties (default)
When calling the `getData()` method with no arguments, only the **modified** `Recordable` properties will be included in the `array`. 

### Usage example
```php
// Get first available Article
$article = Article::first();

// Get the last Ledger
$ledger = $article->ledgers()->latest()->first();

var_dump($ledger->getData());
```

### Output example
```php
array(1) {
  'content' =>
  string(43) "First step: install the Accountant package."
}
```

## Getting all Recordable properties
To retrieve an `array` with **all** the `Recordable` properties when the recording took place, pass `true` to the `getData()` method.

### Usage example
```php
// Get the first available Article
$article = Article::first();

// Get the latest Ledger
$ledger = $article->ledgers()->latest()->first();

var_dump($ledger->getData(true));
```

### Output example
```php
array(7) {
  'title' =>
  string(39) "Keeping Track Of Eloquent Model Changes"
  'content' =>
  string(43) "First step: install the Accountant package."
  'published_at' =>
  string(19) "2012-06-18 21:32:34"
  'reviewed' =>
  bool(true)
  'updated_at' =>
  string(19) "2015-10-24 23:11:10"
  'created_at' =>
  string(19) "2012-06-14 15:03:03"
  'id' =>
  int(1)
}
```

> **TIP:** The `getMetadata()` and `getData()` methods will honour any established attribute **mutator** or **cast**.

## Getting pivot data
Support for recording pivot events was introduced in version **1.1.0**.

To get the relation and the properties, use the `getPivotData()` method.

### Usage example
```php
// Get the first available Article
$article = Article::first();

// Get the latest Ledger containing pivot data
$ledger = $article->ledgers()->whereIn('event', [
    'existingPivotUpdated',
    'attached',
    'detached',
])
->latest()
->first();

var_dump($ledger->getPivotData());
```

### Output example
```php
array(2) {
  'relation' =>
  string(8) "articles"
  'properties' =>
  array(2) {
    [0] =>
    array(3) {
      'user_id' =>
      int(1)
      'liked' =>
      bool(false)
      'article_id' =>
      int(2)
    }
    [1] =>
    array(3) {
      'user_id' =>
      int(1)
      'liked' =>
      bool(true)
      'article_id' =>
      int(1)
    }
  }
}
```
