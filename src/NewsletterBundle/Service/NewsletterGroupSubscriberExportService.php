<?php
namespace ChameleonSystem\NewsletterBundle\Service;

use Doctrine\DBAL\Connection;
use TdbPkgNewsletterGroup;

class NewsletterGroupSubscriberExportService
{
    public function __construct(
        private readonly Connection $connection)
    {
    }

    public function exportSubscriberAsCsv(string $newsletterGroupId): array
    {
        $newsletterGroup = TdbPkgNewsletterGroup ::GetNewInstance();
        if (false === $newsletterGroup->Load($newsletterGroupId)) {
            return [];
        }

        $exportFields = $this->getExportFields();

        $subscriberList = [];
        $query = "SELECT 
                    `data_extranet_salutation`.`name` AS salutation,
                    `pkg_newsletter_user`.*
               FROM `pkg_newsletter_user`
          LEFT JOIN `data_extranet_salutation` ON (`pkg_newsletter_user`.`data_extranet_salutation_id` = `data_extranet_salutation`.`id`)
          LEFT JOIN `pkg_newsletter_user_pkg_newsletter_group_mlt` ON (`pkg_newsletter_user_pkg_newsletter_group_mlt`.`source_id` = `pkg_newsletter_user`.`id`)
              WHERE `pkg_newsletter_user`.`optin` = '1'
                AND `pkg_newsletter_user_pkg_newsletter_group_mlt`.`target_id` = ".$this->connection->quote($newsletterGroupId).";
           ";

        $newsletterSubscriberList = \TdbPkgNewsletterUserList::GetList($query);
        while ($newsletterSubscriber = $newsletterSubscriberList->Next()) {
            $subscriberListItem = [];
            foreach($exportFields as $field) {
                if (isset($newsletterSubscriber->sqlData[$field])) {
                    $subscriberListItem[$field] = $newsletterSubscriber->sqlData[$field];
                }
            }

            $subscriberList[$newsletterSubscriber->sqlData['cmsident']] = $subscriberListItem;
        }

        $extranetGroups = $newsletterGroup->GetFieldDataExtranetGroupIdList();
        foreach ($extranetGroups as $extranetGroupId) {
            $query = "SELECT 
                        `data_extranet_salutation`.`name` AS salutation,
                        `pkg_newsletter_user`.*
                   FROM `pkg_newsletter_user`
              LEFT JOIN `data_extranet_salutation` ON (`pkg_newsletter_user`.`data_extranet_salutation_id` = `data_extranet_salutation`.`id`)
              LEFT JOIN `data_extranet_user` ON (`pkg_newsletter_user`.`data_extranet_user_id` = `data_extranet_user`.`id`)
              LEFT JOIN `data_extranet_user_data_extranet_group_mlt` ON (`data_extranet_user_data_extranet_group_mlt`.`source_id` = `data_extranet_user`.`id`)                   
                  WHERE `pkg_newsletter_user`.`optin` = '1'
                    AND `data_extranet_user_data_extranet_group_mlt`.`target_id` = ".$this->connection->quote($extranetGroupId).";
               ";

            $newsletterSubscriberList = \TdbPkgNewsletterUserList::GetList($query);
            while ($newsletterSubscriber = $newsletterSubscriberList->Next()) {
                $subscriberListItem = [];
                foreach($exportFields as $field) {
                    if (isset($newsletterSubscriber->sqlData[$field])) {
                        $subscriberListItem[$field] = $newsletterSubscriber->sqlData[$field];
                    }
                }

                $subscriberList[] = $subscriberListItem;
            }
        }

        return $subscriberList;
    }

    protected function getExportFields(): array
    {
        return [
            'salutation',
            'firstname',
            'lastname',
            'email',
            'signup_date'
        ];
    }
}