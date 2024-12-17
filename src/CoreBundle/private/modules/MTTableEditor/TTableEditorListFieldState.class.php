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
class TTableEditorListFieldState
{
    public const STATE_OPEN = 1;
    public const STATE_CLOSED = 0;
    private $states = [];

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
            $this->states[$tableName] = [];
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

    public function __serialize(): array
    {
        return [
            'states' => $this->states,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->states = $data['states'];
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
