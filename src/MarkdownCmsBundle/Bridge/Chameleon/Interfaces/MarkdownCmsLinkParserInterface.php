<?php

namespace ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Interfaces;

interface MarkdownCmsLinkParserInterface
{
    public function replaceCmsLinksInMarkdown(string $markdownText): string;
}
