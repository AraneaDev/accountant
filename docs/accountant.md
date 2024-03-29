# Accountant
The `Accountant` class is in charge of recording and clearing `Ledger` records.

Normally, there's no reason for its explicit use, given the `RecordableObserver` takes care of those tasks behind the scenes.

Still, here are two usage examples.

## Using the Accountant Facade in a controller
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Altek\Accountant\Facades\Accountant;
use App\Http\Requests\Request;
use App\Models\Article;

class ArticleController extends Illuminate\Routing\Controller
{
    public function update(Request $request, Article $article)
    {
        // ...

        if ($article->update($request->all())) {
            Accountant::record($article, 'updated');
        }

        // ...
    }
}
```

## Injecting the Accountant as a dependency in a controller
With the `AccountantServiceProvider` registered, the IoC can be used to resolve and inject an `Accountant` instance.

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Altek\Accountant\Contracts\Accountant;
use App\Http\Requests\Request;
use App\Models\Article;

class ArticleController extends Illuminate\Routing\Controller
{
    public function update(Request $request, Article $article, Accountant $accountant)
    {
        // ...

        if ($article->update($request->all())) {
            $accountant->record($article, 'updated');
        }

        // ...
    }
}
```

::: warning NOTICE
To avoid duplicate `Ledger` records, make sure that the `$recordableEvents` property in the model is set to an empty `array` or disable recording altogether with the `disableRecording()` static method!
:::
