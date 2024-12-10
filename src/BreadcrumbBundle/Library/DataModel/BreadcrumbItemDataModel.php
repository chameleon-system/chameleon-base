<?php

namespace ChameleonSystem\BreadcrumbBundle\Library\DataModel;

final class BreadcrumbItemDataModel
{
    public function __construct(
        private readonly ?string $name = '',
        private readonly ?string $url = ''
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
}
