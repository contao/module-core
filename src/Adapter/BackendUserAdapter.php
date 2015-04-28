<?php

/**
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Adapter;

/**
 * Provides an adapter for the Contao BackendUser class.
 *
 * @author Yanick Witschi <https://github.com/toflar>
 */
class BackendUserAdapter implements BackendUserAdapterInterface
{
    /**
     * Extend parent getter class and modify some parameters
     *
     * @param string $strKey
     *
     * @return mixed
     */
    public function getValue($strKey)
    {
        return $this->instantiate()->{$strKey};
    }

    /**
     * Redirect to the login screen if authentication fails
     *
     * @return boolean True if the user could be authenticated
     */
    public function authenticate()
    {
        return $this->instantiate()->authenticate();
    }

    /**
     * Check whether the current user has a certain access right
     *
     * @param string $field
     * @param array  $array
     *
     * @return boolean
     */
    public function hasAccess($field, $array)
    {
        return $this->instantiate()->hasAccess($field, $array);
    }

    /**
     * Return true if the current user is allowed to do the current operation on the current page
     *
     * @param integer $int
     * @param array   $row
     *
     * @return boolean
     */
    public function isAllowed($int, $row)
    {
        return $this->instantiate()->isAllowed($int, $row);
    }

    /**
     * Return true if there is at least one allowed excluded field
     *
     * @param string $table
     *
     * @return boolean
     */
    public function canEditFieldsOf($table)
    {
        return $this->instantiate()->canEditFieldsOf($table);
    }

    /**
     * Restore the original numeric file mounts (see #5083)
     */
    public function save()
    {
        $this->instantiate()->save();
    }

    /**
     * Generate the navigation menu and return it as array
     *
     * @param boolean $blnShowAll
     *
     * @return array
     */
    public function navigation($blnShowAll = false)
    {
        return $this->instantiate()->navigation($blnShowAll);
    }

    /**
     * Set an object property
     *
     * @param string $strKey   The property name
     * @param mixed  $varValue The property value
     */
    public function setValue($strKey, $varValue)
    {
        $this->instantiate()->$strKey = $varValue;
    }

    /**
     * Check whether a property is set
     *
     * @param string $strKey The property name
     *
     * @return boolean True if the property is set
     */
    public function hasValue($strKey)
    {
        return isset($this->instantiate()->{$strKey});
    }

    /**
     * Get a string representation of the user
     *
     * @return string The string representation
     */
    public function __toString()
    {
        return (string) $this->instantiate();
    }

    /**
     * Instantiate a new user object (Factory)
     *
     * @return static The object instance
     */
    public function instantiate()
    {
        return \Contao\BackendUser::getInstance();
    }

    /**
     * Return the table name
     *
     * @return string
     */
    public function getTable()
    {
        return $this->instantiate()->getTable();
    }

    /**
     * Return the current record as associative array
     *
     * @return array
     */
    public function getData()
    {
        return $this->instantiate()->getData();
    }

    /**
     * Try to login the current user
     *
     * @return boolean True if the user could be logged in
     */
    public function login()
    {
        return $this->instantiate()->login();
    }

    /**
     * Find a user in the database
     *
     * @param string $strColumn The field name
     * @param mixed  $varValue  The field value
     *
     * @return boolean True if the user was found
     */
    public function findBy($strColumn, $varValue)
    {
        return $this->instantiate()->findBy($strColumn, $varValue);
    }

    /**
     * Remove the authentication cookie and destroy the current session
     *
     * @return boolean True if the user could be logged out
     */
    public function logout()
    {
        return $this->instantiate()->logout();
    }

    /**
     * Return true if the user is member of a particular group
     *
     * @param integer $id The group ID
     *
     * @return boolean True if the user is a member of the group
     */
    public function isMemberOf($id)
    {
        return $this->instantiate()->isMemberOf($id);
    }

    /**
     * Import a library in non-object context
     *
     * @param string  $strClass The class name
     * @param string  $strKey   An optional key to store the object under
     * @param boolean $blnForce If true, existing objects will be overridden
     *
     * @return object The imported object
     */
    public function importStatic($strClass, $strKey = null, $blnForce = false)
    {
        return \Contao\BackendUser::importStatic($strClass, $strKey, $blnForce);
    }

    /**
     * Add a log entry to the database
     *
     * @param string $strText     The log message
     * @param string $strFunction The function name
     * @param string $strCategory The category name
     */
    public function log($strText, $strFunction, $strCategory)
    {
        \Contao\BackendUser::log($strText, $strFunction, $strCategory);
    }

    /**
     * Return the referer URL and optionally encode ampersands
     *
     * @param boolean $blnEncodeAmpersands If true, ampersands will be encoded
     * @param string  $strTable            An optional table name
     *
     * @return string The referer URL
     */
    public function getReferer($blnEncodeAmpersands = false, $strTable = null)
    {
        return \Contao\BackendUser::getReferer($blnEncodeAmpersands, $strTable);
    }

    /**
     * Load a set of language files
     *
     * @param string  $strName     The table name
     * @param boolean $strLanguage An optional language code
     * @param boolean $blnNoCache  If true, the cache will be bypassed
     */
    public function loadLanguageFile($strName, $strLanguage = null, $blnNoCache = false)
    {
        \Contao\BackendUser::loadLanguageFile($strName, $strLanguage, $blnNoCache);
    }

    /**
     * Check whether a language is installed
     *
     * @param boolean $strLanguage The language code
     *
     * @return boolean True if the language is installed
     */
    public function isInstalledLanguage($strLanguage)
    {
        return \Contao\BackendUser::isInstalledLanguage($strLanguage);
    }

    /**
     * Return the countries as array
     *
     * @return array An array of country names
     */
    public function getCountries()
    {
        return \Contao\BackendUser::getCountries();
    }

    /**
     * Return the available languages as array
     *
     * @param boolean $blnInstalledOnly If true, return only installed languages
     *
     * @return array An array of languages
     */
    public function getLanguages($blnInstalledOnly = false)
    {
        return \Contao\BackendUser::getLanguages($blnInstalledOnly);
    }

    /**
     * Return the timezones as array
     *
     * @return array An array of timezones
     */
    public function getTimeZones()
    {
        return \Contao\BackendUser::getTimeZones();
    }

    /**
     * Return all image sizes as array
     *
     * @return array The available image sizes
     */
    public function getImageSizes()
    {
        return \Contao\BackendUser::getImageSizes();
    }

    /**
     * Urlencode a file path preserving slashes
     *
     * @param string $strPath The file path
     *
     * @return string The encoded file path
     */
    public function urlEncode($strPath)
    {
        return \Contao\BackendUser::urlEncode($strPath);
    }

    /**
     * Set a cookie
     *
     * @param string  $strName     The cookie name
     * @param mixed   $varValue    The cookie value
     * @param integer $intExpires  The expiration date
     * @param string  $strPath     An optional path
     * @param string  $strDomain   An optional domain name
     * @param boolean $blnSecure   If true, the secure flag will be set
     * @param boolean $blnHttpOnly If true, the http-only flag will be set
     */
    public function setCookie($strName, $varValue, $intExpires, $strPath = null, $strDomain = null, $blnSecure = false, $blnHttpOnly = false)
    {
        \Contao\BackendUser::setCookie($strName, $varValue, $intExpires, $strPath, $strDomain, $blnSecure, $blnHttpOnly);
    }

    /**
     * Convert a byte value into a human readable format
     *
     * @param integer $intSize     The size in bytes
     * @param integer $intDecimals The number of decimals to show
     *
     * @return string The human readable size
     */
    public function getReadableSize($intSize, $intDecimals = 1)
    {
        return \Contao\BackendUser::getReadableSize($intSize, $intDecimals);
    }

    /**
     * Format a number
     *
     * @param mixed   $varNumber   An integer or float number
     * @param integer $intDecimals The number of decimals to show
     *
     * @return mixed The formatted number
     */
    public function getFormattedNumber($varNumber, $intDecimals = 2)
    {
        return \Contao\BackendUser::getFormattedNumber($varNumber, $intDecimals);
    }

    /**
     * Return the session hash
     *
     * @param string $strCookie The cookie name
     *
     * @return string The session hash
     */
    public function getSessionHash($strCookie)
    {
        return \Contao\BackendUser::getSessionHash($strCookie);
    }

    /**
     * Anonymize an IP address by overriding the last chunk
     *
     * @param string $strIp The IP address
     *
     * @return string The encoded IP address
     */
    public function anonymizeIp($strIp)
    {
        return \Contao\BackendUser::anonymizeIp($strIp);
    }

    /**
     * Convert an .xlf file into a PHP language file
     *
     * @param string  $strName     The name of the .xlf file
     * @param string  $strLanguage The language code
     * @param boolean $blnLoad     Add the labels to the global language array
     *
     * @return string The PHP code
     *
     * @deprecated Deprecated since version 4.0, to be removed in 5.0. Use the Contao\CoreBundle\Config\Loader\XliffFileLoader instead.
     */
    public function convertXlfToPhp($strName, $strLanguage, $blnLoad = false)
    {
        return \Contao\BackendUser::convertXlfToPhp($strName, $strLanguage, $blnLoad);
    }

    /**
     * Parse a date format string and translate textual representations
     *
     * @param string  $strFormat The date format string
     * @param integer $intTstamp An optional timestamp
     *
     * @return string The textual representation of the date
     *
     * @deprecated Use Date::parse() instead
     */
    public function parseDate($strFormat, $intTstamp = null)
    {
        return \Contao\BackendUser::parseDate($strFormat, $intTstamp);
    }

    /**
     * Add a request string to the current URL
     *
     * @param string $strRequest The string to be added
     *
     * @return string The new URL
     *
     * @deprecated Use Controller::addToUrl() instead
     */
    public function addToUrl($strRequest)
    {
        return \Contao\BackendUser::addToUrl($strRequest);
    }

    /**
     * Reload the current page
     *
     * @deprecated Use Controller::reload() instead
     */
    public function reload()
    {
        \Contao\BackendUser::reload();
    }

    /**
     * Redirect to another page
     *
     * @param string  $strLocation The target URL
     * @param integer $intStatus   The HTTP status code (defaults to 303)
     *
     * @deprecated Use Controller::redirect() instead
     */
    public function redirect($strLocation, $intStatus = 303)
    {
        \Contao\BackendUser::redirect($strLocation, $intStatus);
    }

    /**
     * Split a friendly-name e-address and return name and e-mail as array
     *
     * @param string $strEmail A friendly-name e-mail address
     *
     * @return array An array with name and e-mail address
     *
     * @deprecated Use String::splitFriendlyEmail() instead
     */
    public function splitFriendlyName($strEmail)
    {
        return \Contao\BackendUser::splitFriendlyName($strEmail);
    }

    /**
     * Return the request string without the script name
     *
     * @param boolean $blnAmpersand If true, ampersands will be encoded
     *
     * @return string The request string
     *
     * @deprecated Use Environment::get('indexFreeRequest') instead
     */
    public function getIndexFreeRequest($blnAmpersand = true)
    {
        return \Contao\BackendUser::getIndexFreeRequest($blnAmpersand);
    }

    /**
     * Compile a Model class name from a table name (e.g. tl_form_field becomes FormFieldModel)
     *
     * @param string $strTable The table name
     *
     * @return string The model class name
     *
     * @deprecated Use Model::getClassFromTable() instead
     */
    public function getModelClassFromTable($strTable)
    {
        return \Contao\BackendUser::getModelClassFromTable($strTable);
    }

    /**
     * Enable a back end module
     *
     * @param string $strName The module name
     *
     * @return boolean True if the module was enabled
     *
     * @deprecated Use Composer to add or remove modules
     */
    public function enableModule($strName)
    {
        \Contao\BackendUser::enableModule($strName);
    }

    /**
     * Disable a back end module
     *
     * @param string $strName The module name
     *
     * @return boolean True if the module was disabled
     *
     * @deprecated Use Composer to add or remove modules
     */
    public function disableModule($strName)
    {
        \Contao\BackendUser::disableModule($strName);
    }
}
