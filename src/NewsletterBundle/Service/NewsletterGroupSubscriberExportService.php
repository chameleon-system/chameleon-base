<?php

namespace ChameleonSystem\NewsletterBundle\Service;

use Doctrine\DBAL\Connection;

class NewsletterGroupSubscriberExportService
{
    public function __construct(
        private readonly Connection $connection)
    {
    }

    public function exportSubscriberAsCsv(string $newsletterGroupId): array
    {
        $newsletterGroup = \TdbPkgNewsletterGroup::GetNewInstance();
        if (false === $newsletterGroup->Load($newsletterGroupId)) {
            return [];
        }

        $subscriberList = $this->getSubscriberByNewsletterGroup($newsletterGroupId);

        $extranetGroupIds = $newsletterGroup->GetFieldDataExtranetGroupIdList();

        return array_merge($subscriberList, $this->getSubscriberByExtranetGroupIds($extranetGroupIds));
    }

    protected function getSubscriberByNewsletterGroup(string $newsletterGroupId): array
    {
        $subscriberList = [];
        $exportFields = $this->getExportFields();

        $query = "SELECT 
                    `data_extranet_salutation`.`name` AS salutation,
                    `pkg_newsletter_user`.*
               FROM `pkg_newsletter_user`
          LEFT JOIN `data_extranet_salutation` ON (`pkg_newsletter_user`.`data_extranet_salutation_id` = `data_extranet_salutation`.`id`)
          LEFT JOIN `pkg_newsletter_user_pkg_newsletter_group_mlt` ON (`pkg_newsletter_user_pkg_newsletter_group_mlt`.`source_id` = `pkg_newsletter_user`.`id`)
              WHERE `pkg_newsletter_user`.`optin` = '1'
                AND `pkg_newsletter_user_pkg_newsletter_group_mlt`.`target_id` = ".$this->connection->quote($newsletterGroupId).';
           ';

        $newsletterSubscriberList = \TdbPkgNewsletterUserList::GetList($query);
        while ($newsletterSubscriber = $newsletterSubscriberList->Next()) {
            $subscriberListItem = [];
            foreach ($exportFields as $field) {
                if (isset($newsletterSubscriber->sqlData[$field])) {
                    $subscriberListItem[$field] = $newsletterSubscriber->sqlData[$field];
                }
            }

            $subscriberList[$newsletterSubscriber->sqlData['cmsident']] = $subscriberListItem;
        }

        return $subscriberList;
    }

    protected function getSubscriberByExtranetGroupIds(array $extranetGroupIds): array
    {
        $subscriberList = [];
        $exportFields = $this->getExportFields();

        foreach ($extranetGroupIds as $extranetGroupId) {
            $query = "SELECT 
                        `data_extranet_salutation`.`name` AS salutation,
                        `pkg_newsletter_user`.*
                   FROM `pkg_newsletter_user`
              LEFT JOIN `data_extranet_salutation` ON (`pkg_newsletter_user`.`data_extranet_salutation_id` = `data_extranet_salutation`.`id`)
              LEFT JOIN `data_extranet_user` ON (`pkg_newsletter_user`.`data_extranet_user_id` = `data_extranet_user`.`id`)
              LEFT JOIN `data_extranet_user_data_extranet_group_mlt` ON (`data_extranet_user_data_extranet_group_mlt`.`source_id` = `data_extranet_user`.`id`)                   
                  WHERE `pkg_newsletter_user`.`optin` = '1'
                    AND `data_extranet_user_data_extranet_group_mlt`.`target_id` = ".$this->connection->quote($extranetGroupId).';
               ';

            $newsletterSubscriberList = \TdbPkgNewsletterUserList::GetList($query);
            while ($newsletterSubscriber = $newsletterSubscriberList->Next()) {
                $subscriberListItem = [];
                foreach ($exportFields as $field) {
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
            'signup_date',
        ];
    }
}
