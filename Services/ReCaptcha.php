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
 * @link      http://recaptcha.net/apidocs/captcha/client.html
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * Dependencies.
 */
require_once 'Services/ReCaptcha/Base.php';
require_once 'HTTP/Request2.php';
 
/**
 * PHP5 interface to the reCATCHA API.
 *
 * reCAPTCHA is a freely available CAPTCHA implementation. It distinguishes 
 * humans from computers.
 *
 * In order to use reCAPTCHA, you need a public/private API key pair. This key 
 * pair helps to prevent an attack where somebody hosts a reCAPTCHA on their 
 * website, collects answers from their visitors and submits the answers to 
 * your site. You can sign up for a key on the
 * {@link http://recaptcha.net/api/getkey reCAPTCHA Administration Portal}
 *
 * @category  Services
 * @package   Services_ReCaptcha
 * @author    David Jean Louis <izi@php.net>
 * @copyright 2008-2009 David Jean Louis
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Services_ReCaptcha
 * @link      http://recaptcha.net/apidocs/captcha/client.html
 * @since     Class available since release 0.1.0
 * @example   examples/example-01.php Services_ReCaptcha simple example
 * @example   examples/example-02.php Services_ReCaptcha advanced example
 */
class Services_ReCaptcha extends Services_ReCaptcha_Base
{
    // properties {{{

    /**
     * The reCAPTCHA API URL.
     *
     * @var string $apiURL
     */
    public $apiURL = 'http://api.recaptcha.net';

    /**
     * The reCAPTCHA API secure URL, this is URL is used by default when the 
     * script is running on an https host or when you force it via the 'secure' 
     * option.
     *
     * @var string $apiSecureURL
     */
    public $apiSecureURL = 'https://api-secure.recaptcha.net';

    /**
     * Url of the ReCaptcha verify API.
     *
     * @var string $apiVerifyURL
     */
    public $apiVerifyURL = 'http://api-verify.recaptcha.net/verify';

    /**
     * The error code used to display the error message in the captcha.
     *
     * @var string $error
     * @see Services_ReCaptcha::getError()
     * @see Services_ReCaptcha::setError()
     */
    protected $error;

    /**
     * Options to customize the html, the url and the look and feel of the captcha.
     * Available options are:
     *   - secure: whether to force the ssl url (default: false);
     *   - xhtml: whether the html should be xhtml compliant (default: true);
     *   - theme: string, defines which theme to use for reCAPTCHA (default: red);
     *   - lang: string, one of the supported language codes (default: en);
     *   - custom_translations: array, se this to specify custom translations 
     *     of reCAPTCHA strings (default: null).
     *   - custom_theme_widget: string, the ID of a DOM element (default: null);
     *   - tabindex: integer, the tabindex for the reCAPTCHA text area.
     * 
     * For a full documentation of theses options please consult the  
     * {@link http://recaptcha.net/apidocs/captcha/client.html API documentation}.
     *
     * @var array $options
     * @see Services_ReCaptcha_Base::getOption()
     * @see Services_ReCaptcha_Base::setOption()
     * @see Services_ReCaptcha_Base::getOptions()
     * @see Services_ReCaptcha_Base::setOptions()
     */
    protected $options = array(
        'secure'              => false,
        'xhtml'               => true,
        'theme'               => null,
        'lang'                => null,
        'custom_translations' => null,
        'custom_theme_widget' => null,
        'tabindex'            => null,
    );

    /**
     * The HTTP_Request2 instance.
     *
     * You can customize the request if you need to (ie: if you use a proxy)
     * with the get/setRequest() methods, for example:
     *
     * <code>
     * require_once 'Services/ReCaptcha.php';
     *
     * $recaptcha = new Services_ReCaptcha('pubkey', 'privkey');
     * $recaptcha->getRequest()->setConfig(array(
     *     'proxy_host' => 'localhost',
     *     'proxy_port' => 8118,
     * ));
     * </code>
     *
     * @var HTTP_Request2 $request
     * @see Services_ReCaptcha::getRequest()
     * @see Services_ReCaptcha::setRequest()
     */
    protected $request;

    // }}}
    // __construct() {{{
    
    /**
     * Constructor, you must pass a valid public and private API key.
     *
     * @param string $pubKey  The public API key (mandatory)
     * @param string $privKey The private API key (mandatory)
     * @param array  $options An array of options (optional)
     * 
     * @see Services_ReCaptcha::$options
     * @return void
     */
    public function __construct($pubKey, $privKey, array $options = array()) 
    {
        parent::__construct($pubKey, $privKey, $options);
    }
    
    // }}}
    // getURL() {{{
    
    /**
     * Returns the URL of the script or iframe src attribute.
     *
     * @param string $path Set this to "noscript" to get the iframe URL instead 
     *                     of the script URL
     *
     * @return string The URL of the script or iframe src attribute
     */
    public function getURL($path = 'challenge')
    {
        // in order to avoid getting browser warnings, if we have an SSL web 
        // site we use the secure url
        if ($this->options['secure'] 
            || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
        ) {
            $url = $this->apiSecureURL;
        } else {
            $url = $this->apiURL;
        }

        $url .= '/' . $path . '?k=' . $this->apiPublicKey;
        if ($this->error !== null) {
            $url .= '&error=' . urlencode($this->error);
        }
        return $url;
    }
    
    // }}}
    // getHTML() {{{
    
    /**
     * Returns the HTML code to insert into your form.
     *
     * Instead of this method you can use the __toString() magic method to get 
     * the HTML code, for example:
     *
     * <code>
     * require_once 'Services/ReCaptcha.php';
     *
     * $recaptcha = new Services_ReCaptcha('pubkey', 'privkey');
     * // both are equivalents:
     * $html = $recaptcha->getHTML();
     * $html = (string) $recaptcha;
     * </code>
     *
     * @return string The HTML to include in your webpage
     * @see Services_ReCaptcha_Base::__toString()
     */
    public function getHTML()
    {
        $return = '';
        // we use array_slice to get only look and feel options, and
        // array_filter to filter null keys
        $options = array_filter(array_slice($this->options, 2));

        // whether to be xhtml or html compliant
        if ($this->options['xhtml']) {
            $br  = '<br/>';
            $end = '/>';
        } else {
            $br  = '<br>';
            $end = '>';
        }

        if (!empty($options)) {
            $opts    = json_encode($options);
            $return .= <<<HTML
<script type="text/javascript">
    var RecaptchaOptions = $opts;
</script>

HTML;
        }
        $scriptURL = $this->getURL();
        $iframeURL = $this->getURL('noscript');
        $return   .= <<<HTML
<script type="text/javascript" src="{$scriptURL}"></script>
<noscript>
    <iframe src="{$iframeURL}" height="300" width="500" frameborder="0">
    </iframe>
    $br
    <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
    <input type="hidden" name="recaptcha_response_field" value="manual_challenge"$end
</noscript>

HTML;
        return $return;
    }
    
    // }}}
    // validate() {{{
    
    /**
     * Validates the challenge response typed by the user and returns true if 
     * the challenge response is ok or false otherwise.
     *
     * You can explicitely pass the challenge, response and ip values if for 
     * some reason you need to do so.
     *
     * @param string $challenge Value of the challenge field, if not given, the
     *                          method will look for the value in $_POST array
     * @param string $response  Value of the response field, if not given, the
     *                          method will look for the value in $_POST array
     * @param int    $ip        The user IP address, if not given, the method 
     *                          will use $_SERVER['REMOTE_ADDR']
     *
     * @return bool Whether the challenge response is valid or not
     * @throws Services_ReCaptcha_Exception When the request cannot be sent
     * @throws Services_ReCaptcha_HTTPException When the server returns an 
     *                                          error response
     */
    public function validate($challenge = null, $response = null, $ip = null)
    {
        if ($challenge === null && isset($_POST['recaptcha_challenge_field'])) {
            $challenge = $_POST['recaptcha_challenge_field'];
        }
        if ($response === null && isset($_POST['recaptcha_response_field'])) {
            $response = $_POST['recaptcha_response_field'];
        }
        if ($ip === null && isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        try {
            $request = clone $this->getRequest();
            $request->setMethod(HTTP_Request2::METHOD_POST);
            $request->setUrl($this->apiVerifyURL);
            $params = array(
                'privatekey' => $this->apiPrivateKey,
                'remoteip'   => $ip,
                'challenge'  => $challenge,
                'response'   => $response,
            );
            $request->addPostParameter($params);
            $httpResponse = $request->send();
        } catch (Exception $exc) {
            throw new Services_ReCaptcha_Exception(
                $exc->getMessage(),
                $exc
            );
        }

        if ($httpResponse->getStatus() != 200) {
            throw new Services_ReCaptcha_HTTPException(
                $httpResponse->getReasonPhrase(),
                $httpResponse->getStatus(),
                $httpResponse
            );
        }

        $body = explode("\n", $httpResponse->getBody());
        if (count($body) != 2) {
            $this->setError('unknown');
            return false;
        }
        if ($body[0] != 'true') {
            $this->setError($body[1]);
            return false;
        }

        return true;
    }
    
    // }}}
    // getError() {{{

    /**
     * Returns the current error code.
     *
     * @return string The error code
     * @see Services_ReCaptcha::$error
     */
    public function getError()
    {
        return $this->error;
    }

    // }}}
    // setError() {{{

    /**
     * Sets the error code to display in the captcha and returns the current 
     * Services_ReCaptcha instance.
     *
     * @param string $error The error message
     *
     * @return Services_ReCaptcha
     * @see Services_ReCaptcha::$error
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    // }}}
    // getRequest() {{{
    
    /**
     * Returns the HTTP_Request2 instance, if it's not yet set it is 
     * instanciated on the fly.
     * 
     * @return HTTP_Request2 The request instance
     * @see Services_ReCaptcha::$request
     */
    public function getRequest()
    {
        if (!$this->request instanceof HTTP_Request2) {
            $this->request = new HTTP_Request2();
        }
        return $this->request;
    }
    
    // }}}
    // setRequest() {{{
    
    /**
     * Sets the HTTP_Request2 instance and returns the current 
     * Services_ReCaptcha instance.
     * 
     * @param HTTP_Request2 $request The request to set
     *
     * @return Services_ReCaptcha
     * @see Services_ReCaptcha::$request
     */
    public function setRequest(HTTP_Request2 $request)
    {
        $this->request = $request;
        return $this;
    }
    
    // }}}
}
