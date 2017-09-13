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
use Symfony\Component\Routing\RouterInterface;

@trigger_error('Using the logout module has been deprecated and will no longer work in Contao 5.0. Use the logout page instead.', E_USER_DEPRECATED);


/**
 * Front end module "logout".
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 *
 * @deprecated Deprecated since Contao 4.2, to be removed in Contao 5.0.
 *             Use the logout page instead.
 */
class ModuleLogout extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate;


	/**
	 * Logout the current user and redirect
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			/** @var BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['logout'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		/** @var RouterInterface $router */
		$router = System::getContainer()->get('router');
		$session = System::getContainer()->get('session');

		$strRedirect = \Environment::get('base');

		// Set last page visited
		if ($this->redirectBack && $this->getReferer())
		{
			$strRedirect = $this->getReferer();
		}

		// Redirect to jumpTo page
		elseif ($this->jumpTo && ($objTarget = $this->getRelated('jumpTo')) instanceof PageModel)
		{
			/** @var PageModel $objTarget */
			$strRedirect = $objTarget->getAbsoluteUrl();
		}

		$session->set('_contao_logout_target', $strRedirect);

		// TODO: fix/replace me
		// $this->User->logout();

		$this->redirect($router->generate('contao_frontend_logout'));
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		return;
	}
}
