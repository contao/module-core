<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Controller;

use Contao\BackendAlerts;
use Contao\BackendConfirm;
use Contao\BackendFile;
use Contao\BackendHelp;
use Contao\BackendIndex;
use Contao\BackendMain;
use Contao\BackendPage;
use Contao\BackendPassword;
use Contao\BackendPopup;
use Contao\BackendPreview;
use Contao\BackendSwitch;
use Contao\CoreBundle\Picker\PickerConfig;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Handles the Contao back end routes.
 *
 * @author Andreas Schempp <https://github.com/aschempp>
 * @author Leo Feyer <https://github.com/leofeyer>
 * @author David Greminger <https://github.com/bytehead>
 *
 * @Route(defaults={"_scope" = "backend", "_token_check" = true})
 */
class BackendController extends Controller
{
    /**
     * Runs the main back end controller.
     *
     * @return Response
     *
     * @Route("/contao", name="contao_backend")
     */
    public function mainAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendMain();

        return $controller->run();
    }

    /**
     * Renders the back end login form.
     *
     * @return Response
     *
     * @Route("/contao/login", name="contao_backend_login")
     */
    public function loginAction(): Response
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendIndex();

        return $controller->run();
    }

    /**
     * Renders the "set new password" form.
     *
     * @return Response
     *
     * @Route("/contao/password", name="contao_backend_password")
     */
    public function passwordAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendPassword();

        return $controller->run();
    }

    /**
     * Renders the front end preview.
     *
     * @return Response
     *
     * @Route("/contao/preview", name="contao_backend_preview")
     */
    public function previewAction(): Response
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $this->container->get('contao.framework')->initialize();
        $this->container->get('contao.security.preview_authenticator')->authenticateFrontend($request->get('user'));

        $controller = new BackendPreview();

        return $controller->run();
    }

    /**
     * Renders the "invalid request token" screen.
     *
     * @return Response
     *
     * @Route("/contao/confirm", name="contao_backend_confirm")
     */
    public function confirmAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendConfirm();

        return $controller->run();
    }

    /**
     * Renders the file picker.
     *
     * @return Response
     *
     * @Route("/contao/file", name="contao_backend_file")
     */
    public function fileAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendFile();

        return $controller->run();
    }

    /**
     * Renders the help content.
     *
     * @return Response
     *
     * @Route("/contao/help", name="contao_backend_help")
     */
    public function helpAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendHelp();

        return $controller->run();
    }

    /**
     * Renders the page picker.
     *
     * @return Response
     *
     * @Route("/contao/page", name="contao_backend_page")
     */
    public function pageAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendPage();

        return $controller->run();
    }

    /**
     * Renders the pop-up content.
     *
     * @return Response
     *
     * @Route("/contao/popup", name="contao_backend_popup")
     */
    public function popupAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendPopup();

        return $controller->run();
    }

    /**
     * Renders the front end preview switcher.
     *
     * @return Response
     *
     * @Route("/contao/switch", name="contao_backend_switch")
     */
    public function switchAction(): Response
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendSwitch();

        return $controller->run();
    }

    /**
     * Renders the alerts content.
     *
     * @return Response
     *
     * @Route("/contao/alerts", name="contao_backend_alerts")
     */
    public function alertsAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendAlerts();

        return $controller->run();
    }

    /**
     * Symfony will un-authenticate the user automatically by calling this route.
     *
     * @Route("/contao/logout", name="contao_backend_logout")
     */
    public function logoutAction()
    {
    }

    /**
     * Handles the picker redirect.
     * Redirects the user to the Contao back end and includes the picker query parameter. It will determine
     * the current provider URL based on the value (usually read dynamically via JavaScript).
     *
     * @param Request $request
     *
     * @throws BadRequestHttpException
     *
     * @return RedirectResponse
     *
     * @Route("/_contao/picker", name="contao_backend_picker")
     */
    public function pickerAction(Request $request)
    {
        $extras = [];

        if ($request->query->has('extras')) {
            $extras = $request->query->get('extras');

            if (!is_array($extras)) {
                throw new BadRequestHttpException('Invalid picker extras');
            }
        }

        $config = new PickerConfig($request->query->get('context'), $extras, $request->query->get('value'));
        $picker = $this->container->get('contao.picker.builder')->create($config);

        if (null === $picker) {
            throw new BadRequestHttpException('Unsupported picker context');
        }

        return new RedirectResponse($picker->getCurrentUrl());
    }
}
