<?php

namespace ChameleonSystem\SecurityBundle\DataAccess;

interface RightsDataAccessInterface
{
    public function getGroupIdBySystemName(string $groupSystemName): ?string;

    public function getRoleIdBySystemName(string $roleSystemName): ?string;

    public function getRightIdBySystemName(string $rightSystemName): ?string;
}