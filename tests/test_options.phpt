--TEST--
Test for Services_ReCaptcha options related methods.
--FILE--
<?php

require_once 'Services/ReCaptcha.php';

$recaptcha = new Services_ReCaptcha('public_key', 'private_key');
$recaptcha->setOption('theme', 'white');
$recaptcha->setOptions(array('lang' => 'fr', 'foo' => 'bar'));
echo $recaptcha->getOption('lang') . "\n";
echo $recaptcha->getOption('foo')  . "\n";
print_r($recaptcha->getOptions());

$recaptcha = new Services_ReCaptcha('public_key', 'private_key', array(
    'secure'              => true,
    'xhtml'               => false,
    'theme'               => 'white',
    'lang'                => 'fr',
    'custom_translations' => array('instructions_visual' => "This is my text:"),
    'foo'                 => 'bar',
));
print_r($recaptcha->getOptions());

?>
--EXPECT--
fr

Array
(
    [secure] => 
    [xhtml] => 1
    [theme] => white
    [lang] => fr
    [custom_translations] => 
    [custom_theme_widget] => 
    [tabindex] => 
)
Array
(
    [secure] => 1
    [xhtml] => 
    [theme] => white
    [lang] => fr
    [custom_translations] => Array
        (
            [instructions_visual] => This is my text:
        )

    [custom_theme_widget] => 
    [tabindex] => 
)
