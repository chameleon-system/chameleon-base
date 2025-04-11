<?php

namespace ChameleonSystem\MarkdownCmsBundle\Twig;

use ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Interfaces\MarkdownCmsLinkParserInterface;
use ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Service\MarkdownParserServiceInterface;
use Twig\Extension\AbstractExtension;

class MarkdownExtension extends AbstractExtension
{
    public function __construct(
        private readonly MarkdownParserServiceInterface $markdownParser
    ) {
    }

    public function getFilters(): array
    {
        return array(
            new \Twig\TwigFilter(
                'markdown',
                $this->parseMarkdown(...),
                array('is_safe' => array('html'))
            ),
        );
    }

    public function parseMarkdown(?string $content, ?array $replaceVariables = null): string
    {
        return $this->markdownParser->parse($content);
    }
    
}
