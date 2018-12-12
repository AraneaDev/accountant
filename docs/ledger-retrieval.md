# Ledger retrieval
`Ledger` records can be easily fetched, via typical [Eloquent](https://laravel.com/docs/5.7/eloquent) relations.

## Getting Ledgers
Example using an `Article` model:

```php
// Get the fourth Article created
$article = Article::find(4);

// Get all the Ledgers
$ledgers = $article->ledgers;

// Get the first Ledger
$ledger = $article->ledgers()->first();

// Get the last Ledger
$ledger = $article->ledgers()->latest()->first();

// Get the third Ledger
$ledger = $article->ledgers()->find(3);
```

> **NOTICE:** `Ledger` records fetched through the magic `->ledgers` relation attribute, result in a `Collection` ordered by `created_at` in ascending order (oldest to newest).

## Getting Ledgers with the associated User model
```php
// Get all the Ledgers
$ledgers = $article->ledgers()->with('user')->get();
```

> **TIP:** Make sure to properly configure the `User` **prefix** and **guards** in `config/accountant.php`.

## Getting Ledger metadata
Retrieving an `array` with `Ledger` metadata:

### Usage example
```php
// Get the first available Article
$article = Article::first();

// Get the last Ledger
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
  string(7) "updated"
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

## Getting modified Recordable properties (default)
When calling the `getData()` method without arguments, an `array` only including the **modified** properties of the `Recordable` model is returned. 

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
To retrieve an `array` with **all** the properties of the `Recordable` model at the time of recording, pass `true` as the only argument to the `getData()` method.

### Usage example
```php
// Get the first available Article
$article = Article::first();

// Get the last Ledger
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
