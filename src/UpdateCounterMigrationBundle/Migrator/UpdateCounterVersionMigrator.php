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

use ChameleonSystem\UpdateCounterMigrationBundle\DataAccess\BundleDataAccessInterface;
use ChameleonSystem\UpdateCounterMigrationBundle\DataAccess\CounterMigrationDataAccessInterface;
use ChameleonSystem\UpdateCounterMigrationBundle\Exception\InvalidMigrationCounterException;

class UpdateCounterVersionMigrator
{
    /**
     * @var CounterMigrationDataAccessInterface
     */
    private $counterMigrationDataAccess;
    /**
     * @var BundleDataAccessInterface
     */
    private $bundleDataAccess;

    public function __construct(CounterMigrationDataAccessInterface $counterMigrationDataAccess, BundleDataAccessInterface $bundleDataAccess)
    {
        $this->counterMigrationDataAccess = $counterMigrationDataAccess;
        $this->bundleDataAccess = $bundleDataAccess;
    }

    /**
     * @throws InvalidMigrationCounterException
     *
     * @return void
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
     *
     * @return void
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
            $newName = $this->getNewCounterName($oldName);
            if (null === $newName) {
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
     * @return string|null
     */
    private function getNewCounterName($oldName)
    {
        $matches = array();
        preg_match('#dbversion-meta-(\w+)#', $oldName, $matches);
        $type = $matches[1];

        switch ($type) {
            case 'core':
            case 'core3':
                return 'ChameleonSystemCoreBundle';
            case 'custom-core':
                return null;
            case 'customer':
                return 'EsonoCustomerBundle';
            case 'module':
                return $this->getNewModuleCounterName($oldName);
            case 'packages':
                return $this->getNewPackageCounterName($oldName);
            default:
                return null;
        }
    }

    private function getNewModuleCounterName(string $oldName): ?string
    {
        preg_match('#dbversion-meta-module-(.+?)(-updates|/|$)#', $oldName, $matches);
        $packageName = $matches[1];

        if ('pkgShop' === $packageName) {
            return 'ChameleonSystemShopBundle';
        }
        if ('pkgExtranetUserProfile' === $packageName) { // no longer supported as module, only as package
            return null;
        }

        return 'ChameleonSystemCoreBundle';
    }

    /**
     * @param string $oldName
     *
     * @return string|null
     */
    private function getNewPackageCounterName($oldName)
    {
        preg_match('#dbversion-meta-\w+-(.+?)(-updates|/|$)#', $oldName, $matches);
        $normalizedPackageName = $this->normalizeBundleName($matches[1]);

        $bundlePaths = $this->bundleDataAccess->getBundlePaths();
        foreach ($bundlePaths as $bundleName => $bundlePath) {
            /*
             * Handle bundles named CustomerBundle; the "official" EsonoCustomerBundle is already handled in
             * getNewCounterName(), so we are sure that it is not applicable here.
             */
            if ('EsonoCustomerBundle' === $bundleName) {
                continue;
            }

            $normalizedBundleDirectory = $this->normalizeBundleName(\basename($bundlePath));
            if ($normalizedBundleDirectory === $normalizedPackageName) {
                return $bundleName;
            }
            if ($normalizedBundleDirectory === $this->getChameleonBaseOrShopBundleName($normalizedPackageName)
                && 0 === strpos($bundleName, 'ChameleonSystem')) {
                return $bundleName;
            }
        }

        return null;
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

    private function getChameleonBaseOrShopBundleName(string $normalizedPackageName): ?string
    {
        return self::$packageNames[$normalizedPackageName] ?? null;
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
