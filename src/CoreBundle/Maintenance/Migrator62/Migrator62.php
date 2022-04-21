<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Maintenance\Migrator62;

use ChameleonSystem\CoreBundle\Maintenance\Helper\ComposerJsonModifier;
use ChameleonSystem\CoreBundle\Maintenance\MigratorInterface;

class Migrator62 implements MigratorInterface
{
    /**
     * @var string
     */
    private $baseDir;

    public function __construct()
    {
        $this->baseDir = realpath(__DIR__.'/../../../../../../..');
    }

    /**
     * @return void
     */
    public function migrate()
    {
        $this->migrate62FileBase();
        $this->migrate62ComposerJson();
        $this->finish();
    }

    /**
     * @return void
     */
    private function migrate62FileBase()
    {
        $this->addAutoloadFile();
        $this->adjustGitignore();
        $this->adjustPublicSymlinks();
        $this->removeOldFiles();
    }

    /**
     * @return void
     */
    private function addAutoloadFile()
    {
        copy(__DIR__.'/autoloadTemplate.php', $this->baseDir.'/app/autoload.php');
    }

    /**
     * @return void
     */
    private function adjustGitignore()
    {
        copy(__DIR__.'/gitignoreTemplate.txt', $this->baseDir.'/.gitignore');
    }

    /**
     * @return void
     */
    private function migrate62ComposerJson()
    {
        $composerHelper = new ComposerJsonModifier();
        $data = $composerHelper->getComposerData($this->baseDir.'/composer.json');

        $composerHelper->removeKey($data, 'version');
        $composerHelper->addKey($data, 'type', 'project', true);

        $composerHelper->addRequire($data, [
            'incenteev/composer-parameter-handler' => '~2.0',
            'sensio/framework-extra-bundle' => '^3.0.2',
            'symfony/monolog-bundle' => '~2.4|~3.0',
        ]);
        $composerHelper->addRequireDev($data, [
            'sensio/generator-bundle' => '~3.0',
        ]);
        $composerHelper->removeRequire($data, [
            'chameleon-system/javascript-minification-bundle',
            'chameleon-system/pkgcmssecurity',
            'chameleon-system/pkgdependencyinjection',
            'chameleon-system/pkgworkflow',
            'chameleon-system/responsive-images-bundle',
            'doctrine/common',
        ]);
        $composerHelper->removeRequireDev($data, [
            'behat/mink-selenium2-driver',
            'chameleon-system/behat-extension',
            'chameleon-system/javascript-minification-bundle',
            'chameleon-system/pkgcmssecurity',
            'chameleon-system/pkgdependencyinjection',
            'chameleon-system/pkgtranslationservice',
            'chameleon-system/pkgworkflow',
            'chameleon-system/responsive-images-bundle',
            'pdepend/pdepend',
            'phpmd/phpmd',
            'phpspec/phpspec',
        ]);
        $replacedChameleonPackageNames = $this->getReplacedChameleonPackageNames();
        $composerHelper->removeRequire($data, $replacedChameleonPackageNames);
        $composerHelper->removeRequireDev($data, $replacedChameleonPackageNames);
        $composerHelper->removeSuggest($data, [
            'doctrine/common',
        ]);

        $composerHelper->addAutoloadClassmap($data, [
            'src/extensions',
            'src/framework',
        ]);

        $symfonyScripts = [
            'Incenteev\ParameterHandler\ScriptHandler::buildParameters',
            'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::clearCache',
            'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::installAssets',
            'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::prepareDeploymentTarget',
        ];

        $composerHelper->addScripts($data, [
            'symfony-scripts' => $symfonyScripts,
        ]);

        $composerHelper->addPostInstallCommands($data, [
            '@symfony-scripts',
        ]);
        $composerHelper->addPostUpdateCommands($data, [
            '@symfony-scripts',
        ]);

        $composerHelper->addExtra($data, [
            'symfony-app-dir' => 'app',
            'symfony-web-dir' => 'web',
            'symfony-assets-install' => 'relative',
            'incenteev-parameters' => [
                'file' => 'app/config/parameters.yml',
            ],
        ]);

        $composerHelper->removeRepository($data, 'https://github.com/bestform/phpspec');

        $composerHelper->saveComposerFile($data);
    }

    /**
     * @return array
     */
    private function getReplacedChameleonPackageNames()
    {
        return [
            'chameleon-system/autoclasses-bundle',
            'chameleon-system/cookie-consent-bundle',
            'chameleon-system/core',
            'chameleon-system/database-migration-bundle',
            'chameleon-system/debug-bundle',
            'chameleon-system/distribution-bundle',
            'chameleon-system/minifier-js-jshrink-bundle',
            'chameleon-system/pkgatomiclock',
            'chameleon-system/pkgcmsactionplugin',
            'chameleon-system/pkgcmscache',
            'chameleon-system/pkgcmscaptcha',
            'chameleon-system/pkgcmschangelog',
            'chameleon-system/pkgcmsclassmanager',
            'chameleon-system/pkgcmscorelog',
            'chameleon-system/pkgcmscounter',
            'chameleon-system/pkgcmsevent',
            'chameleon-system/pkgcmsfilemanager',
            'chameleon-system/pkgcmsinterfacemanager',
            'chameleon-system/pkgcmsnavigation',
            'chameleon-system/pkgcmsnavigationpkgshop',
            'chameleon-system/pkgcmsresultcache',
            'chameleon-system/pkgcmsrouting',
            'chameleon-system/pkgcmsstringutilities',
            'chameleon-system/pkgcmstextblock',
            'chameleon-system/pkgcmstextfield',
            'chameleon-system/pkgcomment',
            'chameleon-system/pkgcore',
            'chameleon-system/pkgcorevalidatorconstraints',
            'chameleon-system/pkgcsv2sql',
            'chameleon-system/pkgexternaltracker',
            'chameleon-system/pkgexternaltrackergoogleanalytics',
            'chameleon-system/pkgextranet',
            'chameleon-system/pkgextranetregistrationguest',
            'chameleon-system/pkggenerictableexport',
            'chameleon-system/pkgimagehotspot',
            'chameleon-system/pkgmultimodule',
            'chameleon-system/pkgnewsletter',
            'chameleon-system/pkgrevisionmanagement',
            'chameleon-system/pkgsearch',
            'chameleon-system/pkgshop',
            'chameleon-system/pkgshopaffiliate',
            'chameleon-system/pkgshoparticledetailpaging',
            'chameleon-system/pkgshoparticlepreorder',
            'chameleon-system/pkgshoparticlereview',
            'chameleon-system/pkgshopcurrency',
            'chameleon-system/pkgshopdhlpackstation',
            'chameleon-system/pkgshoplistfilter',
            'chameleon-system/pkgshopnewslettersignupwithorder',
            'chameleon-system/pkgshoporderstatus',
            'chameleon-system/pkgshoporderviaphone',
            'chameleon-system/pkgshoppaymentamazon',
            'chameleon-system/pkgshoppaymentipn',
            'chameleon-system/pkgshoppaymenttransaction',
            'chameleon-system/pkgshopprimarynavigation',
            'chameleon-system/pkgshopproductexport',
            'chameleon-system/pkgshopratingservice',
            'chameleon-system/pkgshopwishlist',
            'chameleon-system/pkgsnippetrenderer',
            'chameleon-system/pkgtrackviews',
            'chameleon-system/pkgtshoppaymenthandlersofortueberweisung',
            'chameleon-system/pkgurlalias',
            'chameleon-system/pkgviewrenderer',
            'chameleon-system/twig-debug-bundle',
            'chameleon-system/update-counter-migration-bundle',
        ];
    }

    /**
     * @return void
     */
    private function finish()
    {
        echo <<<EOF
Migration finished. Please check if the changes to the files make sense for this project. Revert single changes where you dissent.

EOF;
    }

    /**
     * @return void
     */
    private function adjustPublicSymlinks()
    {
        $webDir = $this->baseDir.'/web/';

        $symlinks = [
            'fatal.php' => 'fatal.php',
            '.htaccess' => '.htaccess',
            'index.php' => 'app.php',
            'maintenance.php' => 'maintenance.php',
        ];
        $originalWorkingDir = getcwd();
        chdir($webDir);
        foreach ($symlinks as $symlinkName => $targetName) {
            $symlinkFile = $webDir.$symlinkName;
            if (is_link($symlinkFile)) {
                unlink($symlinkFile);
                symlink('../vendor/chameleon-system/chameleon-base/src/CoreBundle/FrontController/'.$targetName, $symlinkName);
            }
        }

        $chameleonDir = $webDir.'chameleon/';
        $symlinkFile = $chameleonDir.'blackbox';
        chdir($chameleonDir);
        if (is_link($symlinkFile)) {
            unlink($symlinkFile);
            symlink('../../vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/public', 'blackbox');
        }

        $assetsDir = $webDir.'assets/';
        chdir($assetsDir);
        if (is_link('standard-assets')) {
            unlink('standard-assets');
            symlink('../../src/themes/standard/standard-assets', 'standard-assets');
        }

        if (true === \is_link('chameleon-standard-shop-assets')) {
            \unlink('chameleon-standard-shop-assets');
            if (true === \class_exists('\ChameleonSystem\ThemeShopStandardBundle\ChameleonSystemThemeShopStandardBundle')) {
                \symlink('../../vendor/chameleon-system/themeshopstandard/Resources/public', 'chameleon-standard-shop-assets');
            }
        }

        if (true === \is_link('ckeditorBootstrap-assets')) {
            \unlink('ckeditorBootstrap-assets');
            \symlink('../../vendor/chameleon-system/ckeditor-bootstrap-bundle/Resources/public', 'ckeditorBootstrap-assets');
        }

        if (true === \is_link('pkgshoppaymentamazon-assets')) {
            \unlink('pkgshoppaymentamazon-assets');
            \symlink('../../vendor/chameleon-system/chameleon-shop/src/AmazonPaymentBundle/Resources/public', 'pkgshoppaymentamazon-assets');
        }

        if (true === \is_link('pkgShopPaymentPayone')) {
            \unlink('pkgShopPaymentPayone');
            \symlink('../../vendor/chameleon-system/pkgshoppaymentpayone/Resources/public', 'pkgShopPaymentPayone');
        }

        if (true === \is_link('sanitycheck-bundle-assets')) {
            \unlink('sanitycheck-bundle-assets');
        }

        $snippetsCmsDir = $this->baseDir.'/src/extensions/snippets-cms';
        if (false === \is_dir($snippetsCmsDir)) {
            \mkdir($snippetsCmsDir, 0777, true);
        }
        \chdir($snippetsCmsDir);
        \symlink('../../../vendor/chameleon-system/chameleon-base/src/MediaManagerBundle/Resources/views/snippets-cms/mediaManager', 'mediaManager');

        chdir($originalWorkingDir);
    }

    /**
     * @return void
     */
    private function removeOldFiles()
    {
        $vendorBinDir = $this->baseDir.'/vendor/bin';
        @unlink($vendorBinDir.'/behat');
        @unlink($vendorBinDir.'/pdepend');
        @unlink($vendorBinDir.'/phpmd');
        @unlink($vendorBinDir.'/phpspec');

        @unlink($this->baseDir.'/behat.yml');
        @unlink($this->baseDir.'/behat-parameters.yml.dist');

        @unlink($this->baseDir.'/deploy/deploy.exclude.txt');

        @unlink($this->baseDir.'/src/Esono/CustomerBundle/framework/preSessionStart/preSessionStart.inc.php');
    }
}
