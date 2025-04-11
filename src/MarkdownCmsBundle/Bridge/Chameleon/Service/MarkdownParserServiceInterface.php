<?php

namespace ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Service;

use League\CommonMark\MarkdownConverter;

interface MarkdownParserServiceInterface
{
    public function getMarkdownParser(): MarkdownConverter;

    public function parse(?string $markdownText, ?array $replaceVariables = null): string;
}
