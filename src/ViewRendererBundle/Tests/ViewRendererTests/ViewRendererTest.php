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
use ChameleonSystem\ViewRendererBundle\objects\DataMappingService;
use ChameleonSystem\ViewRendererBundle\objects\DataMappingServiceHelperFactory;
use PHPUnit\Framework\TestCase;

class ViewRendererTest extends TestCase
{
    /**
     * @var ViewRenderer
     */
    protected $oViewRenderer = null;
    protected $oTestRenderer = null;

    public function setUp()
    {
        $this->mockContainer();

        $dataMappingService = new DataMappingService(new DataMappingServiceHelperFactory());
        $this->oTestRenderer = new SnippetRenderer('testView');
        $this->oViewRenderer = new ViewRenderer($dataMappingService, $this->oTestRenderer);
    }

    public function tearDown()
    {
        $this->oViewRenderer = null;
        $this->oTestRenderer = null;
    }

    private function mockContainer()
    {
        $container = $this->createMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->will($this->returnCallback(function ($value) {
                switch ($value) {
                    case 'chameleon_system_core.debug.show_view_source_html_hints':
                        return false;
                        break;
                    default:
                        throw new \InvalidArgumentException('Invalid container parameter requested: '.$value);
                }
            }));
        /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
        ServiceLocator::setContainer($container);
    }

    public function testRenderNoMappers()
    {
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);
        $expected = array('testView', array());
        $this->assertEquals($expected, $result);
    }

    public function testOneMapper()
    {
        $this->oViewRenderer->AddMappers(array(new ArticleToTitleMapper()));
        $this->oViewRenderer->AddSourceObject('article', new Article());
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);
        $this->assertEquals('testView', $result[0]);
        $this->assertTrue(array_key_exists('title', $result[1]));
        $this->assertEquals($result[1]['title'], 'my nice title');
    }

    public function testChainedModifyingMappers()
    {
        $this->oViewRenderer->AddMappers(array(new ArticleToTitleMapper(), new ModifyingViewMapper()));
        $this->oViewRenderer->AddSourceObject('article', new Article());
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);
        $this->assertEquals('testView', $result[0]);
        $this->assertTrue(array_key_exists('title', $result[1]));
        $this->assertEquals($result[1]['title'], 'my nice title_modified');
    }

    public function testDefaultValueInRequirements()
    {
        $this->oViewRenderer->AddMappers(array(new DefaultValueMapper()));
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);
        $this->assertEquals('testView', $result[0]);
        $this->assertTrue(array_key_exists('title', $result[1]));
        $this->assertEquals($result[1]['title'], 'default title');
    }

    /**
     * @expectedException \MapperException
     */
    public function testUnsatisfiedMapper()
    {
        $this->oViewRenderer->AddMappers(array(new ModifyingViewMapper()));
        $this->oViewRenderer->AddSourceObject('article', new Article());
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);
    }

    public function testChainedMappers()
    {
        $this->oViewRenderer->AddMappers(array(new ArticleToTitleMapper(), new UserToUsernameMapper()));
        $this->oViewRenderer->AddSourceObject('article', new Article());
        $this->oViewRenderer->AddSourceObject('user', new User());
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);
        $this->assertEquals('testView', $result[0]);
        $this->assertTrue(array_key_exists('title', $result[1]));
        $this->assertEquals($result[1]['title'], 'my nice title');
        $this->assertTrue(array_key_exists('username', $result[1]));
        $this->assertEquals($result[1]['username'], 'username');
    }

    /**
     * @expectedException \MapperException
     */
    public function testAddNotExistingMapperFromPath()
    {
        $this->oViewRenderer->AddMapperFromPath('Mapper', 'idontexist/mapper.php');
    }

    public function testAddExistingMapperFromPath()
    {
        $this->oViewRenderer->AddMapperFromPath('ArticleToTitleMapper', dirname(__FILE__).'/../testdata/mappers/ArticleToTitleMapper.class.php');
        $this->oViewRenderer->AddSourceObject('article', new Article());
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);
        $expected = array('testView', array('title' => 'my nice title'));
        $this->assertEquals('testView', $result[0]);
        $this->assertTrue(array_key_exists('title', $result[1]));
        $this->assertEquals($result[1]['title'], 'my nice title');
    }

    public function testObjectInputValidType()
    {
        $oTestObject = new stdClass();

        $oVisitor = new MapperVisitor(array('oTest' => $oTestObject));
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest', 'stdClass');

        $oVisitor->SetCurrentRequirements($oRequirements);

        $oSource = $oVisitor->GetSourceObject('oTest');
        $this->assertEquals($oTestObject, $oSource);
    }

    /**
     * @expectedException \MapperException
     */
    public function testObjectInputInValidType()
    {
        $oTestObject = new stdClass();

        $oVisitor = new MapperVisitor(array('oTest' => $oTestObject));
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest', 'footype');

        $oVisitor->SetCurrentRequirements($oRequirements);

        $oSource = $oVisitor->GetSourceObject('oTest');
    }

    public function testObjectInputNoType()
    {
        $oTestObject = new stdClass();
        $oVisitor = new MapperVisitor(array('oTest' => $oTestObject));
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest');

        $oVisitor->SetCurrentRequirements($oRequirements);

        $oSource = $oVisitor->GetSourceObject('oTest');
        $this->assertEquals($oTestObject, $oSource);
    }

    public function testNonObjectInputValidType()
    {
        $oTestObject = 'bla';
        $oVisitor = new MapperVisitor(array('oTest' => $oTestObject));
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest', 'string');

        $oVisitor->SetCurrentRequirements($oRequirements);

        $oSource = $oVisitor->GetSourceObject('oTest');
        $this->assertEquals($oTestObject, $oSource);
    }

    public function testNonObjectInputInValidType()
    {
        $oTestObject = 'bla';
        $oVisitor = new MapperVisitor(array('oTest' => $oTestObject));
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest', 'int');

        $oVisitor->SetCurrentRequirements($oRequirements);

        $oSource = $oVisitor->GetSourceObject('oTest');
        $this->assertEquals($oTestObject, $oSource);
    }

    public function testNonObjectInputNoType()
    {
        $oTestObject = 'bla';
        $oVisitor = new MapperVisitor(array('oTest' => $oTestObject));
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest');
        $oVisitor->SetCurrentRequirements($oRequirements);
        $oSource = $oVisitor->GetSourceObject('oTest');
        $this->assertEquals($oTestObject, $oSource);
    }

    public function testMissingInputWithDefault()
    {
        $oVisitor = new MapperVisitor();
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest', 'int', 5);
        $oVisitor->SetCurrentRequirements($oRequirements);
        $oSource = $oVisitor->GetSourceObject('oTest');
        $this->assertEquals(5, $oSource);
    }

    public function testInputWithDefault()
    {
        $oTestObject = 10;
        $oVisitor = new MapperVisitor(array('oTest' => $oTestObject));
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest', 'int', 5);
        $oVisitor->SetCurrentRequirements($oRequirements);
        $oSource = $oVisitor->GetSourceObject('oTest');
        $this->assertEquals(10, $oSource);
    }

    /**
     * @expectedException \MapperException
     */
    public function testMissingNonOptionalRequirement()
    {
        $oVisitor = new MapperVisitor();
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest');
        $oVisitor->SetCurrentRequirements($oRequirements);
        $oSource = $oVisitor->GetSourceObject('oTest');
    }

    /**
     * @expectedException \MapperException
     */
    public function testMissingExplicitNonOptionalRequirement()
    {
        $oVisitor = new MapperVisitor();
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest', null, null, false);
        $oVisitor->SetCurrentRequirements($oRequirements);
        $oSource = $oVisitor->GetSourceObject('oTest');
    }

    public function testMissingOptionalRequirement()
    {
        $oVisitor = new MapperVisitor();
        $oRequirements = new MapperRequirements();
        $oRequirements->NeedsSourceObject('oTest', null, null, true);
        $oVisitor->SetCurrentRequirements($oRequirements);
        $oSource = $oVisitor->GetSourceObject('oTest');
        $this->assertEquals(null, $oSource);
    }

    public function testSourceObjectAvailableWithoutMapper()
    {
        $oTestRenderer = new SnippetRenderer('test');
        $this->oViewRenderer->AddSourceObject('foo', 'bar');
        $content = $this->oViewRenderer->Render('test', $oTestRenderer, false);

        $this->assertEquals($content[1]['foo'], 'bar');
    }

    public function testSourceObjectAvailableWithMapper()
    {
        $oTestRenderer = new SnippetRenderer('test');
        $this->oViewRenderer->AddSourceObject('foo', 'bar');
        $this->oViewRenderer->AddMapper(new EmptyMapperThatDoesNothing());
        $content = $this->oViewRenderer->Render('test', $oTestRenderer, false);

        $this->assertTrue(array_key_exists('foo', $content[1]));
        $this->assertEquals($content[1]['foo'], 'bar');
    }

    public function testMapperTransformations()
    {
        $mapper = new ArticleToTitleMapper();
        $this->oViewRenderer->AddMapper($mapper, array('title' => 'transformedTitle'));
        $this->oViewRenderer->AddSourceObject('article', new Article());
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);
        $this->assertEquals('testView', $result[0]);
        $this->assertTrue(array_key_exists('transformedTitle', $result[1]));
        $this->assertEquals($result[1]['transformedTitle'], 'my nice title');
    }

    public function testMapToArray()
    {
        $mapper = new ArticleToTitleMapper();
        $this->oViewRenderer->AddMapper($mapper, null, 'aFoo');
        $this->oViewRenderer->AddSourceObject('article', new Article());
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);

        $this->assertEquals('testView', $result[0]);
        $this->assertTrue(array_key_exists('aFoo', $result[1]));
        $this->assertEquals($result[1]['aFoo'], array('title' => 'my nice title'));
    }

    public function testMapToArrayAndTransformation()
    {
        $mapper = new ArticleToTitleMapper();
        $this->oViewRenderer->AddMapper($mapper, array('title' => 'transformedTitle'), 'aFoo');
        $this->oViewRenderer->AddSourceObject('article', new Article());
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);

        $this->assertEquals('testView', $result[0]);
        $this->assertTrue(array_key_exists('aFoo', $result[1]));
        $this->assertEquals($result[1]['aFoo'], array('transformedTitle' => 'my nice title'));
    }

    public function testMapToArrayAndTransformationAndReset()
    {
        $mapper = new ArticleToTitleMapper();
        $this->oViewRenderer->AddMapper($mapper, array('title' => 'transformedTitle'), 'aFoo');
        $mapper = new ArticleToTitleMapper();
        $this->oViewRenderer->AddMapper($mapper);
        $this->oViewRenderer->AddSourceObject('article', new Article());
        $result = $this->oViewRenderer->Render('testView', $this->oTestRenderer);

        $this->assertEquals('testView', $result[0]);
        $this->assertTrue(array_key_exists('aFoo', $result[1]));
        $this->assertEquals($result[1]['aFoo'], array('transformedTitle' => 'my nice title'));
        $this->assertTrue(array_key_exists('title', $result[1]));
        $this->assertEquals($result[1]['title'], 'my nice title');
    }
}
