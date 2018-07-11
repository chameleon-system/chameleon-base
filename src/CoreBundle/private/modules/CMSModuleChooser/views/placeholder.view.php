<?php
     $translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
?>
<div align="center"
    style="position:relative;z-index:1000;font-family: Segoe UI, Arial, helvetica, sans-serif; font-size: 11px; margin: 5px; padding:5px; color: #616974; background-color: #F8F2AA; border: 1px solid #FFB608;"><?= $translator->trans('chameleon_system_core.template_engine.slot', array(), \ChameleonSystem\CoreBundle\i18n\TranslationConstants::DOMAIN_BACKEND); ?> (<?=$sModuleSpotName; ?>)</div>