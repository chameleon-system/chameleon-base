<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePagedef;

use ChameleonSystem\DataAccessBundle\Entity\Core\CmsLanguage;
use ChameleonSystem\DataAccessBundle\Entity\Core\CmsUser;
use ChameleonSystem\DataAccessBundle\Entity\Core\CmsUsergroup;
use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsMedia;
use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use ChameleonSystem\ExtranetBundle\Entity\DataExtranetGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsTplPage
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldText
        /** @var string - Navigation path image for searches */
        private string $treePathSearchString = '',
        // TCMSFieldLookup
        /** @var CmsMasterPagedef|null - Page template */
        private ?CmsMasterPagedef $cmsMasterPagedef = null,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsTplPageCmsMasterPagedefSpot> - Spots */
        private Collection $cmsTplPageCmsMasterPagedefSpotCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - Page name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - SEO pattern */
        private string $seoPattern = '',
        // TCMSFieldLookup
        /** @var CmsPortal|null - Belongs to portal / website */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldPageTreeNode
        /** @var CmsTree|null - Primary navigation tree node */
        private ?CmsTree $primaryTreeIdHidden = null,
        // TCMSFieldMedia
        /** @var array<string> - Page image */
        private array $images = ['1', '1', '1', '1'],
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Background image */
        private ?CmsMedia $backgroundImage = null,
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, CmsUsergroup> - Additional authorized groups */
        private Collection $cmsUsergroupCollection = new ArrayCollection(),
        // TCMSFieldCMSUser
        /** @var CmsUser|null - Created by */
        private ?CmsUser $cmsUser = null,
        // TCMSFieldBoolean
        /** @var bool - Use SSL */
        private bool $usessl = false,
        // TCMSFieldBoolean
        /** @var bool - Restrict access */
        private bool $extranetPage = false,
        // TCMSFieldBoolean
        /** @var bool - Enable access for non-confirmed users */
        private bool $accessNotConfirmedUser = false,
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, DataExtranetGroup> - Restrict to the following extranet groups */
        private Collection $dataExtranetGroupCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - IVW page code */
        private string $ivwCode = '',
        // TCMSFieldLookup
        /** @var CmsLanguage|null - Content language */
        private ?CmsLanguage $cmsLanguage = null,
        // TCMSFieldVarchar
        /** @var string - Short description */
        private string $metaDescription = '',
        // TCMSFieldText
        /** @var string - Search terms */
        private string $metaKeywords = '',
        // TCMSFieldOption
        /** @var string - Keyword language */
        private string $metaKeywordLanguage = 'Deutsch',
        // TCMSFieldVarchar
        /** @var string - Author */
        private string $metaAuthor = '',
        // TCMSFieldVarchar
        /** @var string - Publisher */
        private string $metaPublisher = '',
        // TCMSFieldVarchar
        /** @var string - Topic */
        private string $metaPageTopic = '',
        // TCMSFieldOption
        /** @var string - Cacheable (pragma) */
        private string $metaPragma = 'no-cache',
        // TCMSFieldVarchar
        /** @var string - Robots */
        private string $metaRobots = 'index, follow',
        // TCMSFieldNumber
        /** @var int - Revisit */
        private int $metaRevisitAfter = 0
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

    // TCMSFieldText
    public function getTreePathSearchString(): string
    {
        return $this->treePathSearchString;
    }

    public function setTreePathSearchString(string $treePathSearchString): self
    {
        $this->treePathSearchString = $treePathSearchString;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsMasterPagedef(): ?CmsMasterPagedef
    {
        return $this->cmsMasterPagedef;
    }

    public function setCmsMasterPagedef(?CmsMasterPagedef $cmsMasterPagedef): self
    {
        $this->cmsMasterPagedef = $cmsMasterPagedef;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsTplPageCmsMasterPagedefSpot>
     */
    public function getCmsTplPageCmsMasterPagedefSpotCollection(): Collection
    {
        return $this->cmsTplPageCmsMasterPagedefSpotCollection;
    }

    public function addCmsTplPageCmsMasterPagedefSpotCollection(
        CmsTplPageCmsMasterPagedefSpot $cmsTplPageCmsMasterPagedefSpot
    ): self {
        if (!$this->cmsTplPageCmsMasterPagedefSpotCollection->contains($cmsTplPageCmsMasterPagedefSpot)) {
            $this->cmsTplPageCmsMasterPagedefSpotCollection->add($cmsTplPageCmsMasterPagedefSpot);
            $cmsTplPageCmsMasterPagedefSpot->setCmsTplPage($this);
        }

        return $this;
    }

    public function removeCmsTplPageCmsMasterPagedefSpotCollection(
        CmsTplPageCmsMasterPagedefSpot $cmsTplPageCmsMasterPagedefSpot
    ): self {
        if ($this->cmsTplPageCmsMasterPagedefSpotCollection->removeElement($cmsTplPageCmsMasterPagedefSpot)) {
            // set the owning side to null (unless already changed)
            if ($cmsTplPageCmsMasterPagedefSpot->getCmsTplPage() === $this) {
                $cmsTplPageCmsMasterPagedefSpot->setCmsTplPage(null);
            }
        }

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

    // TCMSFieldVarchar
    public function getSeoPattern(): string
    {
        return $this->seoPattern;
    }

    public function setSeoPattern(string $seoPattern): self
    {
        $this->seoPattern = $seoPattern;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsPortal(): ?CmsPortal
    {
        return $this->cmsPortal;
    }

    public function setCmsPortal(?CmsPortal $cmsPortal): self
    {
        $this->cmsPortal = $cmsPortal;

        return $this;
    }

    // TCMSFieldPageTreeNode
    public function getPrimaryTreeIdHidden(): ?CmsTree
    {
        return $this->primaryTreeIdHidden;
    }

    public function setPrimaryTreeIdHidden(?CmsTree $primaryTreeIdHidden): self
    {
        $this->primaryTreeIdHidden = $primaryTreeIdHidden;

        return $this;
    }

    // TCMSFieldMedia
    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    // TCMSFieldExtendedLookupMedia
    public function getBackgroundImage(): ?CmsMedia
    {
        return $this->backgroundImage;
    }

    public function setBackgroundImage(?CmsMedia $backgroundImage): self
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

    // TCMSFieldLookupMultiselect

    /**
     * @return Collection<int, CmsUsergroup>
     */
    public function getCmsUsergroupCollection(): Collection
    {
        return $this->cmsUsergroupCollection;
    }

    public function addCmsUsergroupCollection(CmsUsergroup $cmsUsergroupMlt): self
    {
        if (!$this->cmsUsergroupCollection->contains($cmsUsergroupMlt)) {
            $this->cmsUsergroupCollection->add($cmsUsergroupMlt);
            $cmsUsergroupMlt->set($this);
        }

        return $this;
    }

    public function removeCmsUsergroupCollection(CmsUsergroup $cmsUsergroupMlt): self
    {
        if ($this->cmsUsergroupCollection->removeElement($cmsUsergroupMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsUsergroupMlt->get() === $this) {
                $cmsUsergroupMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldCMSUser
    public function getCmsUser(): ?CmsUser
    {
        return $this->cmsUser;
    }

    public function setCmsUser(?CmsUser $cmsUser): self
    {
        $this->cmsUser = $cmsUser;

        return $this;
    }

    // TCMSFieldBoolean
    public function isUsessl(): bool
    {
        return $this->usessl;
    }

    public function setUsessl(bool $usessl): self
    {
        $this->usessl = $usessl;

        return $this;
    }

    // TCMSFieldBoolean
    public function isExtranetPage(): bool
    {
        return $this->extranetPage;
    }

    public function setExtranetPage(bool $extranetPage): self
    {
        $this->extranetPage = $extranetPage;

        return $this;
    }

    // TCMSFieldBoolean
    public function isAccessNotConfirmedUser(): bool
    {
        return $this->accessNotConfirmedUser;
    }

    public function setAccessNotConfirmedUser(bool $accessNotConfirmedUser): self
    {
        $this->accessNotConfirmedUser = $accessNotConfirmedUser;

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, DataExtranetGroup>
     */
    public function getDataExtranetGroupCollection(): Collection
    {
        return $this->dataExtranetGroupCollection;
    }

    public function addDataExtranetGroupCollection(DataExtranetGroup $dataExtranetGroupMlt): self
    {
        if (!$this->dataExtranetGroupCollection->contains($dataExtranetGroupMlt)) {
            $this->dataExtranetGroupCollection->add($dataExtranetGroupMlt);
            $dataExtranetGroupMlt->set($this);
        }

        return $this;
    }

    public function removeDataExtranetGroupCollection(DataExtranetGroup $dataExtranetGroupMlt): self
    {
        if ($this->dataExtranetGroupCollection->removeElement($dataExtranetGroupMlt)) {
            // set the owning side to null (unless already changed)
            if ($dataExtranetGroupMlt->get() === $this) {
                $dataExtranetGroupMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldVarchar
    public function getIvwCode(): string
    {
        return $this->ivwCode;
    }

    public function setIvwCode(string $ivwCode): self
    {
        $this->ivwCode = $ivwCode;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsLanguage(): ?CmsLanguage
    {
        return $this->cmsLanguage;
    }

    public function setCmsLanguage(?CmsLanguage $cmsLanguage): self
    {
        $this->cmsLanguage = $cmsLanguage;

        return $this;
    }

    // TCMSFieldVarchar
    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    // TCMSFieldText
    public function getMetaKeywords(): string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(string $metaKeywords): self
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    // TCMSFieldOption
    public function getMetaKeywordLanguage(): string
    {
        return $this->metaKeywordLanguage;
    }

    public function setMetaKeywordLanguage(string $metaKeywordLanguage): self
    {
        $this->metaKeywordLanguage = $metaKeywordLanguage;

        return $this;
    }

    // TCMSFieldVarchar
    public function getMetaAuthor(): string
    {
        return $this->metaAuthor;
    }

    public function setMetaAuthor(string $metaAuthor): self
    {
        $this->metaAuthor = $metaAuthor;

        return $this;
    }

    // TCMSFieldVarchar
    public function getMetaPublisher(): string
    {
        return $this->metaPublisher;
    }

    public function setMetaPublisher(string $metaPublisher): self
    {
        $this->metaPublisher = $metaPublisher;

        return $this;
    }

    // TCMSFieldVarchar
    public function getMetaPageTopic(): string
    {
        return $this->metaPageTopic;
    }

    public function setMetaPageTopic(string $metaPageTopic): self
    {
        $this->metaPageTopic = $metaPageTopic;

        return $this;
    }

    // TCMSFieldOption
    public function getMetaPragma(): string
    {
        return $this->metaPragma;
    }

    public function setMetaPragma(string $metaPragma): self
    {
        $this->metaPragma = $metaPragma;

        return $this;
    }

    // TCMSFieldVarchar
    public function getMetaRobots(): string
    {
        return $this->metaRobots;
    }

    public function setMetaRobots(string $metaRobots): self
    {
        $this->metaRobots = $metaRobots;

        return $this;
    }

    // TCMSFieldNumber
    public function getMetaRevisitAfter(): int
    {
        return $this->metaRevisitAfter;
    }

    public function setMetaRevisitAfter(int $metaRevisitAfter): self
    {
        $this->metaRevisitAfter = $metaRevisitAfter;

        return $this;
    }
}
