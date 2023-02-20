<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class CmsMasterPagedef {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Layout */
private string $layout = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsMasterPagedefSpot> - Spots */
private Collection $cmsMasterPagedefSpotCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - WYSIWYG CSS URL */
private string $wysiwygCssUrl = ''  ) {}

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
public function getLayout(): string
{
    return $this->layout;
}
public function setLayout(string $layout): self
{
    $this->layout = $layout;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsMasterPagedefSpot>
*/
public function getCmsMasterPagedefSpotCollection(): Collection
{
    return $this->cmsMasterPagedefSpotCollection;
}

public function addCmsMasterPagedefSpotCollection(cmsMasterPagedefSpot $cmsMasterPagedefSpot): self
{
    if (!$this->cmsMasterPagedefSpotCollection->contains($cmsMasterPagedefSpot)) {
        $this->cmsMasterPagedefSpotCollection->add($cmsMasterPagedefSpot);
        $cmsMasterPagedefSpot->setCmsMasterPagedef($this);
    }

    return $this;
}

public function removeCmsMasterPagedefSpotCollection(cmsMasterPagedefSpot $cmsMasterPagedefSpot): self
{
    if ($this->cmsMasterPagedefSpotCollection->removeElement($cmsMasterPagedefSpot)) {
        // set the owning side to null (unless already changed)
        if ($cmsMasterPagedefSpot->getCmsMasterPagedef() === $this) {
            $cmsMasterPagedefSpot->setCmsMasterPagedef(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getWysiwygCssUrl(): string
{
    return $this->wysiwygCssUrl;
}
public function setWysiwygCssUrl(string $wysiwygCssUrl): self
{
    $this->wysiwygCssUrl = $wysiwygCssUrl;

    return $this;
}


  
}
