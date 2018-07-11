<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\core\DatabaseAccessLayer\Workflow;

use ChameleonSystem\core\DatabaseAccessLayer\QueryModifierOrderBy;

/**
 * @deprecated since 6.2.0 - workflow is not supported anymore
 */
class WorkflowQueryModifierOrderBy extends QueryModifierOrderBy
{
    public function getQueryWithOrderBy($query, array $orderBy)
    {
        if ($this->isWorkflowQuery($query)) {
            // note: this is old code from TCMSRecordList. I think it is broken for sort requests on joined tables
            $orderBy = $this->transformOrderByForWorkflow($orderBy);
        }

        return parent::getQueryWithOrderBy($query, $orderBy);
    }

    private function transformOrderByForWorkflow($orderBy)
    {
        $newOrderBy = array();
        foreach ($orderBy as $field => $direction) {
            $parts = explode('.', $field);
            if (2 === count($parts)) {
                $field = 'workflowuniontable.'.$parts[1];
            }
            $newOrderBy[$field] = $direction;
        }

        return $newOrderBy;
    }

    private function isWorkflowQuery($query)
    {
        return false !== mb_strrpos($query, 'workflowuniontable', 'UTF-8');
    }
}
