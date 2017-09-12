<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao;

use Patchwork\Utf8;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


/**
 * Front end module "login".
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleLogin extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_login';

	/**
	 * Flash type
	 * @var string
	 */
	protected $strFlashType = 'contao.' . TL_MODE . '.error';


	/**
	 * Display a login form
	 *
	 * @return string
	 */
	public function generate()
	{
        /** @var Session $session */
        $session = \System::getContainer()->get('session');

        /** @var Request $request */
        $request = \System::getContainer()->get('request_stack')->getCurrentRequest();

        /** @var AuthenticationUtils $authenticationUtils */
        $authenticationUtils = \System::getContainer()->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();

		if (TL_MODE == 'BE')
		{
			/** @var BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['login'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set the last page visited (see #8632)
		if (!$request->isMethod(Request::METHOD_POST) && $this->redirectBack && ($strReferer = $this->getReferer()) != \Environment::get('request'))
		{
			$session->set('LAST_PAGE_VISITED', $strReferer);
		}

		if ($error)
		{
            $session->getFlashBag()->set($this->strFlashType, $GLOBALS['TL_LANG']['ERR']['invalidLogin']);
        }

        return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
	    /** @var Session $session */
	    $session = \System::getContainer()->get('session');

	    /** @var RouterInterface $router */
	    $router = \System::getContainer()->get('router');

        /** @var TokenInterface $token */
        $token = \System::getContainer()->get('security.token_storage')->getToken();

        /** @var Request $request */
        $request = \System::getContainer()->get('request_stack')->getCurrentRequest();

		// Show logout form
        // Do not redirect if authentication is successful
        if ($token !== null && $token->getUser() instanceof FrontendUser && $token->isAuthenticated())
		{
			$this->import('FrontendUser', 'User');

			$this->Template->logout = true;
			$this->Template->formId = 'tl_logout_' . $this->id;
			$this->Template->slabel = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['logout']);
			$this->Template->loggedInAs = sprintf($GLOBALS['TL_LANG']['MSC']['loggedInAs'], $this->User->username);
			$this->Template->action = $router->generate('contao_frontend_logout');
            $this->Template->targetPath = null;

			if ($this->User->lastLogin > 0)
			{
				/** @var PageModel $objPage */
				global $objPage;

				$this->Template->lastLogin = sprintf($GLOBALS['TL_LANG']['MSC']['lastLogin'][1], \Date::parse($objPage->datimFormat, $this->User->lastLogin));
			}

			return;
		}

		$flashBag = \System::getContainer()->get('session')->getFlashBag();

		if ($flashBag->has($this->strFlashType))
		{
			$this->Template->hasError = true;
			$this->Template->message = $flashBag->get($this->strFlashType)[0];
		}

        $this->Template->targetName = '_target_path';
        $this->Template->targetPath = $request->getRequestUri();

        // Redirect to the last page visited
        if ($this->redirectBack && $session->get('LAST_PAGE_VISITED') != '')
        {
            $this->Template->targetName = '_target_referer';
            $this->Template->targetPath = $session->get('LAST_PAGE_VISITED');
        }
        elseif ($this->jumpTo && ($objTarget = $this->objModel->getRelated('jumpTo')) instanceof PageModel)
        {
            /** @var PageModel $objTarget */
            $this->Template->targetPath = $objTarget->getAbsoluteUrl();
        }

		$this->Template->username = $GLOBALS['TL_LANG']['MSC']['username'];
		$this->Template->password = $GLOBALS['TL_LANG']['MSC']['password'][0];
		$this->Template->action = $router->generate('contao_frontend_login');
		$this->Template->slabel = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['login']);
		$this->Template->value = $session->get(Security::LAST_USERNAME);
		$this->Template->formId = 'tl_login_' . $this->id;
		$this->Template->autologin = ($this->autologin && \Config::get('autologin') > 0);
		$this->Template->autoLabel = $GLOBALS['TL_LANG']['MSC']['autologin'];
	}
}
