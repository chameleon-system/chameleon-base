<footer class="footer justify-content-between">
    <div>
        <span><?php
            echo  'Chameleon V. '.CMS_VERSION_MAJOR.'.'.CMS_VERSION_MINOR.' (IP: '.$_SERVER['SERVER_ADDR'].')';
            ?></span>
    </div>
    <div>
        <span>&copy;&nbsp;&nbsp;<a href="http://www.esono.de" target="_blank">ESONO AG</a>&nbsp;&nbsp;<?=date('Y'); ?></span>
    </div>
</footer>
<?php
require_once dirname(__FILE__).'/cms_footer_data.inc.php';
