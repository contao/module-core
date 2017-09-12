<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Controller;

use Contao\FrontendCron;
use Contao\FrontendIndex;
use Contao\FrontendShare;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Handles the Contao frontend routes.
 *
 * @author Andreas Schempp <https://github.com/aschempp>
 * @author Leo Feyer <https://github.com/leofeyer>
 *
 * @Route(defaults={"_scope" = "frontend", "_token_check" = true})
 */
class FrontendController extends Controller
{
    /**
     * Runs the main front end controller.
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new FrontendIndex();

        return $controller->run();
    }

    /**
     * Runs the command scheduler.
     *
     * @return Response
     *
     * @Route("/_contao/cron", name="contao_frontend_cron")
     */
    public function cronAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new FrontendCron();

        return $controller->run();
    }

    /**
     * Renders the content syndication dialog.
     *
     * @return RedirectResponse
     *
     * @Route("/_contao/share", name="contao_frontend_share")
     */
    public function shareAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new FrontendShare();

        return $controller->run();
    }

    /**
     * Symfony will un-authenticate the user automatically by calling this route.
     * Redirects to the login route (see security.yml for logout.target)
     *
     * @Route("/_contao/logout", name="contao_frontend_logout")
     */
    public function logoutAction()
    {
    }

    /**
     * Symfony security login route
     *
     * @Route("/_contao/login", name="contao_frontend_login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
    }
}
