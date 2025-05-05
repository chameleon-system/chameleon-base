<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreConfig;

class CmsConfigCmsmoduleExtensions
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Overwritten by */
        private string $newclass = '',
        // TCMSFieldLookupParentID
        /** @var CmsConfig|null - Belongs to cms config */
        private ?CmsConfig $cmsConfig = null,
        // TCMSFieldVarchar
        /** @var string - Module to overwrite */
        private string $name = '',
        // TCMSFieldOption
        /** @var string - Type */
        private string $type = 'Customer'
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

    // TCMSFieldVarchar
    public function getNewclass(): string
    {
        return $this->newclass;
    }

    public function setNewclass(string $newclass): self
    {
        $this->newclass = $newclass;

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
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldOption
    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
