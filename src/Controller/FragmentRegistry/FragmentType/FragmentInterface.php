<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Controller\FragmentRegistry\FragmentType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface for fragments. This is an abstraction layer for the Symfony fragment
 * handler. The Symfony fragment handler requires to register real controllers
 * for every single fragment. In Contao we have different fragment types as well
 * as fragments themselves (types are modules, content elements etc. names are
 * e.g. "text", "headline" etc.). It would be very tedious work to register a
 * controller for every single one of them. This abstraction layer allows you
 * to tag your service, implement the FragmentInterface (or a subclass of it)
 * and the rest is taken care of for you.
 *
 * @author Yanick Witschi <https://github.com/toflar>
 */
interface FragmentInterface
{
    /**
     * Gets the name of the fragment. Should be unique accross the whole
     * system so use something that ideally includes your vendor name like
     * "contao.text".
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the fragment render strategy. Symfony core provides "inline",
     * "esi", "ssi" and "hinclude" but everybody can extend the available
     * renderes by using the service tag "kernel.fragment_renderer".
     * The passed configuration array contains whatever the triggering code
     * wants to pass on to your fragment.
     * See FragmentRegistryInterface::renderFragment()
     *
     * @param array $configuration
     *
     * @return string
     */
    public function getRenderStrategy(array $configuration);

    /**
     * Gets the render options for the render strategy. Most of the times
     * this is an empty array. Some strategies don't even support options but
     * some (e.g. like ESI) do to add e.g. comments to the <esi> tag.
     * The passed configuration array contains whatever the triggering code
     * wants to pass on to your fragment.
     * See FragmentRegistryInterface::renderFragment()
     *
     * @param array $configuration
     *
     * @return array
     */
    public function getRenderOptions(array $configuration);

    /**
     * Your fragment likely needs some request query parameters if you use any
     * other render strategy than "inline". Return them here as key->value and
     * you will receive them as query parameters in the renderAction() method.
     * The passed configuration array contains whatever the triggering code
     * wants to pass on to your fragment.
     * See FragmentRegistryInterface::renderFragment()
     *
     * @param array $configuration
     *
     * @return array
     */
    public function getQueryParameters(array $configuration);

    /**
     * The render action.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function renderAction(Request $request);
}
