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
 * manages lists of data, provided methods to move forward and back in the list.
 *
 * @template T
 * @implements Iterator<int, T>
 *
 * Dynamic properties introduced by __get and __set
 * @property T[] $_items
 * @property int $_itemPointer
 */
class TIterator implements Iterator
{
    private $itemPointer = 0;
    /**
     * array of item objects.
     *
     * @var T[]
     */
    protected $_items = array();

    public function __get($sParameter)
    {
        if ('_itemPointer' === $sParameter) {
            trigger_error('use of _itemPointer is deprecated - you should use getItemPointer instead', E_USER_DEPRECATED);

            return $this->getItemPointer();
        }
        if ('_items' == $sParameter) {
            trigger_error("Notice: do not access _items directly!\n".TTools::GetFormattedDebug(), E_USER_NOTICE);

            return $this->_items;
        } else {
            trigger_error("Property {$sParameter} does not exist in ".__CLASS__.' on line '.__LINE__, E_USER_NOTICE);
        }
    }

    public function __set($sParameter, $sValue)
    {
        if ('_itemPointer' === $sParameter) {
            trigger_error('use of _itemPointer is deprecated - you should use setItemPointer instead', E_USER_DEPRECATED);
            $this->setItemPointer($sValue);

            return;
        }
        if ('_items' == $sParameter) {
            trigger_error("Notice: do not access _items directly!\n".TTools::GetFormattedDebug(), E_USER_NOTICE);
            $this->_items = $sValue;
        } else {
            trigger_error("Property {$sParameter} does not exist in ".__CLASS__.' on line '.__LINE__, E_USER_NOTICE);
        }
    }

    /**
     * delete the list.
     * @return void
     */
    public function Destroy()
    {
        for ($i = 0; $i < $this->Length(); ++$i) {
            unset($this->_items[$i]);
        }
        $this->setItemPointer(0);
        $this->_items = array();
    }

    /**
     * Adds an item to the end of the list.
     *
     * @param T $item
     * @return void
     */
    public function AddItem(&$item)
    {
        $this->_items[] = $item;
    }

    /**
     * adds an item to the beginning of the list. also keeps the element pointer
     * pointing at the current elemement UNLESS that is the first element.
     *
     * @param T $item
     * @return void
     */
    public function AddItemToStart(&$item)
    {
        array_unshift($this->_items, $item);
        if ($this->getItemPointer() > 0) {
            $this->setItemPointer($this->getItemPointer() + 1);
        }
    }

    /**
     * @param callable(T, T):int $callback
     * @return void
     */
    public function usort($callback)
    {
        usort($this->_items, $callback);
    }

    /**
     * remove an element from the list. works only if the item is an object.
     *
     * @param string $propertyName  - class property
     * @param string $propertyValue - value of the property
     *
     * @return bool
     */
    public function RemoveItem($propertyName, $propertyValue)
    {
        $tmpCurrentPointer = $this->getItemPointer();
        $this->GoToStart();
        $found = false;
        $removedPointer = 0;
        while ($oItem = $this->Next()) {
            if (!$found) {
                if (is_object($oItem) && property_exists($oItem, $propertyName) && $oItem->$propertyName == $propertyValue) {
                    $found = true;
                    $removedPointer = $this->getItemPointer() - 1;
                    //        	  unset($this->_items[$removedPointer]);
                }
            }
            if ($found && array_key_exists($this->getItemPointer(), $this->_items)) {
                $this->_items[$this->getItemPointer() - 1] = $this->_items[$this->getItemPointer()];
            }
        }
        if ($found) {
            array_pop($this->_items);
        }
        if ($tmpCurrentPointer < $removedPointer) {
            $this->setItemPointer($tmpCurrentPointer);
        } elseif ($tmpCurrentPointer > 0) {
            $this->setItemPointer($tmpCurrentPointer - 1);
        } else {
            $this->setItemPointer(0);
        }

        return $found;
    }

    /**
     * searches for ONE item with the property and returns a pointer to the FIRST matching element found
     * returns false if no item is found.
     *
     * @param string $propertyName  - property name (must be public in the items
     * @param string $propertyValue - property value
     * @param bool   $bIgnoreCase   - do a case insenstive compare
     *
     * @return T|false
     */
    public function FindItemWithProperty($propertyName, $propertyValue, $bIgnoreCase = false)
    {
        $tmpCurrentPointer = $this->getItemPointer();
        $this->GoToStart();
        $oItemFound = false;
        while ($oItem = &$this->Next()) {
            if (is_object($oItem) && property_exists($oItem, $propertyName)) {
                $bMatches = false;
                if ($bIgnoreCase) {
                    $bMatches = (0 == strcasecmp($oItem->$propertyName, $propertyValue));
                } else {
                    $bMatches = ($oItem->$propertyName == $propertyValue);
                }
                if ($bMatches) {
                    $oItemFound = &$oItem;
                }
            }
        }
        $this->setItemPointer($tmpCurrentPointer);

        return $oItemFound;
    }

    /**
     * updates an item in the list (match based on $matchPropertyName
     * adds the item if it is not found in the list.
     * returns true if the item was replaced.
     *
     * @param T $oNewItem
     * @param string $matchPropertyName - if the value of this property name is equal the item will be replaced
     *
     * @return bool
     */
    public function UpdateOrAddItem($oNewItem, $matchPropertyName)
    {
        $tmpCurrentPointer = $this->getItemPointer();
        $this->GoToStart();
        $oItemFound = false;
        while (!$oItemFound && ($oItem = &$this->Next())) {
            if (is_object($oItem) && property_exists($oItem, $matchPropertyName) && $oItem->$matchPropertyName == $oNewItem->$matchPropertyName) {
                $this->_items[$this->getItemPointer() - 1] = $oNewItem;
                $oItemFound = true;
            }
        }
        $this->setItemPointer($tmpCurrentPointer);
        if (false == $oItemFound) {
            $this->AddItem($oNewItem);
        }

        return $oItemFound;
    }

    /**
     * searches for items with the property and returns a TIterator of all found items
     * returns false if no items are found.
     *
     * @param string $propertyName  - property name (must be public in the items
     * @param string $propertyValue - property value
     *
     * @return TIterator<T>
     */
    public function FindItemsWithProperty($propertyName, $propertyValue)
    {
        $tmpCurrentPointer = $this->getItemPointer();
        $this->GoToStart();
        $oItemsFound = new self();

        $bItemFound = false;
        while ($oItem = &$this->Next()) {
            if (is_object($oItem) && property_exists($oItem, $propertyName) && $oItem->$propertyName == $propertyValue) {
                $bItemFound = true;
                $oItemsFound->AddItem($oItem);
            }
        }
        $this->setItemPointer($tmpCurrentPointer);
        if (!$bItemFound) {
            $oItemsFound = false;
        }

        return $oItemsFound;
    }

    /**
     * returns current item without moving the item pointer.
     *
     * @return T
     */
    public function &current()
    {
        if (-1 == $this->getItemPointer() && count($this->_items) > 0) {
            $this->setItemPointer(0);
        }
        if (is_null($this->_items[$this->getItemPointer()])) {
            trigger_error('NULL object in iterator NOT ALLOWED!', E_USER_WARNING);
        }

        return $this->_items[$this->getItemPointer()];
    }

    /**
     * return true if the item is in the list (uses the IsSameAs method).
     *
     * @param T $oItem
     *
     * @return bool
     */
    public function IsInList($oItem)
    {
        $bIsInList = false;
        $iCurPos = $this->getItemPointer();
        $this->GoToStart();
        while (!$bIsInList && ($oTmpItem = &$this->Next())) {
            if ($oTmpItem->IsSameAs($oItem)) {
                $bIsInList = true;
            }
        }
        $this->setItemPointer($iCurPos);

        return $bIsInList;
    }

    /**
     * returns the current item in the list and advances the list pointer by 1.
     *
     * @return T|false
     */
    public function &next()
    {
        $item = false;
        if ($this->getItemPointer() < $this->Length()) {
            $item = &$this->Current();
            $this->setItemPointer($this->getItemPointer() + 1);
        }

        return $item;
    }

    /**
     * returns the current item in the list and moves the list pointer back by 1.
     *
     * @return T|false
     */
    public function &Previous()
    {
        $item = false;
        if ($this->getItemPointer() >= 0) {
            $item = &$this->Current();
            $this->setItemPointer($this->getItemPointer() - 1);
        }

        return $item;
    }

    /**
     * Resets the pointer back to the start of the list.
     *
     * @return void
     */
    public function GoToStart()
    {
        reset($this->_items);
        $this->setItemPointer(0);
    }

    /**
     * Returns the number of elements in the list.
     *
     * @return int
     */
    public function Length()
    {
        $length = count($this->_items);

        return $length;
    }

    /**
     * returns true if the entry that was just fetched was the last entry.
     *
     * @return bool
     */
    public function IsLast()
    {
        return ($this->Length()) == $this->getItemPointer();
    }

    /**
     * jumps to the end of the list.
     *
     * @return void
     */
    public function GoToEnd()
    {
        $this->setItemPointer(($this->Length() - 1));
    }

    /**
     * returns one random element from the list or false if the list is empty.
     *
     * @return T|false $item
     */
    public function &Random()
    {
        if ($this->Length() > 0) {
            $randomIndex = array_rand($this->_items);

            return $this->_items[$randomIndex];
        } else {
            return false;
        }
    }

    /**
     * shuffle list.
     *
     * @return void
     */
    public function ShuffleList()
    {
        $this->GoToStart();
        shuffle($this->_items);
    }

    /**
     * reverses the item list.
     *
     * @return void
     */
    public function ReverseItemList()
    {
        $this->_items = array_reverse($this->_items);
    }

    /**
     * @return int
     */
    protected function getItemPointer()
    {
        return $this->itemPointer;
    }

    /**
     * @param int $itemPointer
     */
    protected function setItemPointer($itemPointer)
    {
        $this->itemPointer = $itemPointer;
    }

    public function key(): int
    {
        return $this->getItemPointer();
    }

    public function valid(): bool
    {
        return $this->getItemPointer() < $this->Length();
    }

    public function rewind(): void
    {
        $this->setItemPointer(0);
    }

}
