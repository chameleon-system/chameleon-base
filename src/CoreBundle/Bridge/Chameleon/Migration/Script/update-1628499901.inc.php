<h1>Build #1628499901</h1>
<h2>Date: 2021-08-09</h2>
<div class="changelog">
    - #662: add missing cms message type name translations so TCMSLogChange::GetMessageTypeByName works again
</div>
<?php

$languageService = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
$baseLanguage = $languageService->getCmsBaseLanguage();

if ('de' === $baseLanguage->fieldIso6391) {
    $query = "UPDATE `cms_message_manager_message_type` SET `name__en` = `name` WHERE `name__en` = ''";
    TCMSLogChange::RunQuery(__LINE__,$query);
} else { // en base language
    $query = "UPDATE `cms_message_manager_message_type` SET `name__de` = `name` WHERE `name__de` = ''";
    TCMSLogChange::RunQuery(__LINE__,$query);
}
