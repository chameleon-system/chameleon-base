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
     * @return bool
     */
    public function isChildrenAjaxLoad(): bool
    {
        return $this->childrenAjaxLoad;
    }

    /**
     * @param BackendTreeNodeDataModel[] $children
     */
    public function addChildren(BackendTreeNodeDataModel $treeNodeDataModel): void
    {
        $this->children[] = $treeNodeDataModel;
    }

    /**
     * @param bool $childrenAjaxLoad
     */
    public function addChildrenAjaxLoad(bool $childrenAjaxLoad): void
    {
        $this->childrenAjaxLoad = $childrenAjaxLoad;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        if ($this->isChildrenAjaxLoad()) {
            return
                [
                    'id' => $this->id,
                    'text' => $this->name,
                    'children' => $this->childrenAjaxLoad
                ];
        }

        return
        [
            'id' => $this->id,
            'text' => $this->name,
            'children' => $this->children
        ];
    }
}
