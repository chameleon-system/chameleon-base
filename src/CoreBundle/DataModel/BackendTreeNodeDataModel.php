<?php


namespace ChameleonSystem\CoreBundle\DataModel;


class BackendTreeNodeDataModel
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
     * @var string
     */
    private $childrenAjaxUrl = '';
    

    /**
     * @var BackendTreeNodeDataModel[]
     */
    private $children = [];

    public function __construct($id, $name, $cmsIdent, $childrenAjaxUrl)
    {
        $this->id = $id;
        $this->name = $name;
        $this->cmsIdent = $cmsIdent;
        $this->childrenAjaxUrl = $childrenAjaxUrl;
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
     * @param BackendTreeNodeDataModel[] $children
     */
    public function addChildren(BackendTreeNodeDataModel $treeNodeDataModel): void
    {
        $this->children[] = $treeNodeDataModel;
    }

    /**
     * @return string
     */
    public function getChildrenAjaxUrl(): string
    {
        return $this->childrenAjaxUrl;
    }

    /**
     * @param string $childrenAjaxUrl
     */
    public function setChildrenAjaxUrl(string $childrenAjaxUrl): void
    {
        $this->childrenAjaxUrl = $childrenAjaxUrl;
    }

}
