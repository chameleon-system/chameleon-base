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

class SuccessQuery implements \JsonSerializable
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
     * @param string $query
     * @param int $line
     */
    public function __construct($query, $line)
    {
        $this->query = $query;
        $this->line = $line;
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
