<?php

namespace Altek\Accountant\Contracts;

interface Identifiable
{
    /**
     * Get the User unique identifier.
     *
     * @return mixed
     */
    public function getIdentifier();
}
