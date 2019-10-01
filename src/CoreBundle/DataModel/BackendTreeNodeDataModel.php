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
     * @var BackendTreeNodeDataModel[]
     */
    private $children = [];

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
     * @param BackendTreeNodeDataModel[] $children
     */
    public function addChildren(BackendTreeNodeDataModel $treeNodeDataModel): void
    {
        $this->children[] = $treeNodeDataModel;
    }
}
