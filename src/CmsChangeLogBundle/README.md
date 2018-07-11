Chameleon System CmsChangeLogBundle
===================================

Purpose of this package
-----------------------

After installing this package, you can log changes performed by an administrative user.

Prerequisites
-------------

All tables to be logged need a name column configured.

Limitations
-----------

* This package is not designed to comply to auditing requirements. It is quite easy for an administrator to circumvent the logging by simply deactivating it in the table configuration.
* Not all TCMSField classes are yet compatible. This results in some tables not being loggable, or log table output looking strange. So when using the package, test your tables. 
* When handling encrypted data you will want to make sure that no information is disclosed in the log. See the "Developer information" chapter for details.

Tested tables so far:

* data_extranet_user
* data_extranet_user_profile

Usage
-----

This package will log changes to any table with the changelog flag set to true (activate it in the respective table settings). This flag is set to false by default.
It will log anything that is saved through the TCMSTableEditor (as long as there are no fields that disabled ).
Afterwards there are three places where the change log can be found:

1. The dataset itself will provide a new button in the table's menu. Clicking it will get you a list of changes for that specific dataset.
2. The corresponding list will provide this button as well. Clicking it will get you a list of changes for that specific data type.
3. And there is a new button in the backend overview named "CMS Changelog". From here you will get to a list of all changes in all tables that are enabled for change logging.

Developer information
----------------------

1. When handling encrypted fields, make sure that your field implementation sets the flag bEncryptedData flag to true in its constructor (see TCMSFieldPasswordEncrypted for an example). Otherwise unencrypted data would be stored in the change log, as the change log table manager receives submitted user data before it is encrypted.
2. When using the TCMSTableEditor for other tasks, such as importing data, be aware that these changes will also be logged. Chances are that you want to disable the changelog flag before and re-set it after your operation.
3. When you create a new descendant of TCMSField, there are two places of interest in the code. The first one is in the new TCMSField class itself. If you don't like the output the base TCMSField class generates in the change log, override the toString() method and return the desired format there. The other one is in class TCMSFieldEqualsVisitor in this package. If your new field requires a special way of comparison in order to find differences between fields, implement a new method here that is named visitTCMSFieldNewFieldName.
