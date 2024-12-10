<?php

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class VersionExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('cms_version', [$this, 'getCmsVersion']),
        ];
    }

    public function getCmsVersion()
    {
        $composerLockFile = PATH_PROJECT_BASE.'composer.lock';
        $packageName = 'chameleon-system/chameleon-base';

        if (file_exists($composerLockFile)) {
            $composerData = json_decode(file_get_contents($composerLockFile), true);

            foreach ($composerData['packages'] as $package) {
                if ($package['name'] === $packageName) {
                    return $package['version'];
                }
            }
        }

        return 'Version not found';
    }
}
