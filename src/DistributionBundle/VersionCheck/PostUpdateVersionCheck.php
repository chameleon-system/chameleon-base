<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DistributionBundle\VersionCheck;

use ChameleonSystem\DistributionBundle\VersionCheck\Filter\ChameleonPackageFilter;
use ChameleonSystem\DistributionBundle\VersionCheck\Version\ChameleonVersion;
use ChameleonSystem\DistributionBundle\VersionCheck\Version\MatchLevel;
use Composer\Script\Event;

class PostUpdateVersionCheck
{
    /**
     * @return void
     */
    public static function checkVersion(Event $e)
    {
        echo "\n\n*** Chameleon Version Check ***\n\n";

        $localRepo = $e->getComposer()->getRepositoryManager()->getLocalRepository();

        $packages = $localRepo->getPackages();
        /** @var ChameleonVersion[] $chameleonPackages */
        $chameleonPackages = [];
        $filter = new ChameleonPackageFilter();
        $core = null;
        foreach ($packages as $package) {
            if ('chameleon-system/chameleon-base' === $package->getName()) {
                $core = new ChameleonVersion($package->getName(), $package->getPrettyVersion());
                continue;
            }
            if ($filter->filter($package->getName())) {
                $chameleonPackages[] = new ChameleonVersion($package->getName(), $package->getPrettyVersion());
            }
        }

        if (null === $core) {
            echo "No installed Chameleon version found. No version check required.\n\n";

            return;
        }

        echo 'The chameleon base system is installed in version '.$core->getPrettyVersion()."\n\n";
        echo "Checking other versions...\n\n";

        foreach ($chameleonPackages as $chameleonPackage) {
            $matchLevel = $core->match($chameleonPackage);
            if ($matchLevel->getMatchLevel() === MatchLevel::$MATCH_SAME) {
                continue;
            }
            $message = $matchLevel->getMatchLevel() < $matchLevel::$MATCH_LEVEL_2 ? 'Minor difference' : 'Major difference';
            echo $message.' between '.$core->getName().' ('.$core->getPrettyVersion().') and '.$chameleonPackage->getName().' ('.$chameleonPackage->getPrettyVersion().")\n";
        }
    }
}
