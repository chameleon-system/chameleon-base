<?php


namespace ChameleonSystem\CoreBundle\DataModel;

use JsonSerializable;

class BackendTreeNodeDataModel implements JsonSerializable
{
    /**
     * @var string
     */
    private $id = '';

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $cmsIdent = '';

    /**
     * @var BackendTreeNodeDataModel[]
     */
    private $children = [];

    /**
     * @var bool
     */
    private $childrenAjaxLoad = false;

    /**
     * @var string
     */
    private $type = '';


    /**
     * @var bool
     */
    private $selected = false;


    /**
     * @var bool
     */
    private $disabled = false;


    /**
     * @var bool
     */
    private $opened = false;

    /**
     * @var array
     */
    private $liAttr = [];

    /**
     * @var array
     */
    private $aAttr = [];


    public function __construct($id, $name, $cmsIdent)
    {
        $this->id = $id;
        $this->name = $name;
        $this->cmsIdent = $cmsIdent;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCmsIdent(): string
    {
        return $this->cmsIdent;
    }

    /**
     * @return BackendTreeNodeDataModel[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param BackendTreeNodeDataModel[] $children
     */
    public function addChildren(BackendTreeNodeDataModel $treeNodeDataModel): void
    {
        $this->children[] = $treeNodeDataModel;
    }

    /**
     * @return bool
     */
    public function isChildrenAjaxLoad(): bool
    {
        return $this->childrenAjaxLoad;
    }

    /**
     * @param bool $childrenAjaxLoad
     */
    public function setChildrenAjaxLoad(bool $childrenAjaxLoad): void
    {
        $this->childrenAjaxLoad = $childrenAjaxLoad;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

    /**
     * @param bool $selected
     */
    public function setSelected(bool $selected): void
    {
        $this->selected = $selected;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     */
    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }

    /**
     * @return bool
     */
    public function isOpened(): bool
    {
        return $this->opened;
    }

    /**
     * @param bool $opened
     */
    public function setOpened(bool $opened): void
    {
        $this->opened = $opened;
    }

    /**
     * @return array
     */
    public function getLiAttr(): array
    {
        return $this->liAttr;
    }

    /**
     * @param array $liAttr
     */
    public function setLiAttr(array $liAttr): void
    {
        $this->liAttr = $liAttr;
    }

    /**
     * @return array
     */
    public function getAAttr(): array
    {
        return $this->aAttr;
    }

    /**
     * @param array $aAttr
     */
    public function setAAttr(array $aAttr): void
    {
        $this->aAttr = $aAttr;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by json_encode,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $jsTreeItem = [
            'id' => $this->id,
            'text' => $this->name,
            'type' => $this->type,
            'state' => [
                'opened' => $this->opened,
                'disabled' => $this->disabled,
                'selected' => $this->selected
            ],
            'li_attr' => $this->liAttr,
            'a_attr' => $this->aAttr
        ];


        if ($this->isChildrenAjaxLoad()) {
            $jsTreeItem['children'] = $this->childrenAjaxLoad;
        } else {
            $jsTreeItem['children'] = $this->children;
        }

        return $jsTreeItem;
    }
}
