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
 * manages the image pool (selection of images for a record in a table)
 * includes adding, removing, and changing images, as well as assigning them
 * to the record.
 * views:
 *  - main - used to assign an image to the record, and branches to the other views
 *  - upload - used to upload an image
 *  - edit - used to edit the properties of the image
 *  - choose - used to select an image from the uploaded image
 * operations:
 *  - assign - write image id to field
 *  - reset - reset image id to default value
 *  - show image library
 *    - all image library functions.
 *
 * @deprecated since 6.2.0 - Chameleon has a new media manager
/**/
class CMSModuleImagePool extends TCMSModelBase
{
    public $sTabelId = null;
    public $sId = null;
}
