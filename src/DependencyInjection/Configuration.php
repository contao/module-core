<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Adds the Contao configuration structure.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * Constructor.
     *
     * @param bool $debug If kernel debug mode is enabled
     */
    public function __construct($debug)
    {
        $this->debug = (bool) $debug;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('contao');

        $rootNode
            ->children()
                ->booleanNode('prepend_locale')
                    ->defaultFalse()
                ->end()
                ->scalarNode('encryption_key')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('url_suffix')
                    ->defaultValue('.html')
                ->end()
                ->scalarNode('upload_path')
                    ->cannotBeEmpty()
                    ->defaultValue('files')
                    ->validate()
                        ->ifTrue(function ($v) {
                            return preg_match(
                                '@^(app|assets|contao|plugins|share|system|templates|vendor|web)(/|$)@',
                                $v
                            );
                        })
                        ->thenInvalid('%s')
                    ->end()
                ->end()
                ->scalarNode('csrf_token_name')
                    ->cannotBeEmpty()
                    ->defaultValue('contao_csrf_token')
                ->end()
                ->booleanNode('pretty_error_screens')
                    ->defaultTrue()
                ->end()
                ->integerNode('error_level')
                    ->min(-1)
                    ->max(32767)
                    ->defaultValue(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED)
                ->end()
                ->arrayNode('image')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('bypass_cache')
                            ->defaultValue($this->debug)
                        ->end()
                        ->scalarNode('target_path')
                            ->defaultValue('assets/images')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('disable_ip_check')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('autologin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('cookie_name')
                                    ->cannotBeEmpty()
                                    ->defaultValue('FE_AUTO_LOGIN_MULTI')
                                ->end()
                                ->integerNode('cookie_lifetime')
                                    ->min(0)
                                    ->defaultValue(7776000)
                                ->end()
                                ->scalarNode('formfield_name')
                                    ->cannotBeEmpty()
                                    ->defaultValue('autologin')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
