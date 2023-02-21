<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsPortal;

class DataMailProfile {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - ID code */
private string $idcode = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Subject */
private string $subject = '', 
    // TCMSFieldVarchar
/** @var string - Recipient email address */
private string $mailto = '', 
    // TCMSFieldVarchar
/** @var string - Recipient name */
private string $mailtoName = '', 
    // TCMSFieldVarchar
/** @var string - Sender email address */
private string $mailfrom = '', 
    // TCMSFieldVarchar
/** @var string - Sender name */
private string $mailfromName = '', 
    // TCMSFieldVarchar
/** @var string - Template */
private string $template = '', 
    // TCMSFieldVarchar
/** @var string - Text template */
private string $templateText = '', 
    // TCMSFieldLookup
/** @var CmsPortal|null - Belongs to portal */
private ?CmsPortal $cmsPortal = null
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
public function getIdcode(): string
{
    return $this->idcode;
}
public function setIdcode(string $idcode): self
{
    $this->idcode = $idcode;

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
public function getSubject(): string
{
    return $this->subject;
}
public function setSubject(string $subject): self
{
    $this->subject = $subject;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMailto(): string
{
    return $this->mailto;
}
public function setMailto(string $mailto): self
{
    $this->mailto = $mailto;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMailtoName(): string
{
    return $this->mailtoName;
}
public function setMailtoName(string $mailtoName): self
{
    $this->mailtoName = $mailtoName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMailfrom(): string
{
    return $this->mailfrom;
}
public function setMailfrom(string $mailfrom): self
{
    $this->mailfrom = $mailfrom;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMailfromName(): string
{
    return $this->mailfromName;
}
public function setMailfromName(string $mailfromName): self
{
    $this->mailfromName = $mailfromName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTemplate(): string
{
    return $this->template;
}
public function setTemplate(string $template): self
{
    $this->template = $template;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTemplateText(): string
{
    return $this->templateText;
}
public function setTemplateText(string $templateText): self
{
    $this->templateText = $templateText;

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


  
}
