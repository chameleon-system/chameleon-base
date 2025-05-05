<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Command\Helper;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

class HtmlHelper
{
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var array
     */
    private $lines = [];
    /**
     * @var string
     */
    private static $REPLACETOKEN = '_____REPLACETOKEN_____';

    public function __construct(OutputInterface $output)
    {
        $style = new OutputFormatterStyle('red', null, ['bold']);
        $output->getFormatter()->setStyle('header', $style);

        $this->output = $output;
    }

    /**
     * @param array|string|object $html
     *
     * @return void
     */
    private function computeHtml($html)
    {
        $html = $this->getLines($html);
        $html = $this->convertHeaderTags($html);
        $html = $this->compressWhitespace($html);
        $lines = $this->convertToArray($html);
        $this->lines = $this->killAllOtherTags($lines);
    }

    /**
     * @param array|string|object $html
     *
     * @return string
     */
    private function getLines($html)
    {
        if (is_array($html) || $html instanceof \stdClass) {
            $htmlArray = [];
            foreach ($html as $line) {
                $htmlArray[] = $this->getLines($line);
            }

            return implode('<br />', $htmlArray);
        } else {
            return (string) $html;
        }
    }

    /**
     * @param string|null $html
     *
     * @return void
     */
    public function render($html = null)
    {
        if (null !== $html) {
            $this->computeHtml($html);
        }

        $this->output->writeln($this->lines);
    }

    /**
     * @param string $html
     *
     * @return array
     */
    private function convertToArray($html)
    {
        $tokenizedNewLinesString = preg_replace("/<br[ ]*\/?>/", self::$REPLACETOKEN, $html);

        return explode(self::$REPLACETOKEN, $tokenizedNewLinesString);
    }

    /**
     * @param string $html
     *
     * @return string
     */
    private function convertHeaderTags($html)
    {
        $convertedLines = $html;
        $convertedLines = preg_replace('/<h[1-9].*?>/', '<header>', $convertedLines);
        $convertedLines = preg_replace("/<\/h[1-9]>/", '</header><br />', $convertedLines);

        return $convertedLines;
    }

    /**
     * @param string[] $lines
     *
     * @return string[]
     */
    private function killAllOtherTags(array $lines)
    {
        $convertedLines = [];
        foreach ($lines as $line) {
            $line = preg_replace_callback('/<.*?>/', [$this, 'replaceTagsCallback'], $line);
            $convertedLines[] = $line;
        }

        return $convertedLines;
    }

    /**
     * @param string[] $matches
     *
     * @return string
     */
    private function replaceTagsCallback(array $matches)
    {
        $blacklist = ["/<\/?header>/"];

        $inBlackList = false;

        foreach ($matches as $match) {
            foreach ($blacklist as $blackListEntry) {
                if (1 === preg_match($blackListEntry, $match)) {
                    $inBlackList = true;
                }
            }
        }

        return $inBlackList ? implode('', $matches) : '';
    }

    /**
     * @param string $html
     *
     * @return string
     */
    private function compressWhitespace($html)
    {
        return preg_replace('/  */', ' ', $html);
    }
}
