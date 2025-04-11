<?php

namespace ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Service;

use ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Interfaces\MarkdownCmsLinkParserInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Util\HtmlFilter;

class MarkdownParserService implements MarkdownParserServiceInterface
{
    public function __construct(
        private readonly MarkdownCmsLinkParserInterface $markdownCmsLinkParser,
        private readonly \IPkgCmsStringUtilities_VariableInjection $cmsStringUtilitiesVariableInjection )
    {
    }
    
    public function getMarkdownParser(): MarkdownConverter
    {
        $environment = new Environment($this->getConfig());
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new SmartPunctExtension());
        $environment->addExtension(new AttributesExtension());

        return new MarkdownConverter($environment);
    }
    
    public function parse(?string $markdownText, ?array $replaceVariables = null): string
    {
        if (null === $markdownText) {
            return '';
        }

        $markdownText = $this->markdownCmsLinkParser->replaceCmsLinksInMarkdown($markdownText);
        
        $markdownParser = $this->getMarkdownParser();
        $htmlText = $markdownParser->convert($markdownText);

        if (true === \is_array($replaceVariables)) {
            $htmlText = $this->cmsStringUtilitiesVariableInjection->replace($htmlText, $replaceVariables);
        }
        
        return $htmlText;
    }
    
    protected function getConfig(): array
    {
        return [
            'html_input' => HtmlFilter::ALLOW,
            'table' => [
                'wrap' => [
                    'enabled' => false,
                    'tag' => 'div',
                    'attributes' => [],
                ],
                'alignment_attributes' => [
                    'left'   => ['align' => 'left'],
                    'center' => ['align' => 'center'],
                    'right'  => ['align' => 'right'],
                ],
            ],
        ];
    }
}
