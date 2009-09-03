--TEST--
Test for Services_ReCaptcha exceptions
--FILE--
<?php

require_once 'Services/ReCaptcha.php';
require_once 'HTTP/Request2/Response.php';
require_once 'HTTP/Request2/Adapter/Mock.php';

$livetest = getenv('SERVICES_RECAPTCHA_LIVETEST');

try {
    $recaptcha = new Services_ReCaptcha('public_key', 'private_key');
    $recaptcha->apiVerifyURL = 'Some invalid url...';
    $recaptcha->validate();
} catch (Services_ReCaptcha_Exception $exc) {
    echo $exc->getMessage() . "\n";
}

try {
    $recaptcha = new Services_ReCaptcha('public_key', 'private_key');
    $recaptcha->apiVerifyURL = 'http://api-verify.recaptcha.net/foo';
    if (!$livetest) {
        $mock = new HTTP_Request2_Adapter_Mock();
        $resp = new HTTP_Request2_Response('HTTP/1.1 405 Not Allowed', false);
        $mock->addResponse($resp);
        $request = $recaptcha->getRequest();
        $request->setAdapter($mock);
    } else {
        $request = new HTTP_Request2();
    }
    $recaptcha->setRequest($request);
    $recaptcha->validate();
} catch (Services_ReCaptcha_HTTPException $exc) {
    echo $exc->getMessage() . "\n";
    echo get_class($exc->response);
}

?>
--EXPECT--
Absolute URL required
Not Allowed
HTTP_Request2_Response
