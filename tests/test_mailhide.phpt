--TEST--
Test for Services_ReCaptcha_MailHide class.
--SKIPIF--
<?php
if (!extension_loaded('mcrypt')) {
    die("skip mcrypt is not installed");
}
?>
--FILE--
<?php

require_once 'Services/ReCaptcha/MailHide.php';

$mailhide = new Services_ReCaptcha_MailHide(
    '01PGfQx2CkkYbD2Zk_PfnCUQ==',
    '7A2CA16B1C77BD7F8F125D52E5818A06',
    'joh@example.com'
);
echo $mailhide->getHTML() . "\n";

$mailhide = new Services_ReCaptcha_MailHide(
    '01PGfQx2CkkYbD2Zk_PfnCUQ==',
    '7A2CA16B1C77BD7F8F125D52E5818A06',
    'johnd@example.com'
);
echo $mailhide->getHTML() . "\n";

$mailhide = new Services_ReCaptcha_MailHide(
    '01PGfQx2CkkYbD2Zk_PfnCUQ==',
    '7A2CA16B1C77BD7F8F125D52E5818A06',
    'johndoe@example.com'
);
$mailhide->setOption('popup_width', '550');
$mailhide->setOption('popup_height', '350');
$mailhide->setOption('mask_text', '$$$$$$$$$');
$mailhide->setOption('link_title', 'Foo, """ \'\'\' bar, baz');
echo $mailhide->getHTML() . "\n";


$mailhide = new Services_ReCaptcha_MailHide(
    '01PGfQx2CkkYbD2Zk_PfnCUQ==',
    '7A2CA16B1C77BD7F8F125D52E5818A06',
    'johndoe@example.com',
    array('link_text' => 'John Doe')
);
echo $mailhide->getEmail() . "\n";
echo $mailhide->getHTML() . "\n";

?>
--EXPECT--
j<a href="http://mailhide.recaptcha.net/d?k=01PGfQx2CkkYbD2Zk_PfnCUQ==&amp;c=XRjcV59M9SEvwI_bUX1hwQ==" onclick="window.open('http://mailhide.recaptcha.net/d?k=01PGfQx2CkkYbD2Zk_PfnCUQ==&amp;c=XRjcV59M9SEvwI_bUX1hwQ==', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;" title="Reveal this e-mail address">...</a>@example.com
joh<a href="http://mailhide.recaptcha.net/d?k=01PGfQx2CkkYbD2Zk_PfnCUQ==&amp;c=fRbQwuOHuAonTurVc_aOqNOa_H5UQF7HNS3EEwwDqls=" onclick="window.open('http://mailhide.recaptcha.net/d?k=01PGfQx2CkkYbD2Zk_PfnCUQ==&amp;c=fRbQwuOHuAonTurVc_aOqNOa_H5UQF7HNS3EEwwDqls=', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;" title="Reveal this e-mail address">...</a>@example.com
john<a href="http://mailhide.recaptcha.net/d?k=01PGfQx2CkkYbD2Zk_PfnCUQ==&amp;c=oDkXz3nXyBro55WBTPi2NsNbpS-XwOVppuKoy8bRvoI=" onclick="window.open('http://mailhide.recaptcha.net/d?k=01PGfQx2CkkYbD2Zk_PfnCUQ==&amp;c=oDkXz3nXyBro55WBTPi2NsNbpS-XwOVppuKoy8bRvoI=', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=550,height=350'); return false;" title="Foo, &quot;&quot;&quot; ''' bar, baz">$$$$$$$$$</a>@example.com
johndoe@example.com
<a href="http://mailhide.recaptcha.net/d?k=01PGfQx2CkkYbD2Zk_PfnCUQ==&amp;c=oDkXz3nXyBro55WBTPi2NsNbpS-XwOVppuKoy8bRvoI=" onclick="window.open('http://mailhide.recaptcha.net/d?k=01PGfQx2CkkYbD2Zk_PfnCUQ==&amp;c=oDkXz3nXyBro55WBTPi2NsNbpS-XwOVppuKoy8bRvoI=', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;" title="Reveal this e-mail address">John Doe</a>
