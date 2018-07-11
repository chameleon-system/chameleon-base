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
 * @deprecated since 6.2.0 - use a proper external tool to measure performance.
 */
abstract class AbstractPkgCmsProfilerItem
{
    private $name = null;
    private $file = null;
    private $line = null;
    private $time = null;
    private $endTime = null;

    /**
     * @var AbstractPkgCmsProfilerItem
     */
    private $owner = null;

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param int $line
     *
     * @return $this
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param float $time
     *
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return \AbstractPkgCmsProfilerItem
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param \AbstractPkgCmsProfilerItem $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    public function render($depth = 0)
    {
        $aCol = array(
            $this->formatTime($this->getRuntime()),
            $this->getName(),
            $this->getFile(),
            $this->getLine(),
        );

        return str_pad('', $depth * 5, ' ', STR_PAD_LEFT).implode('|', $aCol);
    }

    public function renderCompact($depth = 0)
    {
        $aCol = array(
            $this->formatTime($this->getRuntime()),
            $this->getName(),
        );

        return str_pad('', $depth * 5, ' ', STR_PAD_LEFT).implode('|', $aCol);
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param null $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    protected function formatTime($time)
    {
        return sprintf('%2.8F', $time);
    }

    public function getLongest()
    {
        return $this;
    }

    public function getRuntime()
    {
        return $this->getEndTime() - $this->getTime();
    }
}
