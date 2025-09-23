<?php

namespace alexbabintsev\Magicline\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \alexbabintsev\Magicline\Magicline
 */
class Magicline extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \alexbabintsev\Magicline\Magicline::class;
    }
}
