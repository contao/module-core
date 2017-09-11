<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\DependencyInjection\Compiler;

use Contao\CoreBundle\Controller\FragmentRegistry\FragmentInterface;
use Contao\CoreBundle\Controller\FragmentRegistry\FragmentRegistry;
use Contao\CoreBundle\Controller\FragmentRegistry\FragmentRegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Fragment registry compiler pass.
 *
 * @author Yanick Witschi <https://github.com/toflar>
 */
class FragmentRegistryPass implements CompilerPassInterface
{
    /**
     * Collect all the fragment and add them to the fragment registry.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('contao.fragment_registry')) {
            return;
        }

        $fragmentRegistry = $container->findDefinition('contao.fragment_registry');

        if (!$this->classImplementsInterface($fragmentRegistry->getClass(), FragmentRegistryInterface::class)) {
            return;
        }

        $fragments = $container->findTaggedServiceIds('contao.fragment');

        foreach ($fragments as $id => $options) {

            $fragment = $container->findDefinition($id);

            if (!$this->classImplementsInterface($fragment->getClass(), FragmentInterface::class)) {
                throw new LogicException(sprintf('The fragment class "%s" was registered as "contao.fragment" but does not implement the interface "%s".',
                    $fragment->getClass(),
                    FragmentInterface::class
                ));
            }

            if (!isset($options[0]['fragment']) || !isset($options[0]['type'])) {
                throw new RuntimeException('A service tagged as "contao.fragment" must have a "fragment" and "type" attribute set.');
            }

            // Mark all fragments as lazy so they are lazy loaded using
            // the proxy manager (which is why we need to require it in the
            // composer.json (otherwise the lazy definition will just be ignored)
            $fragment->setLazy(true);

            $fragmentRegistry->addMethodCall('addFragment', [$options[0]['type'], new Reference($id), $options[0]]);
        }
    }

    /**
     * Checks if a given class name implements a given interface name.
     *
     * @param string $class
     * @param string $interface
     *
     * @return bool
     */
    private function classImplementsInterface($class, $interface)
    {
        $ref = new \ReflectionClass($class);

        return $ref->implementsInterface($interface);
    }
}
