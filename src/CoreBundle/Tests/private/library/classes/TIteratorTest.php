<?php


use PHPUnit\Framework\TestCase;

class TIteratorTest extends TestCase
{

    public function testShouldBeAbleToIterateManually(): void
    {
        $iterator = $this->iterator([ 'foo', 'bar', 'baz' ]);

        $this->assertEquals(0, $iterator->_itemPointer);
        $this->assertEquals('foo', $iterator->current());
        $this->assertEquals('foo', $iterator->next());

        $this->assertEquals(1, $iterator->_itemPointer);
        $this->assertEquals('bar', $iterator->current());
        $this->assertEquals('bar', $iterator->next());

        $this->assertEquals(2, $iterator->_itemPointer);
        $this->assertEquals('baz', $iterator->current());
        $this->assertEquals('baz', $iterator->next());

        $this->assertEquals(3, $iterator->_itemPointer);
        $this->assertEquals(false, $iterator->current());
        $this->assertEquals(false, $iterator->next());
    }

    public function testCanAppendItems(): void
    {
        $iterator = $this->iterator([ 'foo', 'bar', 'baz' ]);

        $test = 'test';
        $iterator->AddItem($test);

        $iterator->next();
        $iterator->next();
        $iterator->next();
        $this->assertEquals(3, $iterator->_itemPointer);
        $this->assertEquals('test', $iterator->current());
    }

    public function testCanPrependItems(): void
    {
        $iterator = $this->iterator([ 'foo', 'bar', 'baz' ]);

        $test = 'test';
        $iterator->AddItemToStart($test);

        $this->assertEquals(0, $iterator->_itemPointer);
        $this->assertEquals('test', $iterator->current());
    }

    public function testCanBeReversed(): void
    {
        $iterator = $this->iterator([ 'foo', 'bar', 'baz' ]);
        $iterator->ReverseItemList();

        $this->assertEquals(0, $iterator->_itemPointer);
        $this->assertEquals('baz', $iterator->next());
        $this->assertEquals('bar', $iterator->next());
        $this->assertEquals('foo', $iterator->next());
    }

    public function testCanGoToStart(): void
    {
        $iterator = $this->iterator([ 'foo', 'bar', 'baz' ]);

        $iterator->next();
        $iterator->next();
        $this->assertEquals(2, $iterator->_itemPointer);

        $iterator->GoToStart();
        $this->assertEquals(0, $iterator->_itemPointer);
    }

    public function testCanGoToEnd(): void
    {
        $iterator = $this->iterator([ 'foo', 'bar', 'baz' ]);

        $this->assertEquals(0, $iterator->_itemPointer);

        $iterator->GoToEnd();
        $this->assertEquals(2, $iterator->_itemPointer);
        $this->assertEquals('baz', $iterator->current());
    }

    public function testCanBeSortedByCallback(): void
    {
        $iterator = $this->iterator([ 3, 7, 1 ,9 ]);
        $iterator->usort(function($a, $b) {
            return $a - $b;
        });

        $this->assertEquals(1, $iterator->next());
        $this->assertEquals(3, $iterator->next());
        $this->assertEquals(7, $iterator->next());
        $this->assertEquals(9, $iterator->next());
    }

    public function testCanFindOneItemBasedOnObjectProperty(): void
    {
        $foo = (object) [ 'id' => 'foo' ];
        $bar = (object) [ 'id' => 'bar' ];
        $baz = (object) [ 'id' => 'baz' ];
        $iterator = $this->iterator([ $foo, $bar, $baz ]);

        $this->assertSame($bar, $iterator->FindItemWithProperty('id', 'bar'));
    }

    public function testCanFindMultipleItemBasedOnObjectProperty(): void
    {
        $foo = (object) [ 'id' => 'foo', 'tag' => 1 ];
        $bar = (object) [ 'id' => 'bar', 'tag' => 2 ];
        $baz = (object) [ 'id' => 'baz', 'tag' => 1 ];
        $iterator = $this->iterator([ $foo, $bar, $baz ]);

        $items = $iterator->FindItemsWithProperty('tag', 1);
        $this->assertEquals(2, $items->Length());
        $this->assertEquals('foo', $items->next()->id);
        $this->assertEquals('baz', $items->next()->id);
    }

    public function testCanDeleteItemBasedOnObjectProperty(): void
    {
        $foo = (object) [ 'id' => 'foo' ];
        $bar = (object) [ 'id' => 'bar' ];
        $baz = (object) [ 'id' => 'baz' ];
        $iterator = $this->iterator([ $foo, $bar, $baz ]);

        $iterator->RemoveItem('id', 'bar');

        $this->assertEquals($foo, $iterator->next());
        $this->assertEquals($baz, $iterator->next());
    }

    public function testCanBeIterated(): void
    {
        $iterator = $this->iterator([ 'foo', 'bar', 'baz' ]);

        foreach ($iterator as $index => $item) {
            switch ($index) {
                case 0:
                    $this->assertEquals('foo', $item);
                    break;
                case 1:
                    $this->assertEquals('bar', $item);
                    break;
                case 2:
                    $this->assertEquals('baz', $item);
                    break;
                default:
                    throw new \Exception('Invalid index ' . index);
            }
        }
    }

    public function testResetsItemPointerBeforeIterating(): void
    {
        $iterator = $this->iterator([ 'foo', 'bar', 'baz' ]);
        $iterator->next();

        $this->assertEquals(1, $iterator->_itemPointer);
        $i = 0;
        foreach ($iterator as $_) {
            $i++;
        }

        // If the item pointer wasn't reset we'd assume to see 2 here
        // as the first item would be skipped.
        $this->assertEquals(3, $i);
    }

    public function testCanBeConvertedToArrayUsingIteratorMethods(): void
    {
        $iterator = $this->iterator([ 'foo', 'bar', 'baz' ]);
        $array = iterator_to_array($iterator);
        $this->assertIsArray($array);
        $this->assertEquals('foo', $array[0]);
        $this->assertEquals('bar', $array[1]);
        $this->assertEquals('baz', $array[2]);
    }

    private function iterator(array $items): TIterator
    {
        $iterator = new TIterator();
        foreach ($items as $item) {
            $iterator->AddItem($item);
        }
        return $iterator;
    }

}
