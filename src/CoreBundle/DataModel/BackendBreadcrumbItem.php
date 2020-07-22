<?php

namespace ChameleonSystem\CoreBundle\DataModel;

class BackendBreadcrumbItem
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $name;

    public function __construct(string $url, string $name)
    {
        $this->url = $url;
        $this->name = $name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function equals(BackendBreadcrumbItem $other): bool
    {
        return $this->name === $other->name && $this->url === $other->url;
    }
}
