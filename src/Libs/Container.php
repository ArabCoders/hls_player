<?php

declare(strict_types=1);

namespace App\Libs;

use League\Container\ReflectionContainer;
use App\Libs\Extenders\PSRContainer as BaseContainer;

final class Container
{
    private static BaseContainer|null $container = null;

    public static function init(BaseContainer|null $container = null): static
    {
        if (null === self::$container) {
            if (null === $container) {
                $container = new BaseContainer();
                $container->defaultToShared(true);
                $reflectionContainer = new ReflectionContainer(true);
                $container->delegate($reflectionContainer);
                $container->addShared(ReflectionContainer::class, $reflectionContainer);
                $reflectionContainer = null;
            }
            self::$container = $container;
        }

        return new static();
    }

    public static function add(string $id, mixed $concrete): static
    {
        self::addService($id, $concrete);

        return new static();
    }

    private static function addService(string $id, callable|array|object $definition): void
    {
        if (is_callable($definition) || is_object($definition)) {
            self::$container->add($id, $definition);
        }

        if (is_array($definition)) {
            $service = self::$container->add($id, $definition['class']);

            if (!empty($definition['args'])) {
                $service->addArguments($definition['args']);
            }

            if (!empty($definition['tag'])) {
                $service->addTag($definition['tag']);
            }

            if (!empty($definition['alias'])) {
                $service->setAlias($definition['alias']);
            }

            if (array_key_exists('shared', $definition)) {
                $service->setShared((bool)$definition['shared']);
            }

            if (!empty($definition['call']) && is_array($definition['call'])) {
                $service->addMethodCalls($definition['call']);
            }
        }
    }

    public static function get($id)
    {
        return self::$container->get($id);
    }

    public static function getNew($id)
    {
        return self::$container->getNew($id);
    }

    public static function has(string $id): bool
    {
        return self::$container->has($id);
    }

    public static function getContainer(): BaseContainer
    {
        if (null === self::$container) {
            throw new \RuntimeException('PSRContainer has not been initialized.');
        }

        return self::$container;
    }
}
