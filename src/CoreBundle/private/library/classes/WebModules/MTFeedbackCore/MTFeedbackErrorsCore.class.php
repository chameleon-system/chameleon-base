<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTFeedbackErrorsCore
{
    public $_errorRegister = [];

    /**
     * @param string $field
     * @param string $error
     */
    public function AddError($field, $error)
    {
        if (!array_key_exists($field, $this->_errorRegister)) {
            $this->_errorRegister[$field] = [];
        }
        $this->_errorRegister[$field][] = $error;
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    public function FieldHasErrors($field)
    {
        return isset($this->_errorRegister[$field]);
    }

    /**
     * @param string $field
     *
     * @return bool|array
     */
    public function GetFieldErrors($field)
    {
        if ($this->FieldHasErrors($field)) {
            return $this->_errorRegister[$field];
        } else {
            return false;
        }
    }

    /**
     * are errors registered?
     *
     * @return bool
     */
    public function HasErrors()
    {
        return count($this->_errorRegister) > 0;
    }
}
