UPGRADE FROM 7.1 to 7.2
=======================

# Essentials

The steps in this chapter are required to get the project up and running in version 7.2.
It is recommended to follow these steps in the given order.

## Change Or Remove Deprecated Code (Symfony)

You must change some code which was deprecated in previous Symfony versions and is now removed. Do this now with a working
Chameleon 7.1 project. Any change should also be working with "old" Symfony 4.4.

### List Of Removed Or Changed Code

- Configuration classes: TreeBuilder must be constructed with an argument. (search for new TreeBuilder())
- Event dispatcher: The argument order is swapped. (search for ->dispatch( )
- Session: Instead of `getSession()` `hasSession()` should be used for a null check. (search for ->getSession( with a following null check)
- Some event classes have been renamed. Especially FilterResponseEvent, GetResponseEvent and GetRequestEvent.
- Also note that the event class should match the event type (i.e. RequestEvent for "kernel.request").
- Change the event base class to \Symfony\Contracts\EventDispatcher\Event.
- The namespace of the TranslatorInterface changed to Symfony\Contracts\Translation\TranslatorInterface.
- Take care that all yaml string values have quotes. For example in any config.yml.

This list might not be complete. Also take a look at the official Symfony migration documentation:
https://github.com/symfony/symfony/blob/5.4/UPGRADE-5.0.md


## Adjust Composer Dependencies

In `composer.json`, adjust version constraints for all Chameleon dependencies from `~7.1.0` to `~7.2.0` and run
`composer update`.

Remove the file `app/autoload.php`. It is no longer used by the system (see below).

# Removed Features

## Annotation support

The functionality "annotation support" was removed. This file was calling a
deprecated function `AnnotationRegistry::registerLoader()`. If needed annotations can still be configured and used
directly in a project.
However with php 8 you should use attributes instead.

# Newly Deprecated Code Entities
# Removed Code Entities

The code entities in this list were marked as deprecated in previous releases and have now been removed.

