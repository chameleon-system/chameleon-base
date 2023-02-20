<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTplPage {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef|null - Page template */
private \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef|null $cmsMasterPagedef = null,
/** @var null|string - Page template */
private ?string $cmsMasterPagedefId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Belongs to portal / website */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Belongs to portal / website */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Background image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $backgroundImage = null,
/** @var null|string - Background image */
private ?string $backgroundImageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUser|null - Created by */
private \ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser = null,
/** @var null|string - Created by */
private ?string $cmsUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null - Content language */
private \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage = null,
/** @var null|string - Content language */
private ?string $cmsLanguageId = null
, 
    // TCMSFieldText
/** @var string - Navigation path image for searches */
private string $treePathSearchString = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplPageCmsMasterPagedefSpot[] - Spots */
private \Doctrine\Common\Collections\Collection $cmsTplPageCmsMasterPagedefSpotCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Page name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - SEO pattern */
private string $seoPattern = '', 
    // TCMSFieldPageTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Primary navigation tree node */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $primaryTreeIdHidden = null, 
    // TCMSFieldMedia
/** @var array<string> - Page image */
private array $images = [], 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup[] - Additional authorized groups */
private \Doctrine\Common\Collections\Collection $cmsUsergroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
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
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] - Restrict to the following extranet groups */
private \Doctrine\Common\Collections\Collection $dataExtranetGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - IVW page code */
private string $ivwCode = '', 
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
private int $metaRevisitAfter = 0  ) {}

  public function getId(): ?string
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
public function getCmsMasterPagedef(): \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef|null
{
    return $this->cmsMasterPagedef;
}
public function setCmsMasterPagedef(\ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef|null $cmsMasterPagedef): self
{
    $this->cmsMasterPagedef = $cmsMasterPagedef;
    $this->cmsMasterPagedefId = $cmsMasterPagedef?->getId();

    return $this;
}
public function getCmsMasterPagedefId(): ?string
{
    return $this->cmsMasterPagedefId;
}
public function setCmsMasterPagedefId(?string $cmsMasterPagedefId): self
{
    $this->cmsMasterPagedefId = $cmsMasterPagedefId;
    // todo - load new id
    //$this->cmsMasterPagedefId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getCmsTplPageCmsMasterPagedefSpotCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTplPageCmsMasterPagedefSpotCollection;
}
public function setCmsTplPageCmsMasterPagedefSpotCollection(\Doctrine\Common\Collections\Collection $cmsTplPageCmsMasterPagedefSpotCollection): self
{
    $this->cmsTplPageCmsMasterPagedefSpotCollection = $cmsTplPageCmsMasterPagedefSpotCollection;

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
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

    return $this;
}



  
    // TCMSFieldPageTreeNode
public function getPrimaryTreeIdHidden(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->primaryTreeIdHidden;
}
public function setPrimaryTreeIdHidden(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $primaryTreeIdHidden): self
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


  
    // TCMSFieldLookup
public function getBackgroundImage(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->backgroundImage;
}
public function setBackgroundImage(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $backgroundImage): self
{
    $this->backgroundImage = $backgroundImage;
    $this->backgroundImageId = $backgroundImage?->getId();

    return $this;
}
public function getBackgroundImageId(): ?string
{
    return $this->backgroundImageId;
}
public function setBackgroundImageId(?string $backgroundImageId): self
{
    $this->backgroundImageId = $backgroundImageId;
    // todo - load new id
    //$this->backgroundImageId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookupMultiselect
public function getCmsUsergroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsUsergroupMlt;
}
public function setCmsUsergroupMlt(\Doctrine\Common\Collections\Collection $cmsUsergroupMlt): self
{
    $this->cmsUsergroupMlt = $cmsUsergroupMlt;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsUser(): \ChameleonSystem\CoreBundle\Entity\CmsUser|null
{
    return $this->cmsUser;
}
public function setCmsUser(\ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser): self
{
    $this->cmsUser = $cmsUser;
    $this->cmsUserId = $cmsUser?->getId();

    return $this;
}
public function getCmsUserId(): ?string
{
    return $this->cmsUserId;
}
public function setCmsUserId(?string $cmsUserId): self
{
    $this->cmsUserId = $cmsUserId;
    // todo - load new id
    //$this->cmsUserId = $?->getId();

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
public function getDataExtranetGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetGroupMlt;
}
public function setDataExtranetGroupMlt(\Doctrine\Common\Collections\Collection $dataExtranetGroupMlt): self
{
    $this->dataExtranetGroupMlt = $dataExtranetGroupMlt;

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
public function getCmsLanguage(): \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null
{
    return $this->cmsLanguage;
}
public function setCmsLanguage(\ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage): self
{
    $this->cmsLanguage = $cmsLanguage;
    $this->cmsLanguageId = $cmsLanguage?->getId();

    return $this;
}
public function getCmsLanguageId(): ?string
{
    return $this->cmsLanguageId;
}
public function setCmsLanguageId(?string $cmsLanguageId): self
{
    $this->cmsLanguageId = $cmsLanguageId;
    // todo - load new id
    //$this->cmsLanguageId = $?->getId();

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
