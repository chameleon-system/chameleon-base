Chameleon System ExtranetBundle
===============================


Setup
-----

* On every page that has a login form, a static module needs to be placed with the following settings:
   * Name: extranetHandler
   * Model: MTExtranet
   * Template: inc/system
   * Static: yes
* Create the following pages and add the module MTExtranet with the corresponding view
   * Login
   * Login success
   * Forgot password
   * Registration (if users are not added manually via CMS backend)
   * Registration success
   * Access denied (if you want to show a different text as the login page)
* Change the email template text and sender with systemname "registration"
