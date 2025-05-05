<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * simulate a field type TCMSRecord (required since we need to object, but can not access auto objects
 * Class TCMSTableToField_TCMSFieldType.
 */
class TCMSTableToField_TCMSFieldType extends TCMSTableToClass_MockRecord
{
    public function __construct(Doctrine\DBAL\Connection $connection)
    {
        parent::__construct($connection, 'cms_field_type');
    }
}
