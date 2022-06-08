<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TDataCountryList extends TDataCountryListAutoParent
{
    /**
     * {@inheritdoc}
     *
     * @psalm-suppress AssignmentToVoid, InvalidReturnStatement
     * @FIXME `Load` is a void method. Saving the parent return value and returning in this method makes no sense.
     */
    public function Load($sQuery, array $queryParameters = array(), array $queryParameterTypes = array())
    {
        $returnValue = parent::Load($sQuery, $queryParameters, $queryParameterTypes);
        $this->AddFilterString('`data_country`.`active`="1"');

        return $returnValue;
    }
}
