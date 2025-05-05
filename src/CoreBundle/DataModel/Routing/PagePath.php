<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DataModel\Routing;

class PagePath
{
    /**
     * @var string
     */
    private $pageId;
    /**
     * @var string
     */
    private $primaryPath;
    /**
     * @var string[]
     */
    private $pathList = [];

    /**
     * @param string $pageId
     * @param string $primaryPath
     */
    public function __construct($pageId, $primaryPath)
    {
        $this->pageId = $pageId;
        $this->primaryPath = $primaryPath;
        $this->pathList[] = $primaryPath;
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function addPath($path)
    {
        $this->pathList[] = $path;
    }

    /**
     * @return string
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @return string
     */
    public function getPrimaryPath()
    {
        return $this->primaryPath;
    }

    /**
     * @return string[]
     */
    public function getPathList()
    {
        return $this->pathList;
    }
}
