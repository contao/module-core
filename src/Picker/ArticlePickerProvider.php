<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Picker;

use Contao\DataContainer;

/**
 * Provides the article picker.
 *
 * @author Andreas Schempp <https://github.com/aschempp>
 */
class ArticlePickerProvider extends AbstractPickerProvider
{

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'articlePicker';
    }

    /**
     * {@inheritdoc}
     */
    protected function getLinkClass()
    {
        return 'articles';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsContext($context)
    {
        return 'link' === $context && $this->getUser()->hasAccess('article', 'modules');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsValue(PickerConfig $config)
    {
        return false !== strpos($config->getValue(), '{{article_url::');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteParameters(PickerConfig $config)
    {
        return [
            'do' => 'article',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareConfig(PickerConfig $config, DataContainer $dc)
    {
        if ('tl_article' !== $dc->table) {
            return null;
        }

        $result = ['fieldType' => $config->getExtra('fieldType')];

        if ('link' === $config->getContext() && $this->supportsValue($config)) {
            $result['value'] = str_replace(['{{article_url::', '}}'], '', $config->getValue());
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareValue(PickerConfig $config, $value)
    {
        return '{{article_url::'.$value.'}}';
    }
}
