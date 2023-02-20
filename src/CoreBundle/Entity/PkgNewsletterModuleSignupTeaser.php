<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterModuleSignupTeaser {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Belongs to module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Belongs to module instance */
private ?string $cmsTplModuleInstanceId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Login takes place via the following instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $configForSignupModuleInstance = null,
/** @var null|string - Login takes place via the following instance */
private ?string $configForSignupModuleInstanceId = null
, 
    // TCMSFieldVarchar
/** @var string - Heading */
private string $name = '', 
    // TCMSFieldWYSIWYG
/** @var string - Introduction */
private string $intro = ''  ) {}

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
    // TCMSFieldLookup
public function getCmsTplModuleInstance(): \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null
{
    return $this->cmsTplModuleInstance;
}
public function setCmsTplModuleInstance(\ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstance?->getId();

    return $this;
}
public function getCmsTplModuleInstanceId(): ?string
{
    return $this->cmsTplModuleInstanceId;
}
public function setCmsTplModuleInstanceId(?string $cmsTplModuleInstanceId): self
{
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstanceId;
    // todo - load new id
    //$this->cmsTplModuleInstanceId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getConfigForSignupModuleInstance(): \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null
{
    return $this->configForSignupModuleInstance;
}
public function setConfigForSignupModuleInstance(\ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $configForSignupModuleInstance): self
{
    $this->configForSignupModuleInstance = $configForSignupModuleInstance;
    $this->configForSignupModuleInstanceId = $configForSignupModuleInstance?->getId();

    return $this;
}
public function getConfigForSignupModuleInstanceId(): ?string
{
    return $this->configForSignupModuleInstanceId;
}
public function setConfigForSignupModuleInstanceId(?string $configForSignupModuleInstanceId): self
{
    $this->configForSignupModuleInstanceId = $configForSignupModuleInstanceId;
    // todo - load new id
    //$this->configForSignupModuleInstanceId = $?->getId();

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


  
    // TCMSFieldWYSIWYG
public function getIntro(): string
{
    return $this->intro;
}
public function setIntro(string $intro): self
{
    $this->intro = $intro;

    return $this;
}


  
}
