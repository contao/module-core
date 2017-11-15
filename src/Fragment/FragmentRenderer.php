<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Fragment;

use Contao\CoreBundle\Fragment\Reference\FragmentReference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class FragmentRenderer implements FragmentRendererInterface
{
    /**
     * @var FragmentRegistryInterface
     */
    protected $fragmentRegistry;

    /**
     * @var FragmentHandler
     */
    protected $fragmentHandler;
    /**
     * @var ServiceLocator
     */
    private $preHandlers;

    /**
     * Constructor.
     *
     * @param FragmentRegistryInterface $fragmentRegistry
     * @param FragmentHandler           $fragmentHandler
     * @param ServiceLocator            $preHandlers
     */
    public function __construct(FragmentRegistryInterface $fragmentRegistry, FragmentHandler $fragmentHandler, ServiceLocator $preHandlers)
    {
        $this->fragmentRegistry = $fragmentRegistry;
        $this->fragmentHandler = $fragmentHandler;
        $this->preHandlers = $preHandlers;
    }

    /**
     * {@inheritdoc}
     */
    public function render(FragmentReference $uri): ?string
    {
        $config = $this->fragmentRegistry->get($uri->controller);

        if (null === $config) {
            throw new UnknownFragmentException(sprintf('Fragment with identifier "%s" was not found', $uri->controller));
        }

        $this->preHandleFragment($uri, $config);

        return $this->fragmentHandler->render(
            $uri,
            $config->getRenderer(),
            $config->getOptions()
        );
    }

    /**
     * Adds generic attributes and query parameters before rendering.
     *
     * @param FragmentReference $uri
     * @param FragmentConfig    $config
     */
    private function preHandleFragment(FragmentReference $uri, FragmentConfig $config)
    {
        if (isset($GLOBALS['objPage']) && !isset($uri->attributes['pageModel'])) {
            $uri->attributes['pageModel'] = $GLOBALS['objPage']->id;
        }

        if ($this->preHandlers->has($uri->controller)) {
            $this->preHandlers->get($uri->controller)->preHandleFragment($uri, $config);
        }
    }
}
