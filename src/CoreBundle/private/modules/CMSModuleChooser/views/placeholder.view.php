<?php
use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\ServiceLocator;

$translator = ServiceLocator::get('translator');
?>
<div style="display: flex; align-items: center; margin-bottom: 8px; padding: 10px 15px;
    font-family: 'Segoe UI', Arial, Helvetica, sans-serif; font-size: 14px;
    color: #39f; background-color: #cce5ff; border: 1px solid #b8daff;
    border-left: 4px solid #39f; border-radius: 4px;">

    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="24" height="24" fill="currentColor"
         style="flex-shrink: 0;">
        <path d="M12.4 148l232.9 105.7c6.8 3.1 14.5 3.1 21.3 0l232.9-105.7c16.6-7.5 16.6-32.5 0-40L266.7 2.3a25.6 25.6 0 0 0 -21.3 0L12.4 108c-16.6 7.5-16.6 32.5 0 40zm487.2 88.3l-58.1-26.3-161.6 73.3c-7.6 3.4-15.6 5.2-23.9 5.2s-16.3-1.7-23.9-5.2L70.5 210l-58.1 26.3c-16.6 7.5-16.6 32.5 0 40l232.9 105.6c6.8 3.1 14.5 3.1 21.3 0L499.6 276.3c16.6-7.5 16.6-32.5 0-40zm0 127.8l-57.9-26.2-161.9 73.4c-7.6 3.4-15.6 5.2-23.9 5.2s-16.3-1.7-23.9-5.2L70.3 337.9 12.4 364.1c-16.6 7.5-16.6 32.5 0 40l232.9 105.6c6.8 3.1 14.5 3.1 21.3 0L499.6 404.1c16.6-7.5 16.6-32.5 0-40z"/>
    </svg>

    <span style="margin-left: 10px; flex-grow: 1;">
        <?php echo $translator->trans('chameleon_system_core.template_engine.slot', [], TranslationConstants::DOMAIN_BACKEND); ?>
        (<strong><?php echo $sModuleSpotName; ?></strong>)
    </span>
</div>
