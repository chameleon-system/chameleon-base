<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\DataModel;

class ErrorQuery implements \JsonSerializable
{
    /**
     * @var string
     */
    private $query;
    /**
     * @var int
     */
    private $line;
    /**
     * @var string
     */
    private $error;

    /**
     * @param string $query
     * @param int $line
     * @param string $error
     */
    public function __construct($query, $line, $error)
    {
        $this->query = $query;
        $this->line = $line;
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $result = '';
        foreach (get_object_vars($this) as $key => $value) {
            $result .= "$key: $value\n";
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
