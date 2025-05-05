<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration;

class CmsTblDisplayListFields
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Field name */
        private string $title = '',
        // TCMSFieldLookupParentID
        /** @var CmsTblConf|null - Belongs to table */
        private ?CmsTblConf $cmsTblConf = null,
        // TCMSFieldVarchar
        /** @var string - Database field name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Database field name of translation */
        private string $cmsTranslationFieldName = '',
        // TCMSFieldVarchar
        /** @var string - Field alias (abbreviated) */
        private string $dbAlias = '',
        // TCMSFieldPosition
        /** @var int - Position */
        private int $position = 0,
        // TCMSFieldNumber
        /** @var int - Column width */
        private int $width = -1,
        // TCMSFieldOption
        /** @var string - Orientation */
        private string $align = 'left',
        // TCMSFieldVarchar
        /** @var string - Call back function */
        private string $callbackFnc = '',
        // TCMSFieldBoolean
        /** @var bool - Activate call back functions */
        private bool $useCallback = false,
        // TCMSFieldBoolean
        /** @var bool - Show in list */
        private bool $showInList = true,
        // TCMSFieldBoolean
        /** @var bool - Show in sorting */
        private bool $showInSort = false
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
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getCmsTblConf(): ?CmsTblConf
    {
        return $this->cmsTblConf;
    }

    public function setCmsTblConf(?CmsTblConf $cmsTblConf): self
    {
        $this->cmsTblConf = $cmsTblConf;

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
    public function getCmsTranslationFieldName(): string
    {
        return $this->cmsTranslationFieldName;
    }

    public function setCmsTranslationFieldName(string $cmsTranslationFieldName): self
    {
        $this->cmsTranslationFieldName = $cmsTranslationFieldName;

        return $this;
    }

    // TCMSFieldVarchar
    public function getDbAlias(): string
    {
        return $this->dbAlias;
    }

    public function setDbAlias(string $dbAlias): self
    {
        $this->dbAlias = $dbAlias;

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

    // TCMSFieldNumber
    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    // TCMSFieldOption
    public function getAlign(): string
    {
        return $this->align;
    }

    public function setAlign(string $align): self
    {
        $this->align = $align;

        return $this;
    }

    // TCMSFieldVarchar
    public function getCallbackFnc(): string
    {
        return $this->callbackFnc;
    }

    public function setCallbackFnc(string $callbackFnc): self
    {
        $this->callbackFnc = $callbackFnc;

        return $this;
    }

    // TCMSFieldBoolean
    public function isUseCallback(): bool
    {
        return $this->useCallback;
    }

    public function setUseCallback(bool $useCallback): self
    {
        $this->useCallback = $useCallback;

        return $this;
    }

    // TCMSFieldBoolean
    public function isShowInList(): bool
    {
        return $this->showInList;
    }

    public function setShowInList(bool $showInList): self
    {
        $this->showInList = $showInList;

        return $this;
    }

    // TCMSFieldBoolean
    public function isShowInSort(): bool
    {
        return $this->showInSort;
    }

    public function setShowInSort(bool $showInSort): self
    {
        $this->showInSort = $showInSort;

        return $this;
    }
}
