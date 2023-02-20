<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;

class PkgCommentModuleConfig {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsTplModuleInstance|null - Belongs to module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Comments per page */
private string $numberOfCommentsPerPage = '20', 
    // TCMSFieldVarchar
/** @var string - Display if comment is deleted */
private string $commentOnDelete = 'Dieser Kommentar wurde gelöscht'  ) {}

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
    // TCMSFieldLookupParentID
public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
{
    return $this->cmsTplModuleInstance;
}

public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;

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
public function getNumberOfCommentsPerPage(): string
{
    return $this->numberOfCommentsPerPage;
}
public function setNumberOfCommentsPerPage(string $numberOfCommentsPerPage): self
{
    $this->numberOfCommentsPerPage = $numberOfCommentsPerPage;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCommentOnDelete(): string
{
    return $this->commentOnDelete;
}
public function setCommentOnDelete(string $commentOnDelete): self
{
    $this->commentOnDelete = $commentOnDelete;

    return $this;
}


  
}
