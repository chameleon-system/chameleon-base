<?php

namespace ChameleonSystem\CoreBundle\Service;

use Doctrine\DBAL\Connection;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Property\Import;

class CssClassExtractor implements CssClassExtractorInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    /**
     * @throws \Exception
     */
    public function extractCssClasses(string $filePath, int $level = 0): array
    {
        $classNames = [];

        $cssContent = $this->getCSSFileContent($filePath, $level);

        if (null === $cssContent) {
            return $classNames;
        }

        try {
            $parser = new Parser($cssContent);
            $cssDocument = $parser->parse();
        } catch (\Exception $e) {
            return $classNames;
        }

        $classNames = \array_merge($classNames, $this->getCssClassesFromDocument($cssDocument));

        // handle @import of first level
        if (0 === $level) {
            $classNames = \array_merge($classNames, $this->getCssClassesFromImports($cssDocument));
        }

        return array_unique($classNames);
    }

    private function getCssClassesFromDocument(Document $cssDocument): array
    {
        $selectors = [];

        foreach ($cssDocument->getAllRuleSets() as $ruleSet) {
            if (false === method_exists($ruleSet, 'getSelectors')) {
                continue;
            }

            foreach ($ruleSet->getSelectors() as $selector) {
                // collect classes with dot
                preg_match_all('/\.([a-zA-Z0-9_-]+)/', $selector->getSelector(), $classMatches);
                if (!empty($classMatches[0])) {
                    $selectors = \array_merge($selectors, $classMatches[0]);
                }

                // collect HTML tags
                preg_match_all('/\b([a-z]+)\b/i', $selector->getSelector(), $tagMatches);
                if (!empty($tagMatches[1])) {
                    foreach ($tagMatches[1] as $tag) {
                        if ($this->isHtmlTag($tag)) {
                            $selectors[] = $tag;
                        }
                    }
                }
            }
        }

        return array_unique($selectors);
    }

    private function isHtmlTag(string $tag): bool
    {
        $htmlTags = ['div', 'span', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'a', 'img', 'form', 'input', 'button', 'textarea', 'select', 'option', 'table', 'thead', 'tbody', 'tr', 'td', 'th', 'header', 'footer', 'article', 'section', 'nav', 'aside'];

        return \in_array(\strtolower($tag), $htmlTags, true);
    }

    /**
     * @throws \Exception
     */
    public function getCssClassesFromImports(Document $cssDocument): array
    {
        $classNames = [];

        foreach ($cssDocument->getContents() as $content) {
            if ($content instanceof Import) {
                $importUrl = $content->getLocation()->getURL()->getString();

                if (!empty($importUrl)) {
                    $classesFromImport = $this->extractCssClasses($importUrl, 1);

                    if (null !== $classesFromImport) {
                        $classNames = \array_merge($classNames, $classesFromImport);
                    }
                }
            }
        }

        return array_unique($classNames);
    }

    /**
     * @throws \Exception
     */
    private function getCSSFileContent(string $filePath, int $level = 0): ?string
    {
        /** @var array|bool $urlParts */
        $urlParts = parse_url($filePath);

        if (!\is_array($urlParts) || !isset($urlParts['path'])
            || (isset($urlParts['host']) && false === $this->hasValidHostName($urlParts['host']))) {
            return null;
        }

        $localPath = PATH_WEB.$urlParts['path'];

        if (\file_exists($localPath)) {
            $filePath = $localPath;
        }

        try {
            $fileContent = \file_get_contents($filePath);
        } catch (\Exception $e) {
            if (0 === $level) {
                throw new \Exception('Error while reading file content: '.$filePath, 0, $e);
            }

            return null;
        }

        return false === $fileContent ? null : $fileContent;
    }

    private function hasValidHostName(string $hostName): bool
    {
        try {
            $query = 'SELECT *
                    FROM `cms_portal_domains`
                   WHERE `name` = :hostname
                      OR `sslname` = :hostname
                   ';
            $sqlStatement = $this->connection->prepare($query);
            $result = $sqlStatement->executeQuery(['hostname' => $hostName]);
        } catch (\Exception $e) {
            return false;
        }

        return $result->rowCount() > 0;
    }
}
