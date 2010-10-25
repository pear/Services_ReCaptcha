<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of the PEAR Services_ReCaptcha package.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Services
 * @package   Services_ReCaptcha
 * @author    David Jean Louis <izi@php.net>
 * @copyright 2008-2009 David Jean Louis
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Services_ReCaptcha
 * @link      http://www.recaptcha.net
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * Dependencies.
 */
require_once 'Services/ReCaptcha/Exception.php';
 
/**
 * Base class for Services_ReCaptcha and Services_ReCaptcha_MailHide.
 *
 * @category  Services
 * @package   Services_ReCaptcha
 * @author    David Jean Louis <izi@php.net>
 * @copyright 2008-2009 David Jean Louis
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Services_ReCaptcha
 * @link      http://www.recaptcha.net
 * @since     Class available since release 0.1.0
 */
abstract class Services_ReCaptcha_Base
{
    // properties {{{

    /**
     * reCAPTCHA/reCAPTCHA Mailhide API URL.
     *
     * @var string $apiURL
     */
    public $apiURL;

    /**
     * reCAPTCHA/reCAPTCHA Mailhide API public key.
     *
     * @var string $apiPublicKey
     */
    protected $apiPublicKey;

    /**
     * reCAPTCHA/reCAPTCHA Mailhide API private key.
     *
     * @var string $apiPrivateKey
     */
    protected $apiPrivateKey;

    /**
     * Array of options.
     *
     * @var array $options
     */
    protected $options = array();

    // }}}
    // __construct() {{{
    
    /**
     * Constructor, you must pass a valid public and private API key, and 
     * optionnaly an array of options.
     *
     * @param string $pubKey  The public API key (mandatory)
     * @param string $privKey The private API key (mandatory)
     * @param array  $options An array of options (optional)
     * 
     * @return void
     */
    public function __construct($pubKey, $privKey, array $options = array())
    {
        $this->apiPublicKey  = $pubKey;
        $this->apiPrivateKey = $privKey;
        if (!empty($options)) {
            $this->setOptions($options);
        }
    }
    
    // }}}
    // __toString() {{{
    
    /**
     * Returns the corresponding HTML code, this is a "magic" shortcut to the
     * getHTML() method.
     *
     * @return string The html code
     * @see Services_ReCaptcha_Base::getHTML()
     */
    public function __toString()
    {
        try {
            return $this->getHTML();
        } catch (Services_ReCaptcha_Exception $exc) {
            trigger_error($exc->getMessage(), E_USER_ERROR);
        }
    }
    
    // }}}
    // getHTML() {{{

    /**
     * Returns the corresponding HTML code.
     *
     * @return string
     */
    abstract public function getHTML();

    // }}}
    // getOption() {{{

    /**
     * Returns an option from {@link Services_ReCaptcha::$options}.
     *
     * @param string $option Name of option
     *
     * @return mixed The value of the option
     */
    public function getOption($option)
    {
        if (array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }
    }

    // }}}
    // setOption() {{{

    /**
     * Sets an option in {@link Services_ReCaptcha::$options} and returns the
     * current Services_ReCaptcha_Base instance.
     *
     * @param string $option Name of option
     * @param mixed  $value  Value of option
     *
     * @return Services_ReCaptcha_Base
     */
    public function setOption($option, $value)
    {
        if (array_key_exists($option, $this->options)) {
            $this->options[$option] = $value;
        }
        return $this;
    }

    // }}}
    // getOptions() {{{

    /**
     * Returns the {@link Services_ReCaptcha::$options} array.
     *
     * @return array The options array
     */
    public function getOptions()
    {
        return $this->options;
    }

    // }}}
    // setOptions() {{{

    /**
     * Sets a number of options at once in {@link Services_ReCaptcha::$options}
     * and returns the current Services_ReCaptcha_Base instance.
     *
     * @param array $options Associative array of options name/value
     *
     * @return Services_ReCaptcha_Base
     * @see Services_ReCaptcha::setOption()
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
        return $this;
    }

    // }}}
}
