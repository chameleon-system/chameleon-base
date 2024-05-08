Recording Updates
=================

Inject the service chameleon_system_database_migration.recorder.migration_recorder and follow this call sequence:

.. code-block:: php

    $filePointer = $migrationRecorder->startTransaction($activeTrackName, $activeBuildNumber);
    $migrationRecorder->writeQueries($filePointer, $dataModels);
    $migrationRecorder->endTransaction($filePointer);

$activeTrackName determines the base name of the file to be written and should be the name of the bundle
the migration file is written for (e.g. "ChameleonSystemCoreBundle").

$activeBuildNumber is the sequential number for this update. This number will be part of the file name and is crucial for
determining the order in which updates are executed on the target system. It is strongly recommended to pass a unix timestamp.

The recorder will open an update file, write the queries for the given data models and close the file when executed in
the sequence shown in the example.
