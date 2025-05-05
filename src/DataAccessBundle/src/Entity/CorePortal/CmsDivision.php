<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePortal;

use ChameleonSystem\DataAccessBundle\Entity\Core\CmsLanguage;
use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsMedia;
use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTree;

class CmsDivision
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsPortal|null - Belongs to portal / website */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldVarchar
        /** @var string - Area name */
        private string $name = '',
        // TCMSFieldNavigationTreeNode
        /** @var CmsTree|null - Navigation node */
        private ?CmsTree $cmsTreeIdTree = null,
        // TCMSFieldLookup
        /** @var CmsLanguage|null - Area language */
        private ?CmsLanguage $cmsLanguage = null,
        // TCMSFieldMedia
        /** @var array<string> - Images */
        private array $images = ['6', '6', '6', '6', '6', '6', '6', '6', '6', '6'],
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Background image */
        private ?CmsMedia $backgroundImage = null,
        // TCMSFieldColorpicker
        /** @var string - Main color */
        private string $colorPrimaryHexcolor = '',
        // TCMSFieldColorpicker
        /** @var string - Secondary color */
        private string $colorSecondaryHexcolor = '',
        // TCMSFieldColorpicker
        /** @var string - Tertiary color */
        private string $colorTertiaryHexcolor = '',
        // TCMSFieldPosition
        /** @var int - Position */
        private int $position = 0,
        // TCMSFieldOption
        /** @var string - Menu direction */
        private string $menuDirection = 'Rechts',
        // TCMSFieldText
        /** @var string - Keywords */
        private string $keywords = '',
        // TCMSFieldVarchar
        /** @var string - IVW code */
        private string $ivwCode = '',
        // TCMSFieldNumber
        /** @var int - Stop hover menu at this level */
        private int $menuStopLevel = 0
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
    public function getCmsPortal(): ?CmsPortal
    {
        return $this->cmsPortal;
    }

    public function setCmsPortal(?CmsPortal $cmsPortal): self
    {
        $this->cmsPortal = $cmsPortal;

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

    // TCMSFieldNavigationTreeNode
    public function getCmsTreeIdTree(): ?CmsTree
    {
        return $this->cmsTreeIdTree;
    }

    public function setCmsTreeIdTree(?CmsTree $cmsTreeIdTree): self
    {
        $this->cmsTreeIdTree = $cmsTreeIdTree;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsLanguage(): ?CmsLanguage
    {
        return $this->cmsLanguage;
    }

    public function setCmsLanguage(?CmsLanguage $cmsLanguage): self
    {
        $this->cmsLanguage = $cmsLanguage;

        return $this;
    }

    // TCMSFieldMedia
    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    // TCMSFieldExtendedLookupMedia
    public function getBackgroundImage(): ?CmsMedia
    {
        return $this->backgroundImage;
    }

    public function setBackgroundImage(?CmsMedia $backgroundImage): self
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

    // TCMSFieldColorpicker
    public function getColorPrimaryHexcolor(): string
    {
        return $this->colorPrimaryHexcolor;
    }

    public function setColorPrimaryHexcolor(string $colorPrimaryHexcolor): self
    {
        $this->colorPrimaryHexcolor = $colorPrimaryHexcolor;

        return $this;
    }

    // TCMSFieldColorpicker
    public function getColorSecondaryHexcolor(): string
    {
        return $this->colorSecondaryHexcolor;
    }

    public function setColorSecondaryHexcolor(string $colorSecondaryHexcolor): self
    {
        $this->colorSecondaryHexcolor = $colorSecondaryHexcolor;

        return $this;
    }

    // TCMSFieldColorpicker
    public function getColorTertiaryHexcolor(): string
    {
        return $this->colorTertiaryHexcolor;
    }

    public function setColorTertiaryHexcolor(string $colorTertiaryHexcolor): self
    {
        $this->colorTertiaryHexcolor = $colorTertiaryHexcolor;

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

    // TCMSFieldOption
    public function getMenuDirection(): string
    {
        return $this->menuDirection;
    }

    public function setMenuDirection(string $menuDirection): self
    {
        $this->menuDirection = $menuDirection;

        return $this;
    }

    // TCMSFieldText
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    // TCMSFieldVarchar
    public function getIvwCode(): string
    {
        return $this->ivwCode;
    }

    public function setIvwCode(string $ivwCode): self
    {
        $this->ivwCode = $ivwCode;

        return $this;
    }

    // TCMSFieldNumber
    public function getMenuStopLevel(): int
    {
        return $this->menuStopLevel;
    }

    public function setMenuStopLevel(int $menuStopLevel): self
    {
        $this->menuStopLevel = $menuStopLevel;

        return $this;
    }
}
