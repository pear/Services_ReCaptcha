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
    $recaptcha->validate('foo', 'bar');
} catch (Services_ReCaptcha_Exception $exc) {
    echo $exc->getMessage() . "\n";
}

try {
    $recaptcha = new Services_ReCaptcha('public_key', 'private_key');
    $recaptcha->apiVerifyURL = 'http://api-verify.recaptcha.net/foo';
    if (!$livetest) {
        $mock = new HTTP_Request2_Adapter_Mock();
        $resp = new HTTP_Request2_Response('HTTP/1.1 404 Not Found', false);
        $mock->addResponse($resp);
        $request = $recaptcha->getRequest();
        $request->setAdapter($mock);
    } else {
        $request = new HTTP_Request2();
    }
    $recaptcha->setRequest($request);
    $recaptcha->validate('foo', 'bar');
} catch (Services_ReCaptcha_HTTPException $exc) {
    echo $exc->getMessage() . "\n";
    echo get_class($exc->response) . "\n";
}

try {
    $recaptcha = new Services_ReCaptcha('public_key', 'private_key');
    $recaptcha->validate();
    echo $recaptcha->getError() . "\n";
    $recaptcha->validate('foo', false);
    echo $recaptcha->getError() . "\n";
    $recaptcha->validate('', 'bar');
    echo $recaptcha->getError();
} catch (Services_ReCaptcha_Exception $exc) {
    echo $exc->getMessage();
}

?>
--EXPECT--
HTTP_Request2 needs an absolute HTTP(S) request URL, 'Some invalid url...' given
Not Found
HTTP_Request2_Response
incorrect-captcha-sol
incorrect-captcha-sol
incorrect-captcha-sol
