Media Manager
=============

The default media manager in Chameleon is a module ('chameleon_system_media_manager.backend_module.media_manager').
It's used to both browse and edit images as well as to pick images inside image fields.

It supports different list views, drag and drop of images and folders, multi selection and click-to-edit in list view.

Installation
------------

The bundle is installed automatically as part of the Chameleon System.

Configuration
-------------

The config parameter `open_in_new_window` defines if the standalone media manager should be opened
in a new window or in the same frame with default chameleon header:

.. code-block:: yaml

    chameleon_system_media_manager:
        open_in_new_window: false

By editing the config parameters `available_page_sizes` and `default_page_size` available page sizes for the list view
can be set. Page size -1 shows all entries.

.. code-block:: yaml

    chameleon_system_media_manager:
        available_page_sizes: [12, 24, 48, 96, 204, 504, -1]
        default_page_size: 204

Find and Delete Usages
----------------------

The media manager finds usages of images automatically for core fields and deletes them if the image is deleted. For
custom fields that incorporate image usage, custom usage finders need to be registered. Do this as follows:

- To find usages, create a class implementing `MediaItemUsageFinderInterface`, and register a service in the service
  container tagged with `chameleon_system_media_manager.usage_finder`.
- To delete references, create a class implementing `MediaItemUsageDeleteServiceInterface`, and register a service in
  the service container tagged with `chameleon_system_media_manager.usage_delete_service`.

Legacy Media Manager
--------------------

Because the media manager is not a table manager, the list field configuration from the table config is not represented
in the default list view of the media manager. However, there is one list view that uses the default list manager.
