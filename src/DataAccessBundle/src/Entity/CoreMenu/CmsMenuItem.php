<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreMenu;

class CmsMenuItem
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldExtendedLookupMultiTable
        /** @var string - Target */
        private string $target = '',
        // TCMSFieldExtendedLookupMultiTable
        /** @var string - Target */
        private string $targetTableName = '',
        // TCMSFieldVarchar
        /** @var string - Icon font CSS class */
        private string $iconFontCssClass = '',
        // TCMSFieldPosition
        /** @var int - Position */
        private int $position = 0,
        // TCMSFieldLookupParentID
        /** @var CmsMenuCategory|null - CMS main menu category */
        private ?CmsMenuCategory $cmsMenuCategory = null
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
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldExtendedLookupMultiTable
    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    // TCMSFieldExtendedLookupMultiTable
    public function getTargetTableName(): string
    {
        return $this->targetTableName;
    }

    public function setTargetTableName(string $targetTableName): self
    {
        $this->targetTableName = $targetTableName;

        return $this;
    }

    // TCMSFieldVarchar
    public function getIconFontCssClass(): string
    {
        return $this->iconFontCssClass;
    }

    public function setIconFontCssClass(string $iconFontCssClass): self
    {
        $this->iconFontCssClass = $iconFontCssClass;

        return $this;
    }

    // TCMSFieldPosition
    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getCmsMenuCategory(): ?CmsMenuCategory
    {
        return $this->cmsMenuCategory;
    }

    public function setCmsMenuCategory(?CmsMenuCategory $cmsMenuCategory): self
    {
        $this->cmsMenuCategory = $cmsMenuCategory;

        return $this;
    }
}
