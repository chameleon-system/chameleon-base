<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Maintenance\DataModel;

class ComposerData
{
    /**
     * @var string
     */
    private $filePath;
    /**
     * @var array
     */
    private $data;

    /**
     * @param string $filePath
     */
    public function __construct($filePath, array $data)
    {
        $this->filePath = $filePath;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
