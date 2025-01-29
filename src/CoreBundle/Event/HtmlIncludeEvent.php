<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class HtmlIncludeEvent extends Event implements HtmlIncludeEventInterface
{
    /**
     * @var array
     */
    private $data = array();

    public function __construct(array $data = null)
    {
        if (null !== $data) {
            $this->addData($data);
        }
    }

    /**
     * only unique entries will be kept. unique will be determine as follows:
     *   - if an entry in the array has a key, that will be used to ensure uniqueness.
     *   - if it does not, the md5 sum of the content will be used.
     *
     * @param array $data
     *
     * @return void
     */
    public function addData(array $data)
    {
        foreach ($data as $key => $content) {
            if ($this->isInteger($key)) {
                $key = md5($content);
            }
            if (isset($this->data[$key])) {
                continue;
            }

            $this->data[$key] = $content;
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function removeDataElement(string $key): bool
    {
        if (!isset($this->data[$key])) {
            return false;
        }
    
        unset($this->data[$key]);

        return true;
    }

    public function updateDataElement(string $key, string $value): bool
    {
        if (!isset($this->data[$key])) {
            return false;
        }
        
        $this->data[$key] = $value;

        return true;
    }

    /**
     * @param mixed $value
     * @psalm-assert-if-true string $value
     */
    private function isInteger($value): bool
    {
        if (true === is_numeric($value)) {
            $test = (int) $value;
            if (0 !== $test) {
                return true;
            }
        }

        return false;
    }
}
