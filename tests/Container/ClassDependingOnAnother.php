<?php
namespace Journal\Blog\UnitTests\Container;

use Journal\Blog\UnitTests\Container\SomeClassWithoutDependencies;
use Journal\Blog\UnitTests\Container\SomeClassWithParameter;

class ClassDependingOnAnother
{
    public function __construct(
        private SomeClassWithoutDependencies $one,
        private SomeClassWithParameter $two,
    )
    {
    }
}