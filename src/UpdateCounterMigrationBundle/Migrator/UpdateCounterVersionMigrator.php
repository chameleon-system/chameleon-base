<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\Migrator;

use ChameleonSystem\UpdateCounterMigrationBundle\DataAccess\CounterMigrationDataAccessInterface;
use ChameleonSystem\UpdateCounterMigrationBundle\Exception\InvalidMigrationCounterException;
use LogicException;
use Symfony\Component\HttpKernel\KernelInterface;

class UpdateCounterVersionMigrator
{
    /**
     * @var CounterMigrationDataAccessInterface
     */
    private $counterMigrationDataAccess;
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param CounterMigrationDataAccessInterface $counterMigrationDataAccess
     * @param KernelInterface                     $kernel
     */
    public function __construct(CounterMigrationDataAccessInterface $counterMigrationDataAccess, KernelInterface $kernel)
    {
        $this->counterMigrationDataAccess = $counterMigrationDataAccess;
        $this->kernel = $kernel;
    }

    /**
     * @throws InvalidMigrationCounterException
     */
    public function migrate()
    {
        $migrationCounterVersion = $this->counterMigrationDataAccess->getMigrationCounterVersion();
        if ($migrationCounterVersion < 2) {
            $this->migrateToVersionTwo();
        }
    }

    /**
     * @throws InvalidMigrationCounterException
     */
    private function migrateToVersionTwo()
    {
        $this->counterMigrationDataAccess->createMigrationTablesVersionTwo();
        $oldCounters = $this->counterMigrationDataAccess->getAllCountersVersionOne();
        $mapping = $this->getNewCounterData($oldCounters);
        $this->counterMigrationDataAccess->createCountersVersionTwo($mapping);
        $this->counterMigrationDataAccess->deleteCountersVersionOne('dbversion-%', array(
            'dbversion-counter',
            'dbversion-timestamp',
        ));
        $this->counterMigrationDataAccess->saveMigrationCounterVersion(2);
    }

    /**
     * @param array $oldCounters
     *
     * @return array
     *
     * @throws InvalidMigrationCounterException
     */
    private function getNewCounterData(array $oldCounters)
    {
        $newCounterData = array();
        $invalidCounters = array();
        foreach ($oldCounters as $oldCounter) {
            $oldName = $oldCounter['systemname'];

            try {
                $newName = $this->getNewCounterName($oldName);
            } catch (LogicException $e) {
                $invalidCounters[] = $oldName;
                continue;
            }

            $buildNumbers = json_decode($oldCounter['value'], true);
            $buildNumbers = $buildNumbers['buildNumbers'];
            if (isset($newCounterData[$newName])) {
                foreach ($buildNumbers as $buildNumber) {
                    $newCounterData[$newName][] = $buildNumber;
                }
            } else {
                $newCounterData[$newName] = $buildNumbers;
            }
            $newCounterData[$newName] = array_unique($newCounterData[$newName]);
            sort($newCounterData[$newName], SORT_NUMERIC);
        }
        if (count($invalidCounters) > 0) {
            throw new InvalidMigrationCounterException('Invalid migration counters found.', 0, null, $invalidCounters);
        }

        return $newCounterData;
    }

    /**
     * @param string $oldName
     *
     * @return string
     *
     * @throws LogicException
     */
    private function getNewCounterName($oldName)
    {
        $matches = array();
        preg_match('#dbversion-meta-(\w+)#', $oldName, $matches);
        $type = $matches[1];

        switch ($type) {
            case 'core':
            case 'core3':
                $newName = 'ChameleonSystemCoreBundle';
                break;
            case 'custom-core':
                $newName = '';
                break;
            case 'customer':
                $newName = 'EsonoCustomerBundle';
                break;
            case 'module':
                preg_match('#dbversion-meta-module-(.+?)(-updates|/|$)#', $oldName, $matches);
                $tempName = $matches[1];
                if ('pkgShop' === $tempName) {
                    $newName = 'ChameleonSystemShopBundle';
                } elseif ('pkgExtranetUserProfile' === $tempName) {
                    throw new LogicException(sprintf('pkgExtranetUserProfile no longer supported as a module. The migration counter "%s" will automatically be removed from the database.', $oldName));
                } else {
                    $newName = 'ChameleonSystemCoreBundle';
                }
                break;
            case 'packages':
                preg_match('#dbversion-meta-\w+-(.+?)(-updates|/|$)#', $oldName, $matches);
                try {
                    $newName = $this->getBundleNameFromPackageName($matches[1]);
                } catch (LogicException $e) {
                    throw new LogicException(sprintf(
                        'No bundle was found for package "%s". The migration counter "%s" will automatically be removed from the database.',
                        $matches[1],
                        $oldName
                    ));
                }
                break;
            default:
                $newName = '';
                break;
        }

        return $newName;
    }

    /**
     * @param string $packageName
     *
     * @return string
     *
     * @throws LogicException
     */
    private function getBundleNameFromPackageName($packageName)
    {
        $normalizedPackageName = $this->normalizeBundleName($packageName);
        $bundles = $this->kernel->getBundles();
        foreach ($bundles as $bundle) {
            $bundlePath = $bundle->getPath();
            $normalizedBundleName = $this->normalizeBundleName(\basename($bundlePath));
            if ($normalizedBundleName === $normalizedPackageName
                || (true === \array_key_exists($normalizedPackageName, self::$packageNames) && self::$packageNames[$normalizedPackageName] === $normalizedBundleName)) {
                return $bundle->getName();
            }
        }

        throw new LogicException();
    }

    /**
     * @param string $bundleName
     *
     * @return string
     */
    private function normalizeBundleName($bundleName)
    {
        return \mb_strtolower(\str_replace('-', '', $bundleName));
    }

    /**
     * @var array
     */
    private static $packageNames = [
        'core' => 'corebundle',
        'pkgatomiclock' => 'atomiclockbundle',
        'pkgcmsactionplugin' => 'cmsactionpluginbundle',
        'pkgcmscache' => 'cmscachebundle',
        'pkgcmscaptcha' => 'cmscaptchabundle',
        'pkgcmschangelog' => 'cmschangelogbundle',
        'pkgcmsclassmanager' => 'cmsclassmanagerbundle',
        'pkgcmscorelog' => 'cmscorelogbundle',
        'pkgcmscounter' => 'cmscounterbundle',
        'pkgcmsevent' => 'cmseventbundle',
        'pkgcmsfilemanager' => 'cmsfilemanagerbundle',
        'pkgcmsinterfacemanager' => 'cmsinterfacemanagerbundle',
        'pkgcmsnavigation' => 'cmsnavigationbundle',
        'pkgcmsresultcache' => 'cmsresultcachebundle',
        'pkgcmsrouting' => 'cmsroutingbundle',
        'pkgcmsstringutilities' => 'cmsstringutilitiesbundle',
        'pkgcmstextblock' => 'cmstextblockbundle',
        'pkgcmstextfield' => 'cmstextfieldbundle',
        'pkgcomment' => 'commentbundle',
        'pkgcore' => 'pkgcorebundle',
        'pkgcorevalidatorconstraints' => 'corevalidatorconstraintsbundle',
        'pkgcsv2sql' => 'csv2sqlbundle',
        'pkgexternaltracker' => 'externaltrackerbundle',
        'pkgexternaltrackergoogleanalytics' => 'externaltrackergoogleanalyticsbundle',
        'pkgextranet' => 'extranetbundle',
        'pkggenerictableexport' => 'generictableexportbundle',
        'pkgmultimodule' => 'multimodulebundle',
        'pkgnewsletter' => 'newsletterbundle',
        'pkgrevisionmanagement' => 'revisionmanagementbundle',
        'pkgsnippetrenderer' => 'snippetrendererbundle',
        'pkgtrackviews' => 'trackviewsbundle',
        'pkgurlalias' => 'urlaliasbundle',
        'pkgviewrenderer' => 'viewrendererbundle',

        'pkgcmsnavigationpkgshop' => 'cmsnavigationpkgshopbundle',
        'pkgextranetregistrationguest' => 'extranetregistrationguestbundle',
        'pkgimagehotspot' => 'imagehotspotbundle',
        'pkgsearch' => 'searchbundle',
        'pkgshop' => 'shopbundle',
        'pkgshopaffiliate' => 'shopaffiliatebundle',
        'pkgshoparticledetailpaging' => 'shoparticledetailpagingbundle',
        'pkgshoparticlepreorder' => 'shoparticlepreorderbundle',
        'pkgshoparticlereview' => 'shoparticlereviewbundle',
        'pkgshopcurrency' => 'shopcurrencybundle',
        'pkgshopdhlpackstation' => 'shopdhlpackstationbundle',
        'pkgshoplistfilter' => 'shoplistfilterbundle',
        'pkgshopnewslettersignupwithorder' => 'shopnewslettersignupwithorderbundle',
        'pkgshoporderstatus' => 'shoporderstatusbundle',
        'pkgshoporderviaphone' => 'shoporderviaphonebundle',
        'pkgshoppaymentamazon' => 'amazonpaymentbundle',
        'pkgshoppaymentipn' => 'shoppaymentipnbundle',
        'pkgshoppaymenttransaction' => 'shoppaymenttransactionbundle',
        'pkgshopprimarynavigation' => 'shopprimarynavigationbundle',
        'pkgshopproductexport' => 'shopproductexportbundle',
        'pkgshopratingservice' => 'shopratingservicebundle',
        'pkgshopwishlist' => 'shopwishlistbundle',
        'pkgtshoppaymenthandlersofortueberweisung' => 'shoppaymenthandlersofortueberweisungbundle',
        'themeshopstandard' => 'themeshopstandardbundle',
    ];
}
