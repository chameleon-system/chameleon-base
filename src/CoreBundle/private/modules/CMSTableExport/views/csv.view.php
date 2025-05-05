<?php

if (!empty($data['csvDownloadUrl'])) {
    echo '<h2>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_export.state_export_done').'.<br />
    '.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_export.download_help').'.</h2>

    <a href="'.$data['csvDownloadUrl'].'" target="_blank"><img src="/chameleon/blackbox/images/button_download_web20.jpg" border="0" alt="'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_export.download_help').'" style="margin-top: 20px;" /></a>
    ';
}
