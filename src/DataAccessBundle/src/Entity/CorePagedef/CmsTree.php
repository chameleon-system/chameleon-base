<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePagedef;

use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsMedia;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsTree
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookup
        /** @var CmsTree|null - Is subnode of */
        private ?CmsTree $parent = null,
        // TCMSFieldNumber
        /** @var int - Nested set: left */
        private int $lft = 0,
        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldNumber
        /** @var int - Nested set: right */
        private int $rgt = 0,
        // TCMSFieldSEOURLTitle
        /** @var string - URL name */
        private string $urlname = '',
        // TCMSFieldBoolean
        /** @var bool - Hide */
        private bool $hidden = false,
        // TCMSFieldBoolean
        /** @var bool - Show restricted page in navigation */
        private bool $showExtranetPage = false,
        // TCMSFieldNumber
        /** @var int - Position */
        private int $entrySort = 0,
        // TCMSFieldURL
        /** @var string - External link */
        private string $link = '',
        // TCMSFieldBoolean
        /** @var bool - Open link in new window */
        private bool $linkTarget = false,
        // TCMSFieldTreePageAssignment
        /** @var string - Pages / layouts */
        private string $cmsTplPagePrimaryLink = '',
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Icon for navigation */
        private ?CmsMedia $naviIconCmsMedia = null,
        // TCMSFieldText
        /** @var string - Navigation path cache */
        private string $pathcache = '',
        // TCMSFieldModuleInstance
        /** @var CmsTplModuleInstance|null - Connect module to navigation */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldBoolean
        /** @var bool - SEO: no follow */
        private bool $seoNofollow = false,
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, CmsTplPage> - SEO: no follow - page exclusion list */
        private Collection $cmsTplPageCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - Hotkeys */
        private string $htmlAccessKey = '',
        // TCMSFieldVarchar
        /** @var string - CSS classes */
        private string $cssClasses = '',
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsTreeNode> - Connected pages */
        private Collection $cmsTreeNodeCollection = new ArrayCollection()
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCmsident(): ?int
    {
        return $this->cmsident;
    }

    public function setCmsident(int $cmsident): self
    {
        $this->cmsident = $cmsident;

        return $this;
    }

    // TCMSFieldLookup
    public function getParent(): ?CmsTree
    {
        return $this->parent;
    }

    public function setParent(?CmsTree $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    // TCMSFieldNumber
    public function getLft(): int
    {
        return $this->lft;
    }

    public function setLft(int $lft): self
    {
        $this->lft = $lft;

        return $this;
    }

    // TCMSFieldVarchar
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldNumber
    public function getRgt(): int
    {
        return $this->rgt;
    }

    public function setRgt(int $rgt): self
    {
        $this->rgt = $rgt;

        return $this;
    }

    // TCMSFieldSEOURLTitle
    public function getUrlname(): string
    {
        return $this->urlname;
    }

    public function setUrlname(string $urlname): self
    {
        $this->urlname = $urlname;

        return $this;
    }

    // TCMSFieldBoolean
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    // TCMSFieldBoolean
    public function isShowExtranetPage(): bool
    {
        return $this->showExtranetPage;
    }

    public function setShowExtranetPage(bool $showExtranetPage): self
    {
        $this->showExtranetPage = $showExtranetPage;

        return $this;
    }

    // TCMSFieldNumber
    public function getEntrySort(): int
    {
        return $this->entrySort;
    }

    public function setEntrySort(int $entrySort): self
    {
        $this->entrySort = $entrySort;

        return $this;
    }

    // TCMSFieldURL
    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    // TCMSFieldBoolean
    public function isLinkTarget(): bool
    {
        return $this->linkTarget;
    }

    public function setLinkTarget(bool $linkTarget): self
    {
        $this->linkTarget = $linkTarget;

        return $this;
    }

    // TCMSFieldTreePageAssignment
    public function getCmsTplPagePrimaryLink(): string
    {
        return $this->cmsTplPagePrimaryLink;
    }

    public function setCmsTplPagePrimaryLink(string $cmsTplPagePrimaryLink): self
    {
        $this->cmsTplPagePrimaryLink = $cmsTplPagePrimaryLink;

        return $this;
    }

    // TCMSFieldExtendedLookupMedia
    public function getNaviIconCmsMedia(): ?CmsMedia
    {
        return $this->naviIconCmsMedia;
    }

    public function setNaviIconCmsMedia(?CmsMedia $naviIconCmsMedia): self
    {
        $this->naviIconCmsMedia = $naviIconCmsMedia;

        return $this;
    }

    // TCMSFieldText
    public function getPathcache(): string
    {
        return $this->pathcache;
    }

    public function setPathcache(string $pathcache): self
    {
        $this->pathcache = $pathcache;

        return $this;
    }

    // TCMSFieldModuleInstance
    public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
    {
        return $this->cmsTplModuleInstance;
    }

    public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
    {
        $this->cmsTplModuleInstance = $cmsTplModuleInstance;

        return $this;
    }

    // TCMSFieldBoolean
    public function isSeoNofollow(): bool
    {
        return $this->seoNofollow;
    }

    public function setSeoNofollow(bool $seoNofollow): self
    {
        $this->seoNofollow = $seoNofollow;

        return $this;
    }

    // TCMSFieldLookupMultiselect

    /**
     * @return Collection<int, CmsTplPage>
     */
    public function getCmsTplPageCollection(): Collection
    {
        return $this->cmsTplPageCollection;
    }

    public function addCmsTplPageCollection(CmsTplPage $cmsTplPageMlt): self
    {
        if (!$this->cmsTplPageCollection->contains($cmsTplPageMlt)) {
            $this->cmsTplPageCollection->add($cmsTplPageMlt);
            $cmsTplPageMlt->set($this);
        }

        return $this;
    }

    public function removeCmsTplPageCollection(CmsTplPage $cmsTplPageMlt): self
    {
        if ($this->cmsTplPageCollection->removeElement($cmsTplPageMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsTplPageMlt->get() === $this) {
                $cmsTplPageMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldVarchar
    public function getHtmlAccessKey(): string
    {
        return $this->htmlAccessKey;
    }

    public function setHtmlAccessKey(string $htmlAccessKey): self
    {
        $this->htmlAccessKey = $htmlAccessKey;

        return $this;
    }

    // TCMSFieldVarchar
    public function getCssClasses(): string
    {
        return $this->cssClasses;
    }

    public function setCssClasses(string $cssClasses): self
    {
        $this->cssClasses = $cssClasses;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsTreeNode>
     */
    public function getCmsTreeNodeCollection(): Collection
    {
        return $this->cmsTreeNodeCollection;
    }

    public function addCmsTreeNodeCollection(CmsTreeNode $cmsTreeNode): self
    {
        if (!$this->cmsTreeNodeCollection->contains($cmsTreeNode)) {
            $this->cmsTreeNodeCollection->add($cmsTreeNode);
            $cmsTreeNode->setCmsTree($this);
        }

        return $this;
    }

    public function removeCmsTreeNodeCollection(CmsTreeNode $cmsTreeNode): self
    {
        if ($this->cmsTreeNodeCollection->removeElement($cmsTreeNode)) {
            // set the owning side to null (unless already changed)
            if ($cmsTreeNode->getCmsTree() === $this) {
                $cmsTreeNode->setCmsTree(null);
            }
        }

        return $this;
    }
}
