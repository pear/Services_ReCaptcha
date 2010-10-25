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
 * Include the Services_ReCaptcha class
 */
require_once 'Services/ReCaptcha/MailHide.php';

// you must generate your API keys here:
// http://mailhide.recaptcha.net/apikey
$publicKey  = 'your_public_key';
$privateKey = 'your_private_key';

// we instanciate our Services_ReCaptcha_MailHide instance with the public key
// and the private key
$mailhide1 = new Services_ReCaptcha_MailHide(
    $publicKey,
    $privateKey,
    'johndoe@example.com'
);

$mailhide2 = new Services_ReCaptcha_MailHide(
    $publicKey,
    $privateKey,
    'johndoe@example.com',
    array('link_text' => 'John Doe')
);

$mailhide3 = new Services_ReCaptcha_MailHide(
    $publicKey,
    $privateKey,
    'johndoe@example.com'
);
$mailhide3->setOptions(
    array(
        'link_text'    => 'Click here to display my email',
        'link_title'   => 'Some help message',
        'link_title'   => 'Some help message',
        'popup_width'  => 800,
        'popup_height' => 600,
    )
);

?>
<html>
<head>
    <title>recaptcha test</title>
</head>
<body>
    <h2>Hidden emails can be displayed like this:</h2>
    <p><?php echo $mailhide1 ?></p>
    <h2>Like this:</h2>
    <p><?php echo $mailhide2 ?></p>
    <h2>And even like this:</h2>
    <p><?php echo $mailhide3 ?></p>
</body>
</html>
