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
        if (null === $content) {
            return '';
        }

        $html = $this->markdownParser->parse($content);

        // surround table with wrapper div to make it easier to style (for example horizontal scrolling)
        $html = preg_replace_callback(
            '#<table.*?>.*?</table>#is',
            fn ($matches) => '<div class="markdown-table-wrapper">'.$matches[0].'</div>',
            $html
        );

        return $html;
    }
}
