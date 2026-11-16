<h1>Build #1794835693</h1>
<h2>Date: 2026-11-16</h2>
<div class="changelog">
    - ref #70900: make module_faq fields translatable, as the first update had the wrong migration path
</div>
<?php

TCMSLogChange::makeFieldMultilingual('module_faq', 'name');
TCMSLogChange::makeFieldMultilingual('module_faq', 'qdescription');
TCMSLogChange::makeFieldMultilingual('module_faq', 'artikel');
TCMSLogChange::UpdateTranslationFields();
