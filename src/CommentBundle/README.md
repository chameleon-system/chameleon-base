Chameleon System CommentBundle
==============================

Setup
-----

Copy the required views from the install directory into the customer bundle.

Create Module
-------------

Create a module that extends MTPkgCommentCore and register it in the Chameleon backend.
Also create a views for commenting and a view for reporting comments.


Create Comment Type
-------------------

Create a comment type class that defines which table objects can be commented.
This class needs to extend TPkgCommentType and override the method GetActiveItem().

Also create a new comment type in the backend which uses that class.

Report Comment
--------------

To enable reporting of comments, create a new page and place the comment module with the reporting view on it.
Assign this page to the system page entry "announcecomment".

You might also like to adjust the email template "reportcomment" to your needs.

Configure Module
----------------

Add the module to the detail page of the object you like to get comments for, and configure it.
