Chameleon System NewsletterBundle
=================================

Installation
------------

Copy the directory install/tocopy to your project file directories

Please note, that the field "company" is not activated by default in the signUp twig template.

To set a field mandatory simply set this flag to the field in the backend.

Shell Commands
--------------

You can send an active newsletter campaign by its identifiert name.

```bash 
app/console chameleon_system:newsletter:send-newsletter "Test Newsletter"
```
