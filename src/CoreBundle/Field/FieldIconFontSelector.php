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

        $iconFontCssClassList = $this->getIconFontCssClassList();

        $fieldHtml .= '<div id="'.\TGlobal::OutHTML($this->name).'-icon-list" style="display: none;">
            <div class="mt-4 ml-1 row">';
        foreach ($iconFontCssClassList as $iconFontCssClass) {
            $fieldHtml .= '<span class="col-1 '.\TGlobal::OutHTML($iconFontCssClass).'" style="font-size: 2.1em; cursor: pointer; padding-top: 4px; border: 1px solid #f0f3f5; min-height: 40px;" title="'.\TGlobal::OutHTML($iconFontCssClass).'" data-css-class="'.\TGlobal::OutHTML($iconFontCssClass).'" onclick="CHAMELEON.CORE.FieldIconFontSelector.selectIconClass(this, \''.$this->name.'\')"></span>';
        }
        $fieldHtml .= '</div>
        </div>';

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

        $filteredClassnames = [];

        foreach ($iconFontCssUrlList as $iconFontCssUrl) {
            try {
                $cssClassNames = $this->getCssClassExtractor()->extractCssClasses($iconFontCssUrl);
                $cssClassNames = $this->getFontAwesomeService()->filterFontAwesomeClasses($cssClassNames);

                foreach ($cssClassNames as $cssClassName) {
                    if (\str_starts_with($cssClassName, '.')) {
                        $filteredClassnames[] = str_replace([':before', '.'], ['', ' '], $cssClassName);
                    }
                }
            } catch (Exception $e) {
                // show url error
            }
        }

        return $filteredClassnames;
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
        return ServiceLocator::get('chameleon_system_core.service.font_awesome');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('chameleon_system_core.translator');
    }

    private function getCssClassExtractor(): CssClassExtractorInterface
    {
        return ServiceLocator::get('chameleon_system_core.service.css_class_extractor');
    }
}
