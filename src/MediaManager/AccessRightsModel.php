<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager;

/**
 * Represents access rights to a table.
 */
class AccessRightsModel
{
    // public properties to be able to use the class in js too

    /**
     * @var bool
     */
    public $delete = false;

    /**
     * @var bool
     */
    public $new = false;

    /**
     * @var bool
     */
    public $edit = false;

    /**
     * @var bool
     */
    public $show = false;
}
