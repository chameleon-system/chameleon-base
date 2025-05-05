<?php

namespace ChameleonSystem\NewsletterBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;

class PkgNewsletterModuleSignupTeaser
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsTplModuleInstance|null - Belongs to module instance */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldModuleInstance
        /** @var CmsTplModuleInstance|null - Login takes place via the following instance */
        private ?CmsTplModuleInstance $configForSignupModuleInstance = null,
        // TCMSFieldVarchar
        /** @var string - Heading */
        private string $name = '',
        // TCMSFieldWYSIWYG
        /** @var string - Introduction */
        private string $intro = ''
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

    // TCMSFieldModuleInstance
    public function getConfigForSignupModuleInstance(): ?CmsTplModuleInstance
    {
        return $this->configForSignupModuleInstance;
    }

    public function setConfigForSignupModuleInstance(?CmsTplModuleInstance $configForSignupModuleInstance): self
    {
        $this->configForSignupModuleInstance = $configForSignupModuleInstance;

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
