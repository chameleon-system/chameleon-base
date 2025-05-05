<?php

namespace ChameleonSystem\SecurityBundle\Voter;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template-extends Voter<string,\TCMSRecord>
 */
class CmsTableObjectVoter extends Voter
{
    public function __construct(
        readonly private Connection $connection,
        readonly private CmsTableNameVoter $cmsTableNameVoter
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (false === ($subject instanceof \TCMSRecord)) {
            return false;
        }

        if (null === $subject->table) {
            return false;
        }

        return $this->cmsTableNameVoter->supports($attribute, $subject->table);
    }

    /**
     * @param \TCMSRecord|string $subject
     *
     * @return bool|void
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var CmsUserModel|UserInterface $user */
        $user = $token->getUser();
        if (false === ($user instanceof CmsUserModel)) {
            return false;
        }

        $tableName = $subject->table;
        if (null === $tableName) {
            return false;
        }

        $hasUserIndependentAccess = $this->cmsTableNameVoter->voteOnAttribute($attribute, $tableName, $token);
        if (true === $hasUserIndependentAccess) {
            return true;
        }

        $isRowBasedRequest = in_array(
            $attribute,
            [
                CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS,
                CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT,
                CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE,
            ],
            true
        );

        if (false === $isRowBasedRequest) {
            return false;
        }

        $itemHasId = null !== $subject->id;

        if (false === $itemHasId) {
            return false;
        }

        $recordOwnerId = $this->getRecordOwner($subject->table, $subject->id);

        return $recordOwnerId === $user->getId();
    }

    private function getRecordOwner(string $table, string $id): ?string
    {
        $query = sprintf('SELECT * FROM %s WHERE `id` = :id', $this->connection->quoteIdentifier($table));
        $row = $this->connection->fetchAssociative($query, ['id' => $id]);
        if (false === $row) {
            return null;
        }

        return $row['cms_user_id'] ?? null;
    }
}
