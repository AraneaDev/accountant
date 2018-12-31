<?php

declare(strict_types=1);

namespace Altek\Accountant;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Facades\Accountant;

class RecordableObserver
{
    /**
     * Is the model being restored?
     *
     * @var bool
     */
    public static $restoring = false;

    /**
     * Handle the retrieved event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     *
     * @return void
     */
    public function retrieved(Recordable $model): void
    {
        Accountant::record($model, 'retrieved');
    }

    /**
     * Handle the created event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     *
     * @return void
     */
    public function created(Recordable $model): void
    {
        Accountant::record($model, 'created');
    }

    /**
     * Handle the updated event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     *
     * @return void
     */
    public function updated(Recordable $model): void
    {
        // Ignore the updated event when restoring
        if (!static::$restoring) {
            Accountant::record($model, 'updated');
        }
    }

    /**
     * Handle the restoring event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     *
     * @return void
     */
    public function restoring(Recordable $model): void
    {
        // When restoring a model, an updated event is also fired.
        // By keeping track of the main event that took place, we
        // avoid creating a second Ledger with wrong values
        static::$restoring = true;
    }

    /**
     * Handle the restored event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     *
     * @return void
     */
    public function restored(Recordable $model): void
    {
        Accountant::record($model, 'restored');

        // Once the model is restored, we need to revert the state,
        // in case a legitimate update event is fired
        static::$restoring = false;
    }

    /**
     * Handle the deleted event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     *
     * @return void
     */
    public function deleted(Recordable $model): void
    {
        Accountant::record($model, 'deleted');
    }

    /**
     * Handle the forceDeleted event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     *
     * @return void
     */
    public function forceDeleted(Recordable $model): void
    {
        Accountant::record($model, 'forceDeleted');
    }

    /**
     * Handle the toggled event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     * @param string                                 $relation
     * @param array                                  $attributes
     *
     * @return void
     */
    public function toggled(Recordable $model, string $relation, array $attributes): void
    {
        Accountant::record($model, 'toggled', $relation, $attributes);
    }

    /**
     * Handle the synced event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     * @param string                                 $relation
     * @param array                                  $attributes
     *
     * @return void
     */
    public function synced(Recordable $model, string $relation, array $attributes): void
    {
        Accountant::record($model, 'synced', $relation, $attributes);
    }

    /**
     * Handle the existingPivotUpdated event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     * @param string                                 $relation
     * @param array                                  $attributes
     *
     * @return void
     */
    public function existingPivotUpdated(Recordable $model, string $relation, array $attributes): void
    {
        Accountant::record($model, 'existingPivotUpdated', $relation, $attributes);
    }

    /**
     * Handle the attached event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     * @param string                                 $relation
     * @param array                                  $attributes
     *
     * @return void
     */
    public function attached(Recordable $model, string $relation, array $attributes): void
    {
        Accountant::record($model, 'attached', $relation, $attributes);
    }

    /**
     * Handle the detached event.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     * @param string                                 $relation
     * @param array                                  $attributes
     *
     * @return void
     */
    public function detached(Recordable $model, string $relation, array $attributes): void
    {
        Accountant::record($model, 'detached', $relation, $attributes);
    }
}
