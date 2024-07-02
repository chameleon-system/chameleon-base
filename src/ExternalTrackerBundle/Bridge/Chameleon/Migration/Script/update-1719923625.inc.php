<h1>Build #1719923625</h1>
<h2>Date: 2024-07-02</h2>
<div class="changelog">
    - ref #63934: added entry point TPkgExternalTrackerState
</div>
<?php
if (false === TCMSLogChange::RecordExists('pkg_cms_class_manager','name_of_entry_point', 'TPkgExternalTrackerState')) {
    $data = TCMSLogChange::createMigrationQueryData('pkg_cms_class_manager', 'en')
        ->setFields([
            'name_of_entry_point' => '',
            'exit_class' => '',
            'exit_class_subtype' => '',
            'exit_class_type' => 'Core',
            'id' => '421f31a9-a34c-8517-5255-7ae30970562b',
        ]);
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('pkg_cms_class_manager', 'en')
        ->setFields([
            'name_of_entry_point' => 'TPkgExternalTrackerState',
            'exit_class' => 'TPkgExternalTrackerStateEndPoint',
        ])
        ->setWhereEquals([
            'id' => '421f31a9-a34c-8517-5255-7ae30970562b',
        ]);
    TCMSLogChange::update(__LINE__, $data);
}