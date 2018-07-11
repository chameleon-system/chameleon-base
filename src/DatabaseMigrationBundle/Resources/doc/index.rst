Chameleon System DatabaseMigrationBundle
========================================

This bundle handles the management of Chameleon update files. It has two main function blocks

1. Search and execute updates.
    It is able to combine lists of updates. This functionality is used by the Chameleon update manager to determine
    which updates need to run and which have already been processed previously.
2. Write update files that are ready to execute.
    This recording functionality is used by the database recorder in the Chameleon backend.

Note that the migration functionality in Chameleon is considered internal and is not suited for extension or
modification by external code. Interfaces, functionality and GUI may change anytime.

.. toctree::
  :maxdepth: 2

  usage_execute
  usage_record