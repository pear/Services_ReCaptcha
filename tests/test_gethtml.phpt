--TEST--
Test for Services_ReCaptcha::getHTML() method.
--FILE--
<?php

require_once 'Services/ReCaptcha.php';

$recaptcha = new Services_ReCaptcha('public_key', 'private_key');
echo $recaptcha . "\n";

$recaptcha->setOption('secure', true);
$recaptcha->setOption('theme', 'white');
$recaptcha->setOption('lang', 'fr');
$recaptcha->setOption('custom_translations', array('instructions_visual' => "foo"));
$recaptcha->setError('incorrect-captcha-sol');
echo $recaptcha . "\n";

$recaptcha->setOption('xhtml', false);
echo $recaptcha . "\n";

?>
--EXPECT--
<script type="text/javascript" src="https://www.google.com/recaptcha/api/challenge?k=public_key"></script>
<noscript>
    <iframe src="https://www.google.com/recaptcha/api/noscript?k=public_key" height="300" width="500" frameborder="0">
    </iframe>
    <br/>
    <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
    <input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
</noscript>

<script type="text/javascript">
    var RecaptchaOptions = {"theme":"white","lang":"fr","custom_translations":{"instructions_visual":"foo"}};
</script>
<script type="text/javascript" src="https://www.google.com/recaptcha/api/challenge?k=public_key&error=incorrect-captcha-sol"></script>
<noscript>
    <iframe src="https://www.google.com/recaptcha/api/noscript?k=public_key&error=incorrect-captcha-sol" height="300" width="500" frameborder="0">
    </iframe>
    <br/>
    <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
    <input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
</noscript>

<script type="text/javascript">
    var RecaptchaOptions = {"theme":"white","lang":"fr","custom_translations":{"instructions_visual":"foo"}};
</script>
<script type="text/javascript" src="https://www.google.com/recaptcha/api/challenge?k=public_key&error=incorrect-captcha-sol"></script>
<noscript>
    <iframe src="https://www.google.com/recaptcha/api/noscript?k=public_key&error=incorrect-captcha-sol" height="300" width="500" frameborder="0">
    </iframe>
    <br>
    <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
    <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
</noscript>

