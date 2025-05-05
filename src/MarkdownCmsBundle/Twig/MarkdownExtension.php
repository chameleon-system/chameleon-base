<?php

namespace ChameleonSystem\MarkdownCmsBundle\Twig;

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
        return [
            new \Twig\TwigFilter(
                'markdown',
                $this->parseMarkdown(...),
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function parseMarkdown(?string $content, ?array $replaceVariables = null): string
    {
        return $this->markdownParser->parse($content);
    }
}
