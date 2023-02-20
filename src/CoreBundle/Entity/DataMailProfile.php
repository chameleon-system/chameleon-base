<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataMailProfile {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Belongs to portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Belongs to portal */
private ?string $cmsPortalId = null
, 
    // TCMSFieldVarchar
/** @var string - ID code */
private string $idcode = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Subject */
private string $subject = '', 
    // TCMSFieldEmail
/** @var string - Recipient email address */
private string $mailto = '', 
    // TCMSFieldVarchar
/** @var string - Recipient name */
private string $mailtoName = '', 
    // TCMSFieldEmail
/** @var string - Sender email address */
private string $mailfrom = '', 
    // TCMSFieldVarchar
/** @var string - Sender name */
private string $mailfromName = '', 
    // TCMSFieldText
/** @var string - BCC */
private string $mailbcc = '', 
    // TCMSFieldWYSIWYG
/** @var string - Body */
private string $body = '', 
    // TCMSFieldText
/** @var string - Body (text) */
private string $bodyText = '', 
    // TCMSFieldDownloads
/** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument[] - Attach the following files to the email */
private \Doctrine\Common\Collections\Collection $attachment = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Template */
private string $template = '', 
    // TCMSFieldVarchar
/** @var string - Text template */
private string $templateText = ''  ) {}

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


  
    // TCMSFieldEmail
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


  
    // TCMSFieldEmail
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


  
    // TCMSFieldText
public function getMailbcc(): string
{
    return $this->mailbcc;
}
public function setMailbcc(string $mailbcc): self
{
    $this->mailbcc = $mailbcc;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getBody(): string
{
    return $this->body;
}
public function setBody(string $body): self
{
    $this->body = $body;

    return $this;
}


  
    // TCMSFieldText
public function getBodyText(): string
{
    return $this->bodyText;
}
public function setBodyText(string $bodyText): self
{
    $this->bodyText = $bodyText;

    return $this;
}


  
    // TCMSFieldDownloads
public function getAttachment(): \Doctrine\Common\Collections\Collection
{
    return $this->attachment;
}
public function setAttachment(\Doctrine\Common\Collections\Collection $attachment): self
{
    $this->attachment = $attachment;

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



  
}
