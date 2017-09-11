<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Controller\FragmentRegistry;

/**
 * Interface for fragments that provide a category such as page types or
 * content elements that are part of a certain category.
 *
 * @author Yanick Witschi <https://github.com/toflar>
 */
interface ProvidesCategoryInterface extends FragmentInterface
{
    /**
     * Gets the category of the fragment.
     *
     * @return string
     */
    public static function getCategory();
}
