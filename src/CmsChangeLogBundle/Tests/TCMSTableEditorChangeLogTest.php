<?php

namespace ChameleonSystem\CmsChangeLogBundle\Tests;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class TCMSTableEditorChangeLogTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInsertLogsFieldDefinitionIdsWithoutRequiringFieldIdProperties(): void
    {
        // Replace the generated parent only in this isolated test process.
        class_alias(ChangeLogInsertParentStub::class, 'TCMSTableEditorChangeLogAutoParent');

        $editor = new class extends \TCMSTableEditorChangeLog {
            public array $changes = [];
            public ?string $action = null;

            public function __construct()
            {
                $this->oTableConf = (object) ['fieldChangelogActive' => true, 'fieldName' => 'shop_category'];
            }

            public function insertFields(array $fields): void
            {
                $this->PostInsertHook($fields);
            }

            protected function createChangeSet($sAction)
            {
                $this->action = $sAction;

                return 'change-set';
            }

            protected function createChangeItems($sChangeSetId, $aDiff)
            {
                $this->changes = [$sChangeSetId, $aDiff];
            }
        };

        $mediaField = (new \ReflectionClass(\TCMSFieldExtendedLookupMedia::class))->newInstanceWithoutConstructor();
        $mediaField->oDefinition = (object) ['id' => 'media-field-definition'];
        $mediaField->data = '1';
        $emptyField = clone $mediaField;
        $emptyField->oDefinition = (object) ['id' => 'empty-field-definition'];
        $emptyField->data = '';

        self::assertFalse(property_exists($mediaField, 'id'));
        $editor->insertFields([$mediaField, $emptyField]);
        self::assertTrue($editor->parentInsertCalled);
        self::assertSame('INSERT', $editor->action);
        self::assertSame(['change-set', [['media-field-definition', '', '1']]], $editor->changes);

        $editor->oTableConf->fieldChangelogActive = false;
        $editor->changes = [];
        $editor->action = null;
        $editor->insertFields([$mediaField]);
        self::assertNull($editor->action);
        self::assertSame([], $editor->changes);
    }
}

class ChangeLogInsertParentStub
{
    public $oTableConf;
    public bool $parentInsertCalled = false;

    protected function PostInsertHook($oFields)
    {
        $this->parentInsertCalled = true;
    }
}
