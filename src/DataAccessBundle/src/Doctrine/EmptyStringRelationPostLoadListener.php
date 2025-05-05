<?php

namespace ChameleonSystem\DataAccessBundle\Doctrine;

use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

// In chameleon, foreign key fields representing no relation are stored as empty strings instead of using null to indicate no relation.
// This listener will set the relation to null if the foreign key field is an empty string. Otherwise, doctrine will assume a relation
// exists with the id '' and try to load the related entity resulting in an exception when accessing the relation.
class EmptyStringRelationPostLoadListener
{
    /**
     * @return PostLoadEventArgs
     */
    public function postLoad(PostLoadEventArgs $event)
    {
        $metadata = $event->getObjectManager()->getClassMetadata($event->getObject()::class);
        $object = $event->getObject();
        foreach ($metadata->getAssociationMappings() as $mapping) {
            if (ClassMetadataInfo::MANY_TO_ONE !== $mapping['type']) {
                continue;
            }
            $name = $mapping['fieldName'];
            $relation = $object->{'get'.ucfirst($name)}();
            if ('' === $relation->getId()) {
                $object->{'set'.ucfirst($name)}(null);
            }
        }

        return $event;
    }
}
