<?php

namespace Journal\Blog\UnitTests\Container;

use Journal\Blog\Container\DIContainer;
use Journal\Blog\Exceptions\NotFoundException;
use Journal\Blog\Repositories\UserRepository\InMemoryUsersRepository;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\UnitTests\Container\SomeClassWithoutDependencies;
use Journal\Blog\UnitTests\Container\ClassDependingOnAnother;
use PHPUnit\Framework\TestCase;



class DIContainerTest extends TestCase
{
    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        $container = new DIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: Journal\Blog\UnitTests\Container\SomeClass'
        );

        $container->get(SomeClass::class);
    }

    public function testItResolveClassWithoutDependencies(): void
    {
        $container = new DIContainer();

        $object = $container->get(SomeClassWithoutDependencies::class);

        $this->assertInstanceOf(SomeClassWithoutDependencies::class, $object);
    }

    public function testItResolvesClassByContract(): void
    {
        $container = new DIContainer();

        $container->bind(
            UserRepositoryInterface::class,
            InMemoryUsersRepository::class
        );

        $object = $container->get(UserRepositoryInterface::class);

        $this->assertInstanceOf(InMemoryUsersRepository::class, $object);
    }

    public function testItReturnsPredefinedObject(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();
        // Устанавливаем правило, по которому
// всякий раз, когда контейнеру нужно
// вернуть объект типа SomeClassWithParameter,
// он возвращал бы предопределённый объект
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );
        // Пытаемся получить объект типа SomeClassWithParameter
        $object = $container->get(SomeClassWithParameter::class);
        // Проверяем, что контейнер вернул
// объект того же типа
        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );
        // Проверяем, что контейнер вернул
// тот же самый объект
        $this->assertSame(42, $object->value());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();
        // Устанавливаем правило получения
// объекта типа SomeClassWithParameter
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );
        // Пытаемся получить объект типа ClassDependingOnAnother
        $object = $container->get(ClassDependingOnAnother::class);
        // Проверяем, что контейнер вернул
// объект нужного нам типа
        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }
}