<?php

namespace ChameleonSystem\SecurityBundle\Voter;

interface RestrictedByCmsRightsInterface
{
    /**
     * returns the cms_right ids that are permitted to access the object.
     *
     * @param string|null $qualifier - optional qualifier for items that have multiple properties/actions with different rights
     *
     * @return array<string>
     */
    public function getPermittedRights(?string $qualifier = null): array;
}
