<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;

class MTPkgViewRendererSnippetGalleryCore extends MTPkgViewRendererAbstractModuleMapper
{
    /**
     * @var string
     */
    private $sActiveRelativePath = '';

    /**
     * @var bool
     */
    private $bHideNavigation = false;

    public function Init()
    {
        parent::Init();

        if ($this->global->UserDataExists('sActiveRelativePath')) {
            $this->sActiveRelativePath = $this->global->GetUserData('sActiveRelativePath');
        }
        if ($this->global->UserDataExists('bHideNavigation') && '1' == $this->global->GetUserData('bHideNavigation')) {
            $this->bHideNavigation = true;
        }
    }

    /**
     * To map values from models to views the mapper has to implement iVisitable.
     * The ViewRender will pass a prepared MapeprVisitor instance to the mapper.
     *
     * The mapper has to fill the values it is responsible for in the visitor.
     *
     * example:
     *
     * $foo = $oVisitor->GetSourceObject("foomodel")->GetFoo();
     * $oVisitor->SetMapperValue("foo", $foo);
     *
     *
     * To be able to access the desired source object in the visitor, the mapper has
     * to declare this requirement in its GetRequirements method (see IViewMapper)
     *
     * @param bool $bCachingEnabled - if set to true, you need to define your cache trigger that invalidate the view rendered via mapper. if set to false, you should NOT set any trigger
     *
     * @return void
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $oSnippetDirectory = $this->getSnippetDirectory();

        $aDirTree = $oSnippetDirectory->getDirTree(true);
        $oVisitor->SetMappedValue('aTree', $aDirTree);
        $oVisitor->SetMappedValue('bHideNavigation', $this->bHideNavigation);
        $oVisitor->SetMappedValue('sActiveRelativePath', $this->sActiveRelativePath);
        $oVisitor->SetMappedValue('aSnippetList', $oSnippetDirectory->getSnippetList($aDirTree, $this->sActiveRelativePath));
    }

    private function getSnippetDirectory(): TPkgViewRendererSnippetDirectoryInterface
    {
        return ServiceLocator::get('chameleon_system_view_renderer.snippet_directory');
    }
}
