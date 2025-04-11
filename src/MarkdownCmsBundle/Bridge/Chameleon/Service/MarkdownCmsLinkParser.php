<?php

namespace ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Service;

use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Interfaces\MarkdownCmsLinkParserInterface;

class MarkdownCmsLinkParser implements MarkdownCmsLinkParserInterface
{
    private ?string $notFoundPageUrl = null;

    public function __construct(
        private readonly PortalDomainServiceInterface $portalDomainService,
        private readonly PageServiceInterface $pageService,
    ) {
    }
    
    public function replaceCmsLinksInMarkdown(string $markdownText): string
    {
        return \preg_replace_callback(
            '/\[([^\]]+)\]\(([^)|]+\|[^)]+)\)/',
            array($this, 'replaceCmsLinks'),
            $markdownText
        );
    }
    
    private function replaceCmsLinks(array $matches): string
    {
        $title = \trim($matches[1]);
        $tableAndRecordString = $matches[2];
        $tableAndRecord = explode('|', $tableAndRecordString);
        $table = \trim($tableAndRecord[0]);
        $recordId = \trim($tableAndRecord[1]);

        $url = $this->getLinkForType($table, $recordId);

        if (empty($url)) {
            $url = $this->getNotFoundPageUrl();
        }

        $target = '';
        if ($table === 'cms_document') {
            $target = '{target=_blank}';
        }

        return '[' . $title . '](' . $url . ')' . $target;
    }

    private function getLinkForType(string $table, string $recordId): string
    {
        $url = '';

        switch ($table) {
            case 'pkg_article':
                $article = \TdbPkgArticle::GetNewInstance();
                if (false !== $article->Load($recordId)) {
                    $url = $article->getLink();
                }
                break;
            case 'cms_tpl_page':
                $url = $this->pageService->getLinkToPageRelative($recordId);
                break;
            case 'shop_article':
                $product = \TdbShopArticle::GetNewInstance();
                if (false !== $product->Load($recordId)) {
                    $url = $product->getLink();
                }
                break;
            case 'cms_document':
                $document = \TdbCmsDocument::GetNewInstance(); 
                if (false !== $document->Load($recordId)) {
                    $url = $document->GetPlainDownloadLink();
                }
                break;
                
        }

        return $url;
    }

    private function getNotFoundPageUrl(): string
    {
        if ($this->notFoundPageUrl === null) {
            $portal = $this->portalDomainService->getActivePortal();

            if (null === $portal) {
                return '';
            }

            $this->notFoundPageUrl = $portal->GetFieldPageNotFoundNodePageURL();
        }

        return $this->notFoundPageUrl;
    }
}
