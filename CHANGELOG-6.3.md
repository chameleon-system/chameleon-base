CHANGELOG for 6.3.x
===================

# New Features

## Backend Theme

The backend theme was redesigned to get a clean and modern look. We use the free admin template CoreUI 
(https://coreui.io) - big thanks to the CoreUI team!

The theme is based on Bootstrap 4 and therefore responsive; Chameleon supports use on desktop and tablet devices;
smartphone support is planned for a future release. 

This redesign also incorporates usability improvements, e.g. a new date picker and improved search functionality for
lists and records.

## Backend Sidebar Menu

The classic main menu was replaced with a searchable sidebar menu. Some key features:
- The menu is available on every backend page, so no more hops to the main page.
- Menu categories were rethought so that menu items can more easily be spotted.
- Menu items were renamed to make them easier to understand.
- Menu items are presented in collapsed categories so that the amount of items is no longer overwhelming on first sight.
- Some menu items from the top navigation bar were moved to the sidebar to cleanup the backend.
- Menu items have a fixed order now, so that they keep their familiar places after switching languages.
- Backend modules that were called in a popup window, like navigation, product search index generation and sanity check,
  now have room to open inline.

The classic main menu will be removed in a future Chameleon release and is invisible for now. See the upgrade guide on
how to get it back if needed.

## New ImageCropBundle

Chameleon now ships with a bundle that provides support for image cutouts (display parts of images). See the upgrade
guide on how to configure it.

## Symfony 3.4

Chameleon now uses Symfony in its latest long term support release 3.4.

## PHP 7.3 Compatible

The system was successfully tested for compatibility with PHP 7.3.

## Cronjob Activation Toggle

Cronjobs can now be disabled globally by toggling the new option `Cronjobs enabled` in the CMS settings.

There are also new console commands `chameleon_system:cronjobs:enable` and `chameleon_system:cronjobs:enable` so that
enabling and disabling can be performed by console. This can be useful e.g. in deployment scenarios where it is important
that no cronjob is running in an intermediate system state.

If cronjobs get deactivated while running, the current cronjob will still be finished normally, but no following job
will be started.

Be sure to run the commands in the same Symfony environment as the web application. Otherwise manual cache clearing is
required.

## Cronjob Status Check

There is a new console command `chameleon_system:cronjobs:state_check`. This command will return `running` if any
cronjob is running at the time the command is issued. If currently no cronjob is running, the command will return
`idle`.

## Home Pagedef Configurable

The home pagedef of the backend that is displayed after login and after click on the Home history items can now be
configured using the configuration key `chameleon_system_core: backend: home_pagedef`.

Also any other pagedef that doesn't require further URL parameters can be configured.

## Static Backend Content

It is now possible to display translated static content in the backend by using the new `StaticViewModule`. Usages
should be straightforward - see `welcome.pagedef.php` for an example.

# Changed Features

## Logging

Chameleon now logs messages similar to other Symfony applications, using `Psr\Log\LoggerInterface` and `Monolog`.
See the upgrade guide on how to configure.

## MailTargetTransformationService

The MailTargetTransformationService that redirects emails to a developer email address is now completely disabled if
the configuration key `chameleon_system_core: mail_target_transformation_service: enabled` is set to `false`. In
previous releases the email subject was still modified even if disabled.

## Routing Configuration

Each routing config defined in the backend (menu "Routing configuration") can now be disabled separately.

## Variable Replacement

Post render variables are no longer tied to the ChameleonController. Replacement is now performed in 
`\ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface` and may therefore be called independent from
the controller.
