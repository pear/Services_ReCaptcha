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
require_once 'Services/ReCaptcha.php';

// you must get your API keys here:
// http://recaptcha.net/api/getkey
$publicKey  = 'your_public_key';
$privateKey = 'your_private_key';

// we instanciate our Services_ReCaptcha instance with the public key and the 
// private key
$recaptcha = new Services_ReCaptcha($publicKey, $privateKey);

// if the form was submitted and the catpcha challenge response is ok, we 
// display a message and exit
if (isset($_POST['submit']) && $recaptcha->validate()) {
    echo "Challenge response ok !";
    exit(0);
}

// we display the html form
?>
<html>
<head>
    <title>recaptcha test</title>
</head>
<body>
    <form method="post" action="">
<?php echo $recaptcha; ?>
        <hr/>
        <input type="submit" name="submit" value="Ok"/>
    </form>
</body>
</html>
