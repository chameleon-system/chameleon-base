<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreConfig;

class CmsConfigParameter
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsConfig|null - Belongs to CMS config */
        private ?CmsConfig $cmsConfig = null,
        // TCMSFieldVarchar
        /** @var string - System name */
        private string $systemname = '',
        // TCMSFieldVarchar
        /** @var string - Name / description */
        private string $name = '',
        // TCMSFieldText
        /** @var string - Value */
        private string $value = ''
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
    public function getCmsConfig(): ?CmsConfig
    {
        return $this->cmsConfig;
    }

    public function setCmsConfig(?CmsConfig $cmsConfig): self
    {
        $this->cmsConfig = $cmsConfig;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSystemname(): string
    {
        return $this->systemname;
    }

    public function setSystemname(string $systemname): self
    {
        $this->systemname = $systemname;

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

    // TCMSFieldText
    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
