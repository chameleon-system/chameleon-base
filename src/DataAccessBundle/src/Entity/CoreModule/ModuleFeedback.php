<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreModule;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;

class ModuleFeedback
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookup
        /** @var CmsTplModuleInstance|null - Belongs to module */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldVarchar
        /** @var string - Headline */
        private string $name = '',
        // TCMSFieldWYSIWYG
        /** @var string - Text */
        private string $text = '',
        // TCMSFieldWYSIWYG
        /** @var string - Closing text */
        private string $doneText = '',
        // TCMSFieldVarchar
        /** @var string - Feedback recipient (email address) */
        private string $toEmail = '',
        // TCMSFieldText
        /** @var string - Feedback blind copy recipient (email address) */
        private string $bccEmail = '',
        // TCMSFieldVarchar
        /** @var string - Sender (email address) */
        private string $fromEmail = '',
        // TCMSFieldVarchar
        /** @var string - Default subject */
        private string $defaultSubject = '',
        // TCMSFieldText
        /** @var string - Default text */
        private string $defaultBody = ''
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

    // TCMSFieldLookup
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

    // TCMSFieldWYSIWYG
    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getDoneText(): string
    {
        return $this->doneText;
    }

    public function setDoneText(string $doneText): self
    {
        $this->doneText = $doneText;

        return $this;
    }

    // TCMSFieldVarchar
    public function getToEmail(): string
    {
        return $this->toEmail;
    }

    public function setToEmail(string $toEmail): self
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    // TCMSFieldText
    public function getBccEmail(): string
    {
        return $this->bccEmail;
    }

    public function setBccEmail(string $bccEmail): self
    {
        $this->bccEmail = $bccEmail;

        return $this;
    }

    // TCMSFieldVarchar
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function setFromEmail(string $fromEmail): self
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    // TCMSFieldVarchar
    public function getDefaultSubject(): string
    {
        return $this->defaultSubject;
    }

    public function setDefaultSubject(string $defaultSubject): self
    {
        $this->defaultSubject = $defaultSubject;

        return $this;
    }

    // TCMSFieldText
    public function getDefaultBody(): string
    {
        return $this->defaultBody;
    }

    public function setDefaultBody(string $defaultBody): self
    {
        $this->defaultBody = $defaultBody;

        return $this;
    }
}
