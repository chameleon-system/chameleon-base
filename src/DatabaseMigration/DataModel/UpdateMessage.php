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

class UpdateMessage implements \JsonSerializable
{
    /**
     * @var string
     */
    private $text;
    /**
     * @var int
     */
    private $level;

    /**
     * @param string $text
     * @param int $level
     */
    public function __construct($text, $level)
    {
        $this->text = $text;
        $this->level = $level;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
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
