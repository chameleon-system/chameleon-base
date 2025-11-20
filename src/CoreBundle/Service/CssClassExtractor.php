<?php

namespace ChameleonSystem\CoreBundle\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
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
     * Create a mapping of class rules related to defined html tags for the
     * provided css file.
     *
     * @example:
     *   CSS-file-contents:
     *      .classX
     *      htmlTag1.classY
     *      htmlTag2.classY
     *   result in:
     *      $classMap['classX'][]
     *      $classMap['classY']['htmlTag1', 'htmlTag2']
     *
     * @throws \Exception
     */
    public function extractCssClasses(string $filePath, int $level = 0): array
    {
        $cssContent = $this->getCSSFileContent($filePath, $level);
        if (null === $cssContent) {
            return [];
        }

        try {
            $parser = new Parser($cssContent);
            $cssDocument = $parser->parse();
        } catch (\Exception $e) {
            return [];
        }

        $classMap = $this->getCssClassesFromDocument($cssDocument);

        // handle @import of first level
        if (0 === $level) {
            $importClassMap = $this->getCssClassesFromImports($cssDocument, $filePath);
            $classMap = $this->mergeClassMaps($classMap, $importClassMap);
        }

        return $classMap;
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

    private function getCssClassesFromDocument(Document $cssDocument): array
    {
        $classMap = [];

        foreach ($cssDocument->getAllRuleSets() as $ruleSet) {
            if (false === method_exists($ruleSet, 'getSelectors')) {
                continue;
            }

            foreach ($ruleSet->getSelectors() as $selector) {
                $selectorStr = $selector->getSelector();

                // extract classes and possible html tag relation definition
                // e.g., img.img-rounded, h1.F19, etc.
                preg_match_all(
                    '/(?:(\b[a-zA-Z][a-zA-Z0-9-]*)\s*)?(\.([a-zA-Z0-9_-]+))/',
                    $selectorStr,
                    $matches,
                    PREG_SET_ORDER
                );

                foreach ($matches as $match) {
                    $class = $match[3];

                    if (false === isset($classMap[$class])) {
                        $classMap[$class] = [];
                    }

                    $tag = isset($match[1]) ? strtolower($match[1]) : null;
                    if (true === $this->isHtmlTag($tag) && false === in_array($tag, $classMap[$class], true)) {
                        $classMap[$class][] = $tag;
                    }
                }
            }
        }

        return $classMap;
    }

    private function mergeClassMaps(array $base, array $append): array
    {
        foreach ($append as $class => $tags) {
            if (false === isset($base[$class])) {
                $base[$class] = $tags;
            } else {
                $base[$class] = array_unique(array_merge($base[$class], $tags));
            }
        }

        return $base;
    }

    /**
     * @throws \Exception
     */
    public function getCssClassesFromImports(Document $cssDocument, string $filePath): array
    {
        $baseDir = rtrim(dirname($filePath), '/').'/';
        $classMap = [];
        foreach ($cssDocument->getContents() as $content) {
            if ($content instanceof Import) {
                $importUrl = $content->getLocation()->getURL()->getString();
                if (true === empty($importUrl)) {
                    continue;
                }
                // use importUrl directly if it is an absolute path
                $resolvedPath = parse_url($importUrl, PHP_URL_SCHEME) ? $importUrl : $baseDir.$importUrl;
                $importClasses = $this->extractCssClasses($resolvedPath, 1);
                $classMap = $this->mergeClassMaps($classMap, $importClasses);
            }
        }

        return $classMap;
    }

    private function isHtmlTag(?string $tag): bool
    {
        $htmlTags = [
            'div',
            'span',
            'p',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'ul',
            'ol',
            'li',
            'a',
            'img',
            'form',
            'input',
            'button',
            'textarea',
            'select',
            'option',
            'table',
            'thead',
            'tbody',
            'tr',
            'td',
            'th',
            'header',
            'footer',
            'article',
            'section',
            'nav',
            'aside',
        ];

        return true === in_array(strtolower($tag ?? ''), $htmlTags, true);
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
            $sqlStatement->executeQuery(['hostname' => $hostName]);
        } catch (\Exception|Exception $e) {
            return false;
        }

        return $sqlStatement->rowCount() > 0;
    }
}
