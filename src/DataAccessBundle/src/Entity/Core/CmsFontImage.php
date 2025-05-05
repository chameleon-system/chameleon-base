<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class CmsFontImage
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Profile name */
        private string $profileName = '',
        // TCMSFieldNumber
        /** @var int - Image height */
        private int $imgHeight = -1,
        // TCMSFieldNumber
        /** @var int - Image width */
        private int $imgWidth = -1,
        // TCMSFieldVarchar
        /** @var string - Image background color */
        private string $imgBackgroundColor = '-1',
        // TCMSFieldVarchar
        /** @var string - Font color */
        private string $fontColor = '',
        // TCMSFieldNumber
        /** @var int - Font size */
        private int $fontSize = 0,
        // TCMSFieldOption
        /** @var string - Font width */
        private string $fontWeight = 'normal',
        // TCMSFieldOption
        /** @var string - Font alignment */
        private string $fontAlign = 'left',
        // TCMSFieldOption
        /** @var string - Font alignment vertical */
        private string $fontVerticalAlign = 'top',
        // TCMSFieldVarchar
        /** @var string - Font file */
        private string $fontFilename = '',
        // TCMSFieldOption
        /** @var string - Image type */
        private string $imgType = 'png',
        // TCMSFieldBoolean
        /** @var bool - With background image */
        private bool $imgBackgroundImg = false,
        // TCMSFieldVarchar
        /** @var string - Background image file */
        private string $backgroundImgFile = '',
        // TCMSFieldNumber
        /** @var int - Text position X-axis */
        private int $textPositionX = 0,
        // TCMSFieldNumber
        /** @var int - Text position Y-axis */
        private int $textPositionY = 0
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

    // TCMSFieldVarchar
    public function getProfileName(): string
    {
        return $this->profileName;
    }

    public function setProfileName(string $profileName): self
    {
        $this->profileName = $profileName;

        return $this;
    }

    // TCMSFieldNumber
    public function getImgHeight(): int
    {
        return $this->imgHeight;
    }

    public function setImgHeight(int $imgHeight): self
    {
        $this->imgHeight = $imgHeight;

        return $this;
    }

    // TCMSFieldNumber
    public function getImgWidth(): int
    {
        return $this->imgWidth;
    }

    public function setImgWidth(int $imgWidth): self
    {
        $this->imgWidth = $imgWidth;

        return $this;
    }

    // TCMSFieldVarchar
    public function getImgBackgroundColor(): string
    {
        return $this->imgBackgroundColor;
    }

    public function setImgBackgroundColor(string $imgBackgroundColor): self
    {
        $this->imgBackgroundColor = $imgBackgroundColor;

        return $this;
    }

    // TCMSFieldVarchar
    public function getFontColor(): string
    {
        return $this->fontColor;
    }

    public function setFontColor(string $fontColor): self
    {
        $this->fontColor = $fontColor;

        return $this;
    }

    // TCMSFieldNumber
    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    public function setFontSize(int $fontSize): self
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    // TCMSFieldOption
    public function getFontWeight(): string
    {
        return $this->fontWeight;
    }

    public function setFontWeight(string $fontWeight): self
    {
        $this->fontWeight = $fontWeight;

        return $this;
    }

    // TCMSFieldOption
    public function getFontAlign(): string
    {
        return $this->fontAlign;
    }

    public function setFontAlign(string $fontAlign): self
    {
        $this->fontAlign = $fontAlign;

        return $this;
    }

    // TCMSFieldOption
    public function getFontVerticalAlign(): string
    {
        return $this->fontVerticalAlign;
    }

    public function setFontVerticalAlign(string $fontVerticalAlign): self
    {
        $this->fontVerticalAlign = $fontVerticalAlign;

        return $this;
    }

    // TCMSFieldVarchar
    public function getFontFilename(): string
    {
        return $this->fontFilename;
    }

    public function setFontFilename(string $fontFilename): self
    {
        $this->fontFilename = $fontFilename;

        return $this;
    }

    // TCMSFieldOption
    public function getImgType(): string
    {
        return $this->imgType;
    }

    public function setImgType(string $imgType): self
    {
        $this->imgType = $imgType;

        return $this;
    }

    // TCMSFieldBoolean
    public function isImgBackgroundImg(): bool
    {
        return $this->imgBackgroundImg;
    }

    public function setImgBackgroundImg(bool $imgBackgroundImg): self
    {
        $this->imgBackgroundImg = $imgBackgroundImg;

        return $this;
    }

    // TCMSFieldVarchar
    public function getBackgroundImgFile(): string
    {
        return $this->backgroundImgFile;
    }

    public function setBackgroundImgFile(string $backgroundImgFile): self
    {
        $this->backgroundImgFile = $backgroundImgFile;

        return $this;
    }

    // TCMSFieldNumber
    public function getTextPositionX(): int
    {
        return $this->textPositionX;
    }

    public function setTextPositionX(int $textPositionX): self
    {
        $this->textPositionX = $textPositionX;

        return $this;
    }

    // TCMSFieldNumber
    public function getTextPositionY(): int
    {
        return $this->textPositionY;
    }

    public function setTextPositionY(int $textPositionY): self
    {
        $this->textPositionY = $textPositionY;

        return $this;
    }
}
