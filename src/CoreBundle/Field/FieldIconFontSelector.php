<?php

namespace ChameleonSystem\CoreBundle\Field;

use ChameleonSystem\CoreBundle\Service\CssClassExtractorInterface;
use ChameleonSystem\CoreBundle\Service\FontAwesomeServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class FieldIconFontSelector extends \TCMSFieldVarchar
{
    public function GetHTML(): string
    {
        $fieldHtml = '<div class="input-group input-group-sm">
              <div class="input-group-prepend">
              <span class="input-group-text">
                <span class="'.\TGlobal::OutHTML($this->data).'" id="'.\TGlobal::OutHTML($this->name).'-active-icon" style="font-size: 1.9em;"></span>
              </span>
            </div>
            <input class="form-control form-control-sm" type="text" id="'.\TGlobal::OutHTML($this->name).'" name="'.\TGlobal::OutHTML($this->name).'" maxlength="120" value="'.\TGlobal::OutHTML($this->data).'">
              <div class="input-group-append">
                <button type="button" class="btn btn-secondary" onClick="CHAMELEON.CORE.FieldIconFontSelector.openDialog(\''.\TGlobal::OutHTML($this->name).'\', \''.\TGlobal::OutJS($this->getTranslator()->trans('chameleon_system_core.field_css_icon.select_icon')).'\');">'.\TGlobal::OutHTML($this->getTranslator()->trans('chameleon_system_core.field_css_icon.select_icon')).'</button>
            </div>
        </div>';

        $iconFontCssCategoryList = $this->getIconFontCssClassList();

        $fieldHtml .= '<div id="'.\TGlobal::OutHTML($this->name).'-icon-list" style="display: none;">
            <div class="icon-search-container p-1 bg-light border-bottom" style="position: sticky; top: 0; z-index: 1000;">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control icon-search" placeholder="'.\TGlobal::OutHTML($this->getTranslator()->trans('chameleon_system_core.field_css_icon.search')).'" onkeyup="CHAMELEON.CORE.FieldIconFontSelector.search(this)">
                </div>
            </div>
            <div class="p-0 mt-3">
                <ul class="nav nav-tabs icon-tabs" role="tablist">';

        $first = true;
        foreach ($iconFontCssCategoryList as $categoryName => $iconFontCssClassList) {
            $categoryId = \TGlobal::OutHTML($this->name.'-'.str_replace(' ', '-', $categoryName));
            $activeClass = $first ? 'active' : '';
            $fieldHtml .= '<li class="nav-item">
                                <a class="nav-link '.$activeClass.'" id="'.$categoryId.'-tab" data-toggle="tab" data-coreui-toggle="tab" href="#'.$categoryId.'" role="tab" aria-controls="'.$categoryId.'" aria-selected="'.($first ? 'true' : 'false').'">
                                    '.\TGlobal::OutHTML($categoryName).'
                                </a>
                           </li>';
            $first = false;
        }

        $fieldHtml .= '</ul>
                <div class="tab-content icon-tab-content p-3">';

        $first = true;
        foreach ($iconFontCssCategoryList as $categoryName => $iconFontCssClassList) {
            $categoryId = \TGlobal::OutHTML($this->name.'-'.str_replace(' ', '-', $categoryName));
            $activeClass = $first ? 'show active' : '';
            $headerIcon = 'fas fa-font';
            if ('Font Awesome Brands' === $categoryName) {
                $headerIcon = 'fab fa-font-awesome';
            } elseif ('Custom' === $categoryName) {
                $headerIcon = 'fas fa-cog';
            }

            $fieldHtml .= '<div class="tab-pane fade '.$activeClass.'" id="'.$categoryId.'" role="tabpanel" aria-labelledby="'.$categoryId.'-tab">
                            <h6 class="mt-2 mb-3 pb-2 border-bottom font-weight-bold text-uppercase icon-category-header" style="letter-spacing: 1px; color: #495057;">
                                <i class="'.$headerIcon.' mr-2"></i>'.\TGlobal::OutHTML($categoryName).'
                            </h6>
                            <div class="row no-gutters">';
            foreach ($iconFontCssClassList as $iconFontCssClass) {
                $fieldHtml .= '<span class="col-1 '.\TGlobal::OutHTML($iconFontCssClass).'" style="font-size: 2.1em; cursor: pointer; padding: 10px; border: 1px solid #f0f3f5; min-height: 60px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease-in-out;" title="'.\TGlobal::OutHTML($iconFontCssClass).'" data-css-class="'.\TGlobal::OutHTML($iconFontCssClass).'" onclick="CHAMELEON.CORE.FieldIconFontSelector.selectIconClass(this, \''.$this->name.'\')" onmouseover="this.style.backgroundColor=\'#007bff\'; this.style.color=\'#ffffff\'; this.style.transform=\'scale(1.1)\'; this.style.zIndex=\'10\'; this.style.boxShadow=\'0 4px 8px rgba(0,0,0,0.1)\';" onmouseout="this.style.backgroundColor=\'transparent\'; this.style.color=\'inherit\'; this.style.transform=\'scale(1)\'; this.style.zIndex=\'1\'; this.style.boxShadow=\'none\';"></span>';
            }
            $fieldHtml .= '</div></div>';
            $first = false;
        }

        $fieldHtml .= '</div>';
        $fieldHtml .= '<div class="no-results-message text-center p-5" style="display: none;">
                            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                            <p class="h5 text-muted">'.$this->getTranslator()->trans('chameleon_system_core.field_css_icon.no_results').'</p>
                       </div>';
        $fieldHtml .= '</div></div>';

        return $fieldHtml;
    }

    public function GetCMSHtmlHeadIncludes(): array
    {
        $includes = parent::GetCMSHtmlHeadIncludes();

        $iconFontCssUrlList = $this->getIconFontCssUrls();

        if (null === $iconFontCssUrlList) {
            return $includes;
        }

        foreach ($iconFontCssUrlList as $iconFontCssUrl) {
            $includes[] = '<link href="'.$iconFontCssUrl.'" rel="stylesheet" type="text/css" />';
        }

        return $includes;
    }

    public function GetCMSHtmlFooterIncludes(): array
    {
        $includes = parent::GetCMSHtmlFooterIncludes();
        $includes[] = '<script src="'.URL_CMS.'/fields/FieldIconFontSelector/FieldIconFontSelector.js" type="text/javascript"></script>';

        return $includes;
    }

    protected function getIconFontCssClassList(): array
    {
        $iconFontCssUrlList = $this->getIconFontCssUrls();

        if (null === $iconFontCssUrlList) {
            return [];
        }

        $iconList = [
            'Font Awesome' => [],
            'Font Awesome Brands' => [],
            'Custom' => [],
        ];

        foreach ($iconFontCssUrlList as $iconFontCssUrl) {
            try {
                $cssClassesWithTags = $this->getCssClassExtractor()->extractCssClasses($iconFontCssUrl);
                $cssClassNames = array_keys($cssClassesWithTags);
                $cssClassNamesWithDots = $this->addDotPrefixToCssClasses($cssClassNames);
                $filteredFontAwesomeClassNames = $this->getFontAwesomeService()->filterFontAwesomeClasses($cssClassNamesWithDots);

                $fontAwesomeClassesFromThisFile = [];
                foreach ($filteredFontAwesomeClassNames as $cssClassName) {
                    $cleanClassName = str_replace([':before', '.'], ['', ' '], $cssClassName);
                    $fontAwesomeClassesFromThisFile[] = $cleanClassName;

                    if ($this->getFontAwesomeService()->isFontAwesomeBrand($cleanClassName)) {
                        $iconList['Font Awesome Brands'][] = $cleanClassName;
                    } else {
                        $iconList['Font Awesome'][] = $cleanClassName;
                    }
                }

                // Identify custom icons (those not handled by FontAwesomeService)
                foreach ($cssClassNames as $cssClassName) {
                    if ($this->getFontAwesomeService()->isExcludedClass($cssClassName)) {
                        continue;
                    }

                    $isFontAwesome = false;
                    foreach ($fontAwesomeClassesFromThisFile as $faClass) {
                        if (str_contains($faClass, $cssClassName)) {
                            $isFontAwesome = true;
                            break;
                        }
                    }

                    if (false === $isFontAwesome) {
                        $iconList['Custom'][] = $cssClassName;
                    }
                }
            } catch (Exception $e) {
                // show url error
            }
        }

        foreach ($iconList as $key => $icons) {
            $iconList[$key] = array_unique($icons);
            sort($iconList[$key]);
        }

        return array_filter($iconList);
    }

    private function addDotPrefixToCssClasses(array $cssClasses): array
    {
        return array_map(static fn ($v) => '.'.$v, $cssClasses);
    }

    protected function getIconFontCssUrls(): ?array
    {
        $iconFontCssUrls = $this->getFieldTypeConfigKey('iconFontCssUrls');

        if (null === $iconFontCssUrls) {
            return null;
        }

        if (\str_contains($iconFontCssUrls, ',')) {
            $iconFontCssUrlList = explode(',', $iconFontCssUrls);
        } else {
            $iconFontCssUrlList[] = $iconFontCssUrls;
        }

        $request = $this->getCurrentRequest();
        $host = $request->getHost();

        $filteredIconFontCssUrlList = [];
        foreach ($iconFontCssUrlList as $iconFontCssUrl) {
            if (false === \str_contains($iconFontCssUrl, 'https:')) {
                $filteredIconFontCssUrlList[] = 'https://'.$host.$iconFontCssUrl;
            }
        }

        return $filteredIconFontCssUrlList;
    }

    private function getCurrentRequest(): Request
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    private function getFontAwesomeService(): FontAwesomeServiceInterface
    {
        /* @var FontAwesomeServiceInterface */
        return ServiceLocator::get('chameleon_system_core.service.font_awesome');
    }

    private function getTranslator(): TranslatorInterface
    {
        /* @var TranslatorInterface */
        return ServiceLocator::get('chameleon_system_core.translator');
    }

    private function getCssClassExtractor(): CssClassExtractorInterface
    {
        /* @var CssClassExtractorInterface */
        return ServiceLocator::get('chameleon_system_core.service.css_class_extractor');
    }
}
