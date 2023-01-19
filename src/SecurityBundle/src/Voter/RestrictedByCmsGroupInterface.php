<?php

namespace ChameleonSystem\SecurityBundle\Voter;

interface RestrictedByCmsGroupInterface
{
    /**
     * return the cms_usergroup ids associated with the object. A qualifier may be passed if the object has different groups
     * assigned to different functions.
     * @param string|null $qualifier
     * @return array<string>
     */
    public function getPermittedGroups(?string $qualifier = null): array;
}