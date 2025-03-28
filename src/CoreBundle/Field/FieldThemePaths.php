<?php

namespace ChameleonSystem\CoreBundle\Field;

use ChameleonSystem\CoreBundle\ServiceLocator;

class FieldThemePaths extends \TCMSFieldText
{
    public function GetHTML(): string
    {
        $paths = array_filter(array_map('trim', explode("\n", $this->data)));

        // Invert the list: last entry is first
        $paths = array_reverse($paths);

        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('paths', $paths);
        $viewRenderer->AddSourceObject('id', $this->name);

        $html = $viewRenderer->Render('Fields/FieldThemePaths/themePathsEditor.html.twig', null, false);

        $inputField = parent::GetHTML();
        $html .= <<<HTML
<div id="{$this->name}-textarea" class="d-none">
    {$inputField}
</div>
HTML;

        return $html;
    }

    private function getViewRenderer(): \ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
