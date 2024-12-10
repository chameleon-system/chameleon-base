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
 * Class TTableEditorListFieldState
 * holds the state of all list fields in cms backed (which list was opened, which closed)
 * note: access class as service cmsPkgCore.tableEditorListFieldState (\ChameleonSystem\CoreBundle\ServiceLocator::get('cmsPkgCore.tableEditorListFieldState')).
 */
class TTableEditorListFieldState implements Serializable
{
    const STATE_OPEN = 1;
    const STATE_CLOSED = 0;
    private $states = array();

    public function getState($tableName, $field)
    {
        if (isset($this->states[$tableName]) && isset($this->states[$tableName][$field])) {
            return $this->states[$tableName][$field];
        }

        return self::STATE_CLOSED;
    }

    public function setState($tableName, $field, $state)
    {
        $state = intval($state);
        if (!isset($this->states[$tableName])) {
            $this->states[$tableName] = array();
        }
        $this->states[$tableName][$field] = $state;

        $this->updateSession();
    }

    public function __construct()
    {
        if (isset($_SESSION['tableEditorListFieldState'])) {
            /** @var TTableEditorListFieldState $instance */
            $instance = unserialize($_SESSION['tableEditorListFieldState']);
            $this->setStates($instance->getStates());
        }
    }

    private function updateSession()
    {
        $_SESSION['tableEditorListFieldState'] = serialize($this);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object.
     *
     * @see http://php.net/manual/en/serializable.serialize.php
     *
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->states);
    }

    /**
     * Deprecation Notice:
     * TTableEditorListFieldState implements the Serializable interface, which is deprecated.
     * Implement __serialize() and __unserialize() instead (or in addition, if support for old PHP versions is necessary)
     */
    public function __serialize()
    {
        return $this->serialize();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object.
     *
     * @see http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     */
    public function unserialize($serialized)
    {
        $this->states = unserialize($serialized);
    }

    /**
     * Deprecation Notice:
     * TTableEditorListFieldState implements the Serializable interface, which is deprecated.
     * Implement __serialize() and __unserialize() instead (or in addition, if support for old PHP versions is necessary)
     */
    public function __unserialize($serialized)
    {
        $this->unserialize($serialized);
    }

    /**
     * @return array
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * @param array $states
     */
    public function setStates($states)
    {
        $this->states = $states;
    }
}
