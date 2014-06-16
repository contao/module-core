<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Library
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Contao;

use Exception;


/**
 * Stores and outputs messages
 *
 * The class handles system messages which are shown to the user. You can add
 * messages from anywhere in the application.
 *
 * Usage:
 *
 *     Message::addError('Please enter your name');
 *     Message::addConfirmation('The data has been stored');
 *     Message::addNew('There are two new messages');
 *     Message::addInfo('You can upload only two files');
 *
 * @package   Library
 * @author    Leo Feyer <https://github.com/leofeyer>
 * @copyright Leo Feyer 2005-2014
 */
class Message
{

	/**
	 * Add an error message
	 *
	 * @param string $strMessage The error message
	 */
	public static function addError($strMessage)
	{
		static::add($strMessage, 'TL_ERROR');
	}


	/**
	 * Add a confirmation message
	 *
	 * @param string $strMessage The confirmation message
	 */
	public static function addConfirmation($strMessage)
	{
		static::add($strMessage, 'TL_CONFIRM');
	}


	/**
	 * Add a new message
	 *
	 * @param string $strMessage The new message
	 */
	public static function addNew($strMessage)
	{
		static::add($strMessage, 'TL_NEW');
	}


	/**
	 * Add an info message
	 *
	 * @param string $strMessage The info message
	 */
	public static function addInfo($strMessage)
	{
		static::add($strMessage, 'TL_INFO');
	}


	/**
	 * Add a preformatted message
	 *
	 * @param string $strMessage The preformatted message
	 */
	public static function addRaw($strMessage)
	{
		static::add($strMessage, 'TL_RAW');
	}


	/**
	 * Add a message
	 *
	 * @param string $strMessage The message text
	 * @param string $strType    The message type
	 *
	 * @throws Exception If $strType is not a valid message type
	 */
	public static function add($strMessage, $strType)
	{
		if ($strMessage == '')
		{
			return;
		}

		if (!in_array($strType, static::getTypes()))
		{
			throw new Exception("Invalid message type $strType");
		}

		if (!is_array($_SESSION[$strType]))
		{
			$_SESSION[$strType] = [];
		}

		$_SESSION[$strType][] = $strMessage;
	}


	/**
	 * Return all messages as HTML
	 *
	 * @param boolean $blnDcLayout If true, the line breaks are different
	 * @param boolean $blnNoWrapper If true, there will be no wrapping DIV
	 *
	 * @return string The messages HTML markup
	 */
	public static function generate($blnDcLayout=false, $blnNoWrapper=false)
	{
		$strMessages = '';

		// Regular messages
		foreach (static::getTypes() as $strType)
		{
			if (!is_array($_SESSION[$strType]))
			{
				continue;
			}

			$strClass = strtolower($strType);
			$_SESSION[$strType] = array_unique($_SESSION[$strType]);

			foreach ($_SESSION[$strType] as $strMessage)
			{
				if ($strType == 'TL_RAW')
				{
					$strMessages .= $strMessage;
				}
				else
				{
					$strMessages .= sprintf('<p class="%s">%s</p>%s', $strClass, $strMessage, "\n");
				}
			}

			if (!$_POST)
			{
				$_SESSION[$strType] = [];
			}
		}

		$strMessages = trim($strMessages);

		// Wrapping container
		if (!$blnNoWrapper && $strMessages != '')
		{
			$strMessages = sprintf('%s<div class="tl_message">%s%s%s</div>%s', ($blnDcLayout ? "\n\n" : "\n"), "\n", $strMessages, "\n", ($blnDcLayout ? '' : "\n"));
		}

		return $strMessages;
	}


	/**
	 * Reset the message system
	 */
	public static function reset()
	{
		foreach (static::getTypes() as $strType)
		{
			$_SESSION[$strType] = [];
		}
	}


	/**
	 * Return all available message types
	 *
	 * @return array An array of message types
	 */
	public static function getTypes()
	{
		return ['TL_ERROR', 'TL_CONFIRM', 'TL_NEW', 'TL_INFO', 'TL_RAW'];
	}
}
