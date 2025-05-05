<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UniversalUploader\Bridge\JqueryFileUpload\DataModel;

/**
 * This class is used to generate a JSON object for the jQuery File Upload API, so use public properties.
 */
class File
{
    /**
     * @var string|null
     */
    public $recordId;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var int
     */
    public $size = 0;

    /**
     * @var string
     */
    public $type = '';

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var string
     */
    public $error = '';

    /**
     * @var Error[]
     */
    public $errors = [];
}
