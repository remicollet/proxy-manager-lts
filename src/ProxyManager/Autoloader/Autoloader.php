<?php

declare(strict_types=1);

namespace ProxyManager\Autoloader;

use ProxyManager\FileLocator\FileLocatorInterface;
use ProxyManager\Inflector\ClassNameInflectorInterface;
use function class_exists;
use function file_exists;

/**
 * {@inheritDoc}
 */
class Autoloader implements AutoloaderInterface
{
    protected FileLocatorInterface $fileLocator;
    protected ClassNameInflectorInterface $classNameInflector;

    public function __construct(FileLocatorInterface $fileLocator, ClassNameInflectorInterface $classNameInflector)
    {
        $this->fileLocator        = $fileLocator;
        $this->classNameInflector = $classNameInflector;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress DocblockTypeContradiction class-string has still to be checked in an autoloader
     */
    public function __invoke(string $className) : bool
    {
        if (class_exists($className, false)) {
            return false;
        }

        /** @psalm-var class-string $className */
        if (! $this->classNameInflector->isProxyClassName($className)) {
            return false;
        }

        $file = $this->fileLocator->getProxyFileName($className);

        if (! file_exists($file)) {
            return false;
        }

        /* @noinspection PhpIncludeInspection */
        /* @noinspection UsingInclusionOnceReturnValueInspection */
        return (bool) require_once $file;
    }
}
