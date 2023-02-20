<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleMarker {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Icon */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Icon */
private ?string $cmsMediaId = null
, 
    // TCMSFieldVarchar
/** @var string - System name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Title (as shown on the website) */
private string $title = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = ''  ) {}

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
public function getTitle(): string
{
    return $this->title;
}
public function setTitle(string $title): self
{
    $this->title = $title;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsMedia(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->cmsMedia;
}
public function setCmsMedia(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;
    $this->cmsMediaId = $cmsMedia?->getId();

    return $this;
}
public function getCmsMediaId(): ?string
{
    return $this->cmsMediaId;
}
public function setCmsMediaId(?string $cmsMediaId): self
{
    $this->cmsMediaId = $cmsMediaId;
    // todo - load new id
    //$this->cmsMediaId = $?->getId();

    return $this;
}



  
    // TCMSFieldWYSIWYG
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

    return $this;
}


  
}
