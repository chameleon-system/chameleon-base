<div class="cleardiv">&nbsp;</div>
<div id="footer">
    <div class="pull-left">
        <?php
        $oUser = TCMSUser::GetActiveUser();
        if ($oUser) {
            echo  'Chameleon V. '.CMS_VERSION_MAJOR.'.'.CMS_VERSION_MINOR.' (IP: '.$_SERVER['SERVER_ADDR'].')';
        }
        ?>
    </div>
    <div class="pull-right">
        &nbsp;&copy;&nbsp;&nbsp;<a href="http://www.esono.de" target="_blank">ESONO AG</a>&nbsp;&nbsp; <?=date('Y'); ?>
    </div>
</div>
<?php
require_once dirname(__FILE__).'/cms_footer_data.inc.php';
?>