<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMedia;

class ShopArticleMarker {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - System name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Title (as shown on the website) */
private string $title = '', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Icon */
private ?CmsMedia $cmsMedia = null
  ) {}

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
public function getCmsMedia(): ?CmsMedia
{
    return $this->cmsMedia;
}

public function setCmsMedia(?CmsMedia $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;

    return $this;
}


  
}
