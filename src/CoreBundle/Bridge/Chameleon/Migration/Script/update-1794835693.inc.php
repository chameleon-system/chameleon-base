<h1>Build #1794835693</h1>
<h2>Date: 2026-11-16</h2>
<div class="changelog">
    - ref #70900: make module_faq fields translatable, as the first update had the wrong migration path
</div>
<?php

// module_faq only exists in installations that use the FAQ module.
if (TCMSLogChange::TableExists('module_faq')) {
    foreach (['name', 'qdescription', 'artikel'] as $fieldName) {
        if (TCMSLogChange::FieldExists('module_faq', $fieldName)) {
            TCMSLogChange::makeFieldMultilingual('module_faq', $fieldName);
        }
    }
    TCMSLogChange::UpdateTranslationFields();
}
