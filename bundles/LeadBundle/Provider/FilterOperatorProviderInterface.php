<?php

declare(strict_types=1);

namespace Milex\LeadBundle\Provider;

interface FilterOperatorProviderInterface
{
    /**
     * Finds all operators and reutrn them in an array.
     *
     * @return mixed[]
     */
    public function getAllOperators(): array;
}
