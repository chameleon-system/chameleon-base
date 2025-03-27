<?php

namespace ChameleonSystem\CoreBundle\Field;

class FieldMapperChain extends \TCMSFieldText
{
    /**
     * {@inheritdoc}
     */
    public function GetHTML(): string
    {
        $chains = [];

        // Parse the data lines into key => [mapper1, mapper2, ...]
        $lines = explode("\n", $this->data);
        foreach ($lines as $line) {
            $line = trim($line);
            if ('' === $line || false === strpos($line, '=')) {
                continue;
            }

            [$key, $mapperList] = explode('=', $line, 2);
            $key = trim($key);
            $mappers = array_map('trim', explode(',', $mapperList));
            sort($mappers, SORT_STRING | SORT_FLAG_CASE); // Sort mapper list alphabetically

            $chains[$key] = $mappers;
        }

        // Sort the whole chain by key (view name)
        ksort($chains, SORT_STRING | SORT_FLAG_CASE);

        // Build HTML table
        $overviewHtml = '<div class="table-responsive">';
        $overviewHtml .= '<table class="table table-striped table-bordered">';
        $overviewHtml .= '<thead><tr><th>View</th><th>Mapper</th></tr></thead><tbody>';

        foreach ($chains as $key => $mappers) {
            $overviewHtml .= '<tr>';
            $overviewHtml .= '<td><i class="fas fa-eye"></i> '.htmlspecialchars($key).'</td>';
            $overviewHtml .= '<td><ul class="list-group list-group-flush p-0 m-0">';
            foreach ($mappers as $mapper) {
                $overviewHtml .= '<li class="list-group-item list-group-item-action" style="background-color: transparent;"><i class="fas fa-code-branch"></i> '.htmlspecialchars($mapper).'</li>';
            }
            $overviewHtml .= '</ul></td>';
            $overviewHtml .= '</tr>';
        }

        $overviewHtml .= '</tbody></table>';
        $overviewHtml .= '</div>';

        $html = parent::GetHTML();

        return $overviewHtml.$html;
    }
}
