<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


/**
 * Handle back end logins and logouts.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class BackendIndex extends \Backend
{
	/** @var ContainerInterface $container */
	protected $container;

	/** @var FlashBagInterface $flashBag */
	protected $flashBag;

	/**
	 * Initialize the controller
	 *
	 * 1. Import the user
	 * 2. Call the parent constructor
	 * 3. Login the user
	 * 4. Load the language files
	 * DO NOT CHANGE THIS ORDER!
	 */
	public function __construct()
	{
		$this->container = System::getContainer();
		$this->flashBag = $this->container->get('session')->getFlashBag();

		$this->import('BackendUser', 'User');
		parent::__construct();

		$this->User->authenticate();

		\System::loadLanguageFile('default');
		\System::loadLanguageFile('tl_user');
	}


	/**
	 * Run the controller and parse the login template
	 *
	 * @return Response
	 */
	public function run()
	{
		/** @var BackendTemplate|object $objTemplate */
		$objTemplate = new \BackendTemplate('be_login');

		$strHeadline = sprintf($GLOBALS['TL_LANG']['MSC']['loginTo'], \Config::get('websiteTitle'));

		$objTemplate->theme = \Backend::getTheme();
		$objTemplate->base = \Environment::get('base');
		$objTemplate->language = $GLOBALS['TL_LANGUAGE'];
		$objTemplate->languages = \System::getLanguages(true);
		$objTemplate->title = \StringUtil::specialchars($strHeadline);
		$objTemplate->charset = \Config::get('characterSet');
		$objTemplate->action = ampersand(\Environment::get('request'));
		$objTemplate->userLanguage = $GLOBALS['TL_LANG']['tl_user']['language'][0];
		$objTemplate->headline = $strHeadline;
		$objTemplate->curLanguage = \Input::post('language') ?: str_replace('-', '_', $GLOBALS['TL_LANGUAGE']);
		$objTemplate->curUsername = \Input::post('username') ?: '';
		$objTemplate->uClass = ($_POST && empty($_POST['username'])) ? ' class="login_error"' : '';
		$objTemplate->pClass = ($_POST && empty($_POST['password'])) ? ' class="login_error"' : '';
		$objTemplate->loginButton = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['loginBT']);
		$objTemplate->username = $GLOBALS['TL_LANG']['tl_user']['username'][0];
		$objTemplate->password = $GLOBALS['TL_LANG']['MSC']['password'][0];
		$objTemplate->feLink = $GLOBALS['TL_LANG']['MSC']['feLink'];
		$objTemplate->default = $GLOBALS['TL_LANG']['MSC']['default'];
		$objTemplate->jsDisabled = $GLOBALS['TL_LANG']['MSC']['jsDisabled'];

		if ($this->flashBag->has('be_login')) {
			$flashes = $this->flashBag->get('be_login');

			$objTemplate->message = $flashes[0];
		}

		return $objTemplate->getResponse();
	}
}
