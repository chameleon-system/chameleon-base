<?php
if (!empty($sPayload)) {
    ?>
<img
    src="<?= PATH_CUSTOMER_FRAMEWORK_CONTROLLER; ?>?pg=<?=TGlobal::OutHTML(urlencode($sPayload)); ?>&amp;trackviews=1&amp;rnd=<?=md5(rand(1000000, 9000000)); ?>"
    alt="tracking" width="1" height="1"/>
<?php
}
