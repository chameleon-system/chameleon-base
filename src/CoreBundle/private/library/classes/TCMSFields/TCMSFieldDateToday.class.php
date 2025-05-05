<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * todays date (editable).
 * /**/
class TCMSFieldDateToday extends TCMSFieldDate
{
    protected function getDoctrineDataModelXml(string $namespace): string
    {
        return $this->getDoctrineRenderer('mapping/datetime.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'column' => $this->name,
            'type' => 'datetime',
            'comment' => $this->oDefinition->sqlData['translation'],
            'default' => 'CURRENT_TIMESTAMP',
        ])->render();
    }
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldDateToday';

    /**
     * indicates if the date currently stored in the database is 0000-00-00
     * we need this info to show that in the field so the users don`t get confused why
     * the field shows the current date, but the record doesn`t have it in the database.
     *
     * @var bool
     */
    protected $currentDateIsEmpty = false;

    public function GetHTML()
    {
        $html = parent::GetHTML();

        if ($this->currentDateIsEmpty) {
            $html = sprintf(
                '
                    <div class="alert alert-info">%s</div>
                    %s
                ',
                $this->getTranslator()->trans('chameleon_system_core.field_date_time.not_set'),
                $html
            );
        }

        return $html;
    }

    public function _GetHTMLValue()
    {
        $htmldate = $this->data;
        if ('0000-00-00' === $htmldate || empty($htmldate)) {
            $this->currentDateIsEmpty = true;
            $htmldate = date('Y-m-d');
        }

        return $htmldate;
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
}
