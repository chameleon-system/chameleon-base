<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsException extends \Exception implements Serializable
{
    /**
     * @var null|string
     */
    private $contextData = null;

    /**
     * @param string $message      - additional message string (shows up only in the log file)
     * @param array  $aContextData - any data you want showing up in the log message to help you debug the exception
     */
    public function __construct(
        $message = '',
        $aContextData = array() // any data you want showing up in the log message to help you debug the exception
    ) {
        parent::__construct($message);

        $this->contextData = print_r($aContextData, true);
    }

    public function __toString()
    {
        $sString = parent::__toString();

        $sString .= "\ncalled in [".$this->getFile().'] on line ['.$this->getLine().']';
        $sString .= "\n\nContext:\n".$this->getContextData();

        return $sString;
    }

    /**
     * @return null|string
     */
    public function getContextData()
    {
        return $this->contextData;
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
        $content = array();
        foreach ($this as $key => $value) {
            if ('trace' !== $key) {
                $content[$key] = $value;
            }
        }

        return serialize($content);
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
        $content = unserialize($serialized);
        foreach ($content as $key => $value) {
            $this->$key = $value;
        }
    }
}
