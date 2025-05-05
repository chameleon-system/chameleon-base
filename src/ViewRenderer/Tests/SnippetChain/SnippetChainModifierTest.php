<?php

namespace ChameleonSystem\ViewRendererBundle\Tests\Library\SnippetChain;

use ChameleonSystem\ViewRenderer\SnippetChain\SnippetChainModifier;
use ChameleonSystem\ViewRenderer\SnippetChain\SnippetChainModifierDataAccessInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class SnippetChainModifierTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var SnippetChainModifierDataAccessInterface|ObjectProphecy
     */
    private $dataAccessMock;
    /**
     * @var SnippetChainModifier
     */
    private $snippetChainModifier;

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->dataAccessMock = null;
        $this->snippetChainModifier = null;
    }

    /**
     * @dataProvider getDataForTestAddToSnippetChain
     *
     * @param string $pathToAdd
     * @param string $afterThisPath
     */
    public function testAddToSnippetChain($pathToAdd, $afterThisPath, array $toTheseThemes, array $existingThemes, array $expectedResult)
    {
        $this->givenASnippetChainModifier($toTheseThemes, $existingThemes, $expectedResult);
        $this->whenICallAddToSnippetChain($pathToAdd, $afterThisPath, $toTheseThemes);
        $this->thenTheModifiedSnippetChainsShouldBePersisted($expectedResult);
    }

    private function givenASnippetChainModifier(array $toTheseThemes, array $existingThemes, array $expectedResult)
    {
        $this->dataAccessMock = $this->prophesize(SnippetChainModifierDataAccessInterface::class);
        if (count($toTheseThemes) > 0) {
            $this->dataAccessMock->getThemeData($toTheseThemes)->willReturn(array_intersect_key($existingThemes, array_flip($toTheseThemes)));
        } else {
            $this->dataAccessMock->getThemeData($toTheseThemes)->willReturn($existingThemes);
        }
        foreach ($expectedResult as $themeId => $snippetChain) {
            $this->dataAccessMock->updateSnippetChain($themeId, $snippetChain)->willReturn();
        }
        $this->snippetChainModifier = new SnippetChainModifier($this->dataAccessMock->reveal());
    }

    /**
     * @param string $pathToAdd
     * @param string $afterThisPath
     * @param array $toTheseThemes
     */
    private function whenICallAddToSnippetChain($pathToAdd, $afterThisPath, $toTheseThemes)
    {
        $this->snippetChainModifier->addToSnippetChain($pathToAdd, $afterThisPath, $toTheseThemes);
    }

    private function thenTheModifiedSnippetChainsShouldBePersisted(array $expectedResult)
    {
        if (count($expectedResult) > 0) {
            foreach ($expectedResult as $themeId => $snippetChain) {
                $this->dataAccessMock->updateSnippetChain($themeId, $snippetChain)->shouldHaveBeenCalled();
            }
        } else {
            $this->dataAccessMock->updateSnippetChain(Argument::any(), Argument::any())->shouldNotHaveBeenCalled();
        }
    }

    /**
     * @return array
     */
    public function getDataForTestAddToSnippetChain()
    {
        return [
            'add0' => [
                'foo/bar',
                null,
                [],
                [],
                [],
            ],
            'add1' => [
                '',
                null,
                [],
                [],
                [],
            ],
            'add2' => [
                'foo/bar',
                null,
                [],
                ['id1' => ''],
                ['id1' => 'foo/bar'],
            ],
            'add2a' => [
                '',
                null,
                [],
                ['id1' => ''],
                [],
            ],
            'add3' => [
                'foo/bar',
                null,
                [],
                ['id1' => 'wow/such/path'],
                ['id1' => "wow/such/path\nfoo/bar"],
            ],
            'add4' => [
                'foo/bar',
                null,
                [],
                ['id1' => "wow/such/path\nwow/more/path"],
                ['id1' => "wow/such/path\nwow/more/path\nfoo/bar"],
            ],
            'add5' => [
                'foo/bar',
                'invalid/path',
                [],
                ['id1' => ''],
                ['id1' => 'foo/bar'],
            ],
            'add6' => [
                'foo/bar',
                'invalid/path',
                [],
                ['id1' => 'wow/such/path'],
                ['id1' => "wow/such/path\nfoo/bar"],
            ],
            'add7' => [
                'foo/bar',
                'wow/such/path',
                [],
                ['id1' => "wow/such/path\nwow/more/path"],
                ['id1' => "wow/such/path\nfoo/bar\nwow/more/path"],
            ],
            'add8' => [
                'foo/bar',
                'wow/more/path',
                [],
                ['id1' => "wow/such/path\nwow/more/path"],
                ['id1' => "wow/such/path\nwow/more/path\nfoo/bar"],
            ],
            'add9' => [
                'foo/bar',
                null,
                [],
                ['id1' => 'wow/such/path', 'id2' => 'so/directory'],
                ['id1' => "wow/such/path\nfoo/bar", 'id2' => "so/directory\nfoo/bar"],
            ],
            'add10' => [
                'foo/bar',
                'wow/such/path',
                [],
                ['id1' => "wow/such/path\nwow/more/path", 'id2' => 'so/directory'],
                ['id1' => "wow/such/path\nfoo/bar\nwow/more/path", 'id2' => "so/directory\nfoo/bar"],
            ],
            'add11' => [
                'foo/bar',
                null,
                ['id2'],
                ['id1' => 'wow/such/path', 'id2' => 'so/directory'],
                ['id2' => "so/directory\nfoo/bar"],
            ],
            'add12' => [
                'foo/bar',
                'wow/such/path',
                [],
                ['id1' => "wow/such/path \nwow/more/path"],
                ['id1' => "wow/such/path\nfoo/bar\nwow/more/path"],
            ],
            'add13' => [
                'foo/bar',
                'wow/such/path',
                [],
                ['id1' => "wow/such/path/false/positive\nwow/more/path"],
                ['id1' => "wow/such/path/false/positive\nwow/more/path\nfoo/bar"],
            ],
            'add14' => [
                'foo/bar',
                'wow/such/path',
                [],
                ['id1' => "wow/such/pathfalse/positive\nwow/more/path"],
                ['id1' => "wow/such/pathfalse/positive\nwow/more/path\nfoo/bar"],
            ],
        ];
    }

    /**
     * @dataProvider getDataForTestRemoveFromSnippetChain
     *
     * @param string $pathToRemove
     */
    public function testRemoveFromSnippetChain($pathToRemove, array $fromTheseThemes, array $existingThemes, array $expectedResult)
    {
        $this->givenASnippetChainModifier($fromTheseThemes, $existingThemes, $expectedResult);
        $this->whenICallRemoveFromSnippetChain($pathToRemove, $fromTheseThemes);
        $this->thenTheModifiedSnippetChainsShouldBePersisted($expectedResult);
    }

    /**
     * @param string $pathToRemove
     * @param array $fromTheseThemes
     */
    private function whenICallRemoveFromSnippetChain($pathToRemove, $fromTheseThemes)
    {
        $this->snippetChainModifier->removeFromSnippetChain($pathToRemove, $fromTheseThemes);
    }

    /**
     * @return array
     */
    public function getDataForTestRemoveFromSnippetChain()
    {
        return [
            'remove1' => [
                'foo/bar',
                [],
                [],
                [],
            ],
            'remove2' => [
                'foo/bar',
                [],
                ['id1' => 'wow/such/path'],
                [],
            ],
            'remove3' => [
                '',
                [],
                ['id1' => 'wow/such/path'],
                [],
            ],
            'remove4' => [
                'foo/bar',
                [],
                ['id1' => 'foo/bar'],
                ['id1' => ''],
            ],
            'remove5' => [
                'foo/bar',
                [],
                ['id1' => "wow/such/path\nfoo/bar"],
                ['id1' => 'wow/such/path'],
            ],
            'remove6' => [
                'foo/bar',
                [],
                ['id1' => "foo/bar\nwow/such/path"],
                ['id1' => 'wow/such/path'],
            ],
            'remove7' => [
                'foo/bar',
                [],
                ['id1' => "wow/such/path\nfoo/bar\nwow/more/path"],
                ['id1' => "wow/such/path\nwow/more/path"],
            ],
            'remove8' => [
                'foo/bar',
                [],
                ['id1' => "wow/such/path\nfoo/bar", 'id2' => 'so/directory'],
                ['id1' => 'wow/such/path'],
            ],
            'remove9' => [
                'foo/bar',
                [],
                ['id1' => "wow/such/path\nfoo/bar", 'id2' => "so/directory\nfoo/bar"],
                ['id1' => 'wow/such/path', 'id2' => 'so/directory'],
            ],
            'remove10' => [
                'foo/bar',
                [],
                ['id1' => "foo/bar\nwow/such/path\nfoo/bar"],
                ['id1' => 'wow/such/path'],
            ],
            'remove11' => [
                'foo/bar',
                [],
                ['id1' => "foo/bar \nwow/such/path"],
                ['id1' => 'wow/such/path'],
            ],
            'remove12' => [
                'foo/bar',
                [],
                ['id1' => "foo/bar/false/positive\nwow/such/path"],
                [],
            ],
            'remove13' => [
                'foo/bar',
                [],
                ['id1' => "foo/barfalse/positive\nwow/such/path"],
                [],
            ],
            'remove14' => [
                'foo/bar',
                ['id1'],
                ['id1' => "wow/such/path\nfoo/bar", 'id2' => "so/directory\nfoo/bar"],
                ['id1' => 'wow/such/path'],
            ],
        ];
    }
}
