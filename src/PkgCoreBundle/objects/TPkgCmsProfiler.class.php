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
class TPkgCmsProfiler
{
    /**
     * @var TPkgCmsProfileItem_Group
     */
    private $tick = null;
    /** @var TPkgCmsProfileItem_Group */
    private $groupPointer = null;
    private $startTime = 0;
    private $lastTimeStamp = 0;
    private $enableProfiler = false;

    public function startGroup($name, $file, $line)
    {
        if (false === $this->enableProfiler) {
            return;
        }
        $this->lastTimeStamp = microtime(true);
        $group = new TPkgCmsProfileItem_Group();
        $group
            ->setFile($file)
            ->setLine($line)
            ->setTime($this->lastTimeStamp)
            ->setEndTime($group->getTime())
            ->setName($name);
        if (null === $this->getGroupPointer()) {
            $this->tick = $group;
            $this->groupPointer = $this->tick;
        } else {
            $group->setOwner($this->getGroupPointer());
            $this->groupPointer = $this->getGroupPointer()->addChild($group);
        }
    }

    public function endGroup()
    {
        if (false === $this->enableProfiler) {
            return;
        }

        if (null === $this->getGroupPointer()) {
            return;
        }
        $this->lastTimeStamp = microtime(true);
        $this->getGroupPointer()->setEndTime($this->lastTimeStamp);
        $this->groupPointer = $this->getGroupPointer()->getOwner();
    }

    public function addTick($name, $file, $line, $visible = true)
    {
        if (false === $this->enableProfiler) {
            return;
        }
        $lastTickTime = $this->lastTimeStamp;

        if (null === $this->getGroupPointer()) {
            $this->startGroup('unknown', $file, $line);
        }
        $newTick = new TPkgCmsProfileItem_Tick();

        $this->lastTimeStamp = microtime(true);
        $newTick
            ->setName($name)
            ->setFile($file)
            ->setLine($line)
            ->setTime($lastTickTime)
            ->setEndTime($this->lastTimeStamp);
        $this->getGroupPointer()->addChild($newTick);
    }

    /**
     * @return TPkgCmsProfileItem_Group|null
     */
    private function &getGroupPointer()
    {
        if (null === $this->groupPointer) {
            $this->groupPointer = $this->tick;
        }

        return $this->groupPointer;
    }

    public function __toString()
    {
        if (false === $this->enableProfiler) {
            return '';
        }

        $result = '';
        $timeOfOutput = microtime(true);
        $timeOfFirstTick = $this->tick->getTime();
        $trackedTime = $this->tick->getEndTime();

        $result .= "\nTotal time BEFORE first tick: ".sprintf('%2.8F', $this->tick->getTime() - $this->startTime);
        $result .= "\nTotal tracked time:           ".sprintf('%2.8F', $this->lastTimeStamp - $this->tick->getTime());
        $result .= "\nTotal time AFTER last tick:   ".sprintf('%2.8F', $timeOfOutput - $this->lastTimeStamp);
        $result .= "\nTotal time:                   ".sprintf('%2.8F', $timeOfOutput - $this->startTime)."\n";
        $result .= "\n".$this->tick->render();

        $groups = $this->getGroups($this->tick);
        $result .= "\n\n---------------------------------------------------\n\n";
        $result .= "GROUPS ONLY\n";
        $result .= $groups->renderCompact();

        return $result;
    }

    /**
     * @param AbstractPkgCmsProfilerItem $tick
     *
     * @return AbstractPkgCmsProfilerItem|null
     */
    private function getGroups(AbstractPkgCmsProfilerItem $tick)
    {
        if (false === $tick instanceof TPkgCmsProfileItem_Group) {
            return null;
        }
        /** @var TPkgCmsProfileItem_Group $group */
        $group = clone $tick;
        $children = $group->getChildren();
        $group->setChildren(array());
        foreach (array_keys($children) as $childIndex) {
            if (null === $children[$childIndex]) {
                continue;
            }
            $child = $this->getGroups($children[$childIndex]);
            if (null !== $child) {
                $group->addChild($child);
            }
        }

        return $group;
    }

    /**
     * @param int $startTime
     *
     * @return $this
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @param bool $enableProfiler
     *
     * @return $this
     */
    public function setEnableProfiler($enableProfiler)
    {
        $this->enableProfiler = $enableProfiler;

        return $this;
    }
}
