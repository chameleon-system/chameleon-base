<?php

use ChameleonSystem\CoreBundle\Bridge\Chameleon\Twig\VersionExtension;

/**
 * @deprecated since 7.1.48 - use VersionExtension directly
 */
$versionExtension = new VersionExtension();
$cmsVersion = $versionExtension->getCmsVersion();

if (false !== strpos($cmsVersion, '.')) {
    [$major, $minor] = explode('.', $cmsVersion, 2);
} else {
    $major = 'Major-Version-not-found';
    $minor = 'Minor-Version-not-found';
}

define('CMS_VERSION_MAJOR', $major);
define('CMS_VERSION_MINOR', $minor);
