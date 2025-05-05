<?php

namespace ChameleonSystem\SecurityBundle\Voter;

interface RestrictedByCmsRoleInterface
{
    /**
     * returns the cms_role ids that are permitted to access the object.
     *
     * @param string|null $qualifier - optional qualifier for items that have multiple properties/actions with different rights
     *
     * @return array<string>
     */
    public function getPermittedRoles(?string $qualifier = null): array;
}
