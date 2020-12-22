<?php

use PHPUnit\Framework\MockObject\MockObject;

class TCMSTableEditorManagerTest extends \PHPUnit\Framework\TestCase
{
    /** @var TCMSTableEditorManager */
    private $subject;

    /** @var MockObject<TCMSTableEditor> */
    private $wrappedTableEditor;

    public function setUp(): void
    {
        parent::setUp();

        $this->wrappedTableEditor = $this->createMock(TCMSTableEditorEndPoint::class);
        $this->wrappedTableEditor->oTable = $this->createMock(TCMSRecord::class);

        $this->subject = new TCMSTableEditorManager();
        $this->subject->oTableEditor = $this->wrappedTableEditor;
        $this->subject->AllowEditByAll(true);
    }

    public function testUsesWrappedTableEditorToInsertNewRecord(): void
    {
        $this->wrappedTableEditor
            ->expects($this->once())
            ->method('Insert')
            ->willReturn((object) [ 'id' => 1 ]);

        $this->subject->Insert();
    }

    public function testUsesWrappedTableEditorToInsertNewMltRecordIfRestrictionFieldIsMltField(): void
    {
        $this->subject->sRestrictionField = 'foo_mlt';
        $this->subject->sRestriction = 2;

        $this->wrappedTableEditor
            ->expects($this->once())
            ->method('Insert')
            ->willReturn((object) [ 'id' => 1 ]);

        $this->wrappedTableEditor
            ->expects($this->once())
            ->method('AddMLTConnection')
            ->with('foo_mlt', 2);

        $this->subject->Insert();
    }

    public function testDoesNotInsertMltRecordIfNoRestrictionFieldSet(): void
    {
        $this->wrappedTableEditor
            ->expects($this->once())
            ->method('Insert')
            ->willReturn((object) [ 'id' => 1 ]);

        $this->wrappedTableEditor
            ->expects($this->never())
            ->method('AddMLTConnection');

        $this->subject->Insert();
    }

    public function testDoesNotInsertMltRecordIfRestrictionFieldIsNotMlt(): void
    {
        $this->subject->sRestrictionField = 'foo';
        $this->subject->sRestriction = 2;

        $this->wrappedTableEditor
            ->expects($this->once())
            ->method('Insert')
            ->willReturn((object) [ 'id' => 1 ]);

        $this->wrappedTableEditor
            ->expects($this->never())
            ->method('AddMLTConnection');

        $this->subject->Insert();
    }
}
