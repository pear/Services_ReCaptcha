--TEST--
Test for Services_ReCaptcha exceptions
--FILE--
<?php

require_once 'Services/ReCaptcha.php';
require_once 'HTTP/Request2/Response.php';
require_once 'HTTP/Request2/Adapter/Mock.php';

$livetest = getenv('SERVICES_RECAPTCHA_LIVETEST');
// tweak globals...
$_SERVER['REMOTE_ADDR']             = '127.0.0.1';
$_POST['recaptcha_challenge_field'] = 'foo';
$_POST['recaptcha_response_field']  = 'bar';

try {
    $recaptcha = new Services_ReCaptcha('public_key', 'private_key');
    $mock = new HTTP_Request2_Adapter_Mock();
    $resp = new HTTP_Request2_Response('HTTP/1.1 200 Ok', false);
    $resp->appendBody("false");
    $mock->addResponse($resp);
    $recaptcha->getRequest()->setAdapter($mock);
    $recaptcha->validate('foo', 'bar', '127.0.0.1');
    echo $recaptcha->getError() . "\n";
} catch (Services_ReCaptcha_HTTPException $exc) {
    echo $exc->getMessage();
}

try {
    $recaptcha = new Services_ReCaptcha('public_key', 'private_key');
    if (!$livetest) {
        $mock = new HTTP_Request2_Adapter_Mock();
        $resp = new HTTP_Request2_Response('HTTP/1.1 200 Ok', false);
        $resp->appendBody("false\n'Input error: challenge: Error parsing "
            . "captcha challenge value\\nprivatekey: Format of site key was "
            . "invalid\\n'");
        $mock->addResponse($resp);
        $recaptcha->getRequest()->setAdapter($mock);
    }
    $recaptcha->validate();
    echo $recaptcha->getError() . "\n";
} catch (Services_ReCaptcha_HTTPException $exc) {
    echo $exc->getMessage();
}

try {
    $recaptcha = new Services_ReCaptcha('public_key', 'private_key');
    $mock = new HTTP_Request2_Adapter_Mock();
    $resp = new HTTP_Request2_Response('HTTP/1.1 200 Ok', false);
    $resp->appendBody("true\nsuccess");
    $mock->addResponse($resp);
    $recaptcha->getRequest()->setAdapter($mock);
    $recaptcha->validate();
    echo $recaptcha->getError();
} catch (Services_ReCaptcha_HTTPException $exc) {
    echo $exc->getMessage();
}

?>
--EXPECT--
unknown
'Input error: challenge: Error parsing captcha challenge value\nprivatekey: Format of site key was invalid\n'

