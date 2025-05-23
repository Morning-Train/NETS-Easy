<?php

namespace Morningtrain\NETSEasy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Morningtrain\NETSEasy\NETSEasy
 */
class NETSEasy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Morningtrain\NETSEasy\NETSEasy::class;
    }
}
