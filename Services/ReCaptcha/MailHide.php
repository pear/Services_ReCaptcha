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
 * @link      http://recaptcha.net/apidocs/mailhide/
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * Dependencies.
 */
require_once 'Services/ReCaptcha/Base.php';
 
/**
 * PHP5 interface to the reCATCHA MailHide API.
 *
 * reCAPTCHA Mailhide helps you protect your inbox by asking people to solve a 
 * reCAPTCHA before they can view your email address.
 *
 * To obtain an encryption key, you can go to the
 * {@link http://mailhide.recaptcha.net/apikey key generation service}.
 * This will compute a "public key", which much like a username identifies who
 * you are to the MailHide server, and a "private key" which allows you to
 * encrypt email addresses.
 *
 * @category  Services
 * @package   Services_ReCaptcha
 * @author    David Jean Louis <izi@php.net>
 * @copyright 2008-2009 David Jean Louis
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Services_ReCaptcha
 * @link      http://recaptcha.net/apidocs/mailhide/
 * @since     Class available since release 0.1.0
 * @example   examples/example-03.php Services_ReCaptcha_MailHide example
 */
class Services_ReCaptcha_MailHide extends Services_ReCaptcha_Base
{
    // properties {{{

    /**
     * Url of the reCATCHA MailHide API.
     *
     * @var string $apiURL
     */
    public $apiURL = 'http://mailhide.recaptcha.net/d';

    /**
     * Options to customize the apparence of the generated HTML and the
     * corresponding popup window.
     *
     * Available options are:
     *   - mask_text: string, the chars that will be displayed in the email 
     *     address to mask it;
     *   - link_text: string, the text of the link;
     *   - link_title: string, the title (tooltip) of the link;
     *   - popup_width: integer, the popup width in pixels;
     *   - popup_height: integer, the popup height in pixels.
     * 
     * @var array $options
     * @see Services_ReCaptcha_Base::getOption()
     * @see Services_ReCaptcha_Base::setOption()
     * @see Services_ReCaptcha_Base::getOptions()
     * @see Services_ReCaptcha_Base::setOptions()
     */
    protected $options = array(
        'mask_text'    => '...',
        'link_text'    => null,
        'link_title'   => 'Reveal this e-mail address',
        'popup_width'  => 500,
        'popup_height' => 300,
    );

    /**
     * The email address you want to "hide".
     *
     * @var string $email
     * @see Services_ReCaptcha_MailHide::getEmail()
     * @see Services_ReCaptcha_MailHide::setEmail()
     */
    protected $email;

    // }}}
    // __construct() {{{
    
    /**
     * Constructor, you must pass a valid public and private API key.
     *
     * Additionally, you can pass the email you want to hide, and an array of 
     * options.
     *
     * @param string $pubKey  The public API key (mandatory)
     * @param string $privKey The private API key (mandatory)
     * @param string $email   The email to hide (optional)
     * @param array  $options An array of options (optional)
     * 
     * @return void
     * @see Services_ReCaptcha_MailHide::$email
     * @see Services_ReCaptcha_MailHide::$options
     */
    public function __construct($pubKey, $privKey, $email = null, 
        array $options = array()
    ) {
        // check that mcrypt is available
        if (!extension_loaded('mcrypt')) {
            throw new Services_ReCaptcha_Exception(__CLASS__ . ' requires mcrypt.');
        }
        parent::__construct($pubKey, $privKey, $options);
        if ($email !== null) {
            $this->setEmail($email);
        }
    }
    
    // }}}
    // getURL() {{{
    
    /**
     * Returns the URL of the mailhide popup.
     *
     * @return string The mailhide popup URL
     */
    public function getURL()
    {
        $email = $this->getEmail();

        // aes pad email
        $bsize     = 16;
        $padLength = $bsize - (strlen($email) % $bsize);
        $data      = str_pad($email, strlen($email) + $padLength, chr($padLength));

        // encrypt email
        $cypher   = MCRYPT_RIJNDAEL_128;
        $key      = pack('H*', $this->apiPrivateKey);
        $mode     = MCRYPT_MODE_CBC;
        $yv       = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        $encEmail = mcrypt_encrypt($cypher, $key, $data, $mode, $yv);
        $encEmail = strtr(base64_encode($encEmail), '+/', '-_');

        return $this->apiURL . '?k=' . $this->apiPublicKey . '&c=' . $encEmail;
    }
    
    // }}}
    // getHTML() {{{
    
    /**
     * Returns the HTML code snippet that will hide your email.
     *
     * Instead of this method you can use the __toString() magic method to get 
     * the HTML code, for example:
     *
     * <code>
     * require_once 'Services/ReCaptcha/MailHide.php';
     *
     * $mailhide = new Services_ReCaptcha_MailHide(
     *     'pubkey',
     *     'privkey',
     *     'foo@example.com'
     * );
     * // both are equivalents:
     * $html = $mailhide->getHTML();
     * $html = (string) $mailhide;
     * </code>
     *
     * @return string The HTML code
     * @see Services_ReCaptcha_Base::__toString()
     */
    public function getHTML()
    {
        if ($this->options['link_text'] === null) {
            $emailParts     = explode('@', $this->getEmail(), 2);
            $leftPartLength = strlen($emailParts[0]);
            if ($leftPartLength <= 4) {
                $emailParts[0] = substr($emailParts[0], 0, 1);
            } else if ($leftPartLength <= 6) {
                $emailParts[0] = substr($emailParts[0], 0, 3);
            } else {
                $emailParts[0] = substr($emailParts[0], 0, 4);
            }
            $pre  = htmlentities($emailParts[0]);
            $text = htmlentities($this->options['mask_text']);
            $post = '@' . htmlentities($emailParts[1]);
        } else {
            $pre  = '';
            $text = htmlentities($this->options['link_text']);
            $post = '';
        }
        $url = htmlentities($this->getURL());

        return sprintf(
            '%s<a href="%s" onclick="window.open(\'%s\', \'\', ' .
            '\'toolbar=0,scrollbars=0,location=0,statusbar=0,' .
            'menubar=0,resizable=0,width=%s,height=%s\'); return false;" ' .
            'title="%s">%s</a>%s',
            $pre,
            $url,
            $url, 
            intval($this->getOption('popup_width')),
            intval($this->getOption('popup_height')),
            htmlentities($this->getOption('link_title')),
            $text,
            $post
        );
    }
    
    // }}}
    // getEmail() {{{

    /**
     * Returns the email to "hide".
     *
     * @return string The email to hide
     * @see Services_ReCaptcha_MailHide::$email
     */
    public function getEmail()
    {
        return $this->email;
    }

    // }}}
    // setEmail() {{{

    /**
     * Sets the email to "hide" and returns the current 
     * Services_ReCaptcha_MailHide instance.
     *
     * @param string $email The email to hide
     *
     * @return Services_ReCaptcha_MailHide
     * @see Services_ReCaptcha_MailHide::$email
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    // }}}
}
