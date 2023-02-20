<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsConfigImagemagick;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\CmsConfigParameter;
use ChameleonSystem\CoreBundle\Entity\CmsMessageManagerBackendMessage;
use ChameleonSystem\CoreBundle\Entity\CmsConfigCmsmoduleExtensions;
use ChameleonSystem\CoreBundle\Entity\CmsIpWhitelist;

class CmsConfig {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsConfigImagemagick> - ImageMagick settings */
private Collection $cmsConfigImagemagickCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Chunk size for uploader in KB */
private string $uploaderChunkSize = '1024', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsConfigParameter> - Configuration settings */
private Collection $cmsConfigParameterCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsMessageManagerBackendMessage> - System messages / error codes */
private Collection $cmsMessageManagerBackendMessageCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - SMTP server */
private string $smtpServer = '', 
    // TCMSFieldVarchar
/** @var string - SMTP user */
private string $smtpUser = '', 
    // TCMSFieldVarchar
/** @var string - SMTP password */
private string $smtpPassword = '', 
    // TCMSFieldVarchar
/** @var string - SMTP port */
private string $smtpPort = '25', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsConfigCmsmoduleExtensions> - CMS module extensions */
private Collection $cmsConfigCmsmoduleExtensionsCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsIpWhitelist> - Permitted IPs */
private Collection $cmsIpWhitelistCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Maximum file size of file uploads (in KB)  */
private string $maxDocumentUploadSize = '409600', 
    // TCMSFieldVarchar
/** @var string - CMS owner */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - WYSIWYG editor CSS file */
private string $wysiwygeditorCssUrl = '', 
    // TCMSFieldVarchar
/** @var string - Maximum image file size in kb */
private string $maxImageUploadSize = '1024', 
    // TCMSFieldVarchar
/** @var string - Update server */
private string $updateServer = '', 
    // TCMSFieldVarchar
/** @var string - Lines per page */
private string $entryPerPage = '', 
    // TCMSFieldVarchar
/** @var string - Database version */
private string $databaseversion = '0', 
    // TCMSFieldVarchar
/** @var string - Build no. */
private string $build = '1'  ) {}

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
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsConfigImagemagick>
*/
public function getCmsConfigImagemagickCollection(): Collection
{
    return $this->cmsConfigImagemagickCollection;
}

public function addCmsConfigImagemagickCollection(cmsConfigImagemagick $cmsConfigImagemagick): self
{
    if (!$this->cmsConfigImagemagickCollection->contains($cmsConfigImagemagick)) {
        $this->cmsConfigImagemagickCollection->add($cmsConfigImagemagick);
        $cmsConfigImagemagick->setCmsConfig($this);
    }

    return $this;
}

public function removeCmsConfigImagemagickCollection(cmsConfigImagemagick $cmsConfigImagemagick): self
{
    if ($this->cmsConfigImagemagickCollection->removeElement($cmsConfigImagemagick)) {
        // set the owning side to null (unless already changed)
        if ($cmsConfigImagemagick->getCmsConfig() === $this) {
            $cmsConfigImagemagick->setCmsConfig(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getUploaderChunkSize(): string
{
    return $this->uploaderChunkSize;
}
public function setUploaderChunkSize(string $uploaderChunkSize): self
{
    $this->uploaderChunkSize = $uploaderChunkSize;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsConfigParameter>
*/
public function getCmsConfigParameterCollection(): Collection
{
    return $this->cmsConfigParameterCollection;
}

public function addCmsConfigParameterCollection(cmsConfigParameter $cmsConfigParameter): self
{
    if (!$this->cmsConfigParameterCollection->contains($cmsConfigParameter)) {
        $this->cmsConfigParameterCollection->add($cmsConfigParameter);
        $cmsConfigParameter->setCmsConfig($this);
    }

    return $this;
}

public function removeCmsConfigParameterCollection(cmsConfigParameter $cmsConfigParameter): self
{
    if ($this->cmsConfigParameterCollection->removeElement($cmsConfigParameter)) {
        // set the owning side to null (unless already changed)
        if ($cmsConfigParameter->getCmsConfig() === $this) {
            $cmsConfigParameter->setCmsConfig(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsMessageManagerBackendMessage>
*/
public function getCmsMessageManagerBackendMessageCollection(): Collection
{
    return $this->cmsMessageManagerBackendMessageCollection;
}

public function addCmsMessageManagerBackendMessageCollection(cmsMessageManagerBackendMessage $cmsMessageManagerBackendMessage): self
{
    if (!$this->cmsMessageManagerBackendMessageCollection->contains($cmsMessageManagerBackendMessage)) {
        $this->cmsMessageManagerBackendMessageCollection->add($cmsMessageManagerBackendMessage);
        $cmsMessageManagerBackendMessage->setCmsConfig($this);
    }

    return $this;
}

public function removeCmsMessageManagerBackendMessageCollection(cmsMessageManagerBackendMessage $cmsMessageManagerBackendMessage): self
{
    if ($this->cmsMessageManagerBackendMessageCollection->removeElement($cmsMessageManagerBackendMessage)) {
        // set the owning side to null (unless already changed)
        if ($cmsMessageManagerBackendMessage->getCmsConfig() === $this) {
            $cmsMessageManagerBackendMessage->setCmsConfig(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getSmtpServer(): string
{
    return $this->smtpServer;
}
public function setSmtpServer(string $smtpServer): self
{
    $this->smtpServer = $smtpServer;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSmtpUser(): string
{
    return $this->smtpUser;
}
public function setSmtpUser(string $smtpUser): self
{
    $this->smtpUser = $smtpUser;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSmtpPassword(): string
{
    return $this->smtpPassword;
}
public function setSmtpPassword(string $smtpPassword): self
{
    $this->smtpPassword = $smtpPassword;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSmtpPort(): string
{
    return $this->smtpPort;
}
public function setSmtpPort(string $smtpPort): self
{
    $this->smtpPort = $smtpPort;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsConfigCmsmoduleExtensions>
*/
public function getCmsConfigCmsmoduleExtensionsCollection(): Collection
{
    return $this->cmsConfigCmsmoduleExtensionsCollection;
}

public function addCmsConfigCmsmoduleExtensionsCollection(cmsConfigCmsmoduleExtensions $cmsConfigCmsmoduleExtensions): self
{
    if (!$this->cmsConfigCmsmoduleExtensionsCollection->contains($cmsConfigCmsmoduleExtensions)) {
        $this->cmsConfigCmsmoduleExtensionsCollection->add($cmsConfigCmsmoduleExtensions);
        $cmsConfigCmsmoduleExtensions->setCmsConfig($this);
    }

    return $this;
}

public function removeCmsConfigCmsmoduleExtensionsCollection(cmsConfigCmsmoduleExtensions $cmsConfigCmsmoduleExtensions): self
{
    if ($this->cmsConfigCmsmoduleExtensionsCollection->removeElement($cmsConfigCmsmoduleExtensions)) {
        // set the owning side to null (unless already changed)
        if ($cmsConfigCmsmoduleExtensions->getCmsConfig() === $this) {
            $cmsConfigCmsmoduleExtensions->setCmsConfig(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsIpWhitelist>
*/
public function getCmsIpWhitelistCollection(): Collection
{
    return $this->cmsIpWhitelistCollection;
}

public function addCmsIpWhitelistCollection(cmsIpWhitelist $cmsIpWhitelist): self
{
    if (!$this->cmsIpWhitelistCollection->contains($cmsIpWhitelist)) {
        $this->cmsIpWhitelistCollection->add($cmsIpWhitelist);
        $cmsIpWhitelist->setCmsConfig($this);
    }

    return $this;
}

public function removeCmsIpWhitelistCollection(cmsIpWhitelist $cmsIpWhitelist): self
{
    if ($this->cmsIpWhitelistCollection->removeElement($cmsIpWhitelist)) {
        // set the owning side to null (unless already changed)
        if ($cmsIpWhitelist->getCmsConfig() === $this) {
            $cmsIpWhitelist->setCmsConfig(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getMaxDocumentUploadSize(): string
{
    return $this->maxDocumentUploadSize;
}
public function setMaxDocumentUploadSize(string $maxDocumentUploadSize): self
{
    $this->maxDocumentUploadSize = $maxDocumentUploadSize;

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
public function getWysiwygeditorCssUrl(): string
{
    return $this->wysiwygeditorCssUrl;
}
public function setWysiwygeditorCssUrl(string $wysiwygeditorCssUrl): self
{
    $this->wysiwygeditorCssUrl = $wysiwygeditorCssUrl;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMaxImageUploadSize(): string
{
    return $this->maxImageUploadSize;
}
public function setMaxImageUploadSize(string $maxImageUploadSize): self
{
    $this->maxImageUploadSize = $maxImageUploadSize;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUpdateServer(): string
{
    return $this->updateServer;
}
public function setUpdateServer(string $updateServer): self
{
    $this->updateServer = $updateServer;

    return $this;
}


  
    // TCMSFieldVarchar
public function getEntryPerPage(): string
{
    return $this->entryPerPage;
}
public function setEntryPerPage(string $entryPerPage): self
{
    $this->entryPerPage = $entryPerPage;

    return $this;
}


  
    // TCMSFieldVarchar
public function getDatabaseversion(): string
{
    return $this->databaseversion;
}
public function setDatabaseversion(string $databaseversion): self
{
    $this->databaseversion = $databaseversion;

    return $this;
}


  
    // TCMSFieldVarchar
public function getBuild(): string
{
    return $this->build;
}
public function setBuild(string $build): self
{
    $this->build = $build;

    return $this;
}


  
}
