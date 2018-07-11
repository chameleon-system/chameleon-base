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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class TToolsTest extends TestCase
{
    /**
     * @var bool
     */
    private $isValidEMailActualResult;
    /**
     * @var string
     */
    private $sanitize_filenameActualResult;

    /**
     * @test
     * @dataProvider getIsValidEMailData
     *
     * @param string    $emailAddress
     * @param Countable $expectedValidatorResult
     * @param bool      $expectedResult
     */
    public function it_should_check_if_email_addresses_are_valid($emailAddress, $expectedValidatorResult, $expectedResult)
    {
        $this->givenAValidator($emailAddress, $expectedValidatorResult);
        $this->whenIsValidEMailIsCalled($emailAddress);
        $this->thenItShouldReturnIfTheEMailAddressIsValid($expectedResult);
    }

    /**
     * @param string    $emailAddress
     * @param Countable $expectedValidatorResult
     */
    private function givenAValidator($emailAddress, $expectedValidatorResult)
    {
        $container = $this->prophesize('\Symfony\Component\DependencyInjection\ContainerInterface');
        $validator = $this->prophesize('\Symfony\Component\Validator\ValidatorInterface');
        $validator->validate($emailAddress, array(
            new Email(),
            new NotBlank(),
        ))->willReturn($expectedValidatorResult);
        $container->get('validator')->willReturn($validator->reveal());
        ServiceLocator::setContainer($container->reveal());
    }

    /**
     * @param string $emailAddress
     */
    private function whenIsValidEMailIsCalled($emailAddress)
    {
        $this->isValidEMailActualResult = TTools::IsValidEMail($emailAddress);
    }

    /**
     * @param bool $expected
     */
    private function thenItShouldReturnIfTheEMailAddressIsValid($expected)
    {
        static::assertEquals($expected, $this->isValidEMailActualResult);
    }

    /**
     * @return array
     */
    public function getIsValidEMailData()
    {
        return array(
            array(
                'test@example.com',
                new ArrayObject(array()),
                true,
            ),
            array(
                'test@exampleä.com',
                new ArrayObject(array()),
                true,
            ),
            array(
                'testä@example.com',
                new ArrayObject(array()),
                false,
            ),
            array(
                'test@example.com,test2@example.com',
                new ArrayObject(array()),
                false,
            ),
            array(
                'testwithescaped\@character@example.com',
                new ArrayObject(array()),
                true,
            ),
            array(
                'space test@example.com',
                new ArrayObject(array()),
                false,
            ),
            array(
                'space\ test@example.com',
                new ArrayObject(array()),
                false,
            ),
            array(
                'spacetest@ex ample.com',
                new ArrayObject(array()),
                false,
            ),
            array(
                't<>e<st>@example.com',
                new ArrayObject(array()),
                false,
            ),
        );
    }

    /**
     * @test
     * @dataProvider sanitize_filenameDataProvider
     */
    public function it_should_sanitize_filenames($filenameToSanitize, $forceExtension, $expectedSanitizedFilename)
    {
        $this->whenSanitize_filenameIsCalled($filenameToSanitize, $forceExtension);
        $this->thenItShouldReturnTheExpectedSanitizedFilename($expectedSanitizedFilename, $filenameToSanitize);
    }

    private function whenSanitize_filenameIsCalled($filenameToSanitize, $forceExtension)
    {
        $this->sanitize_filenameActualResult = TTools::sanitizeFilename($filenameToSanitize, $forceExtension);
    }

    private function thenItShouldReturnTheExpectedSanitizedFilename($expectedSanitizedFilename, $input)
    {
        $this->assertEquals($expectedSanitizedFilename, $this->sanitize_filenameActualResult, 'input:'.$input);
    }

    public function sanitize_filenameDataProvider()
    {
        return array(
            array('', null, 'none'),
            array('foo', null, 'foo'),
            array('foo.bar', null, 'foo.bar'),
            array('foé.bar', null, 'fo_.bar'),
            array('foé bár.baz', null, 'fo__b_r.baz'),
            array('foo🄬 .bar', null, 'foo__.bar'),
            array('föö', null, 'f__'),
            array('foo   ', null, 'foo'),
            array(' foo  ', null, 'foo'),
            array('  foo  ', null, 'foo'),
            array('  .  .  foo . ', null, 'foo'),
            array('foo-bar', null, 'foo-bar'),
            array('foo\/§%&bar', null, 'foo_____bar'),
            array('http://foo', null, 'foo'),
            array('xxx://foo', null, 'foo'),
            array(' xxx://foo', null, 'foo'),
            array('/////', null, '_____'),
            array('..foo', null, 'foo'),
            array('../foo', null, '_foo'),
            array('foo..', null, 'foo'),
            array('foo/..', null, 'foo_'),
            array('...', null, 'none'),
            array('foo()', null, 'foo__'),

            array('foo', 'bar', 'foo.bar'),
            array('foo.', 'bar', 'foo.bar'),
            array('foo..', 'bar', 'foo.bar'),
            array('foo.bar', 'bar', 'foo.bar'),
            array('foo.baz', 'bar', 'foo.bar'),
            array('foé.baz', 'bar', 'fo_.bar'),
            array('foo...baz', 'bar', 'foo__.bar'),
            array(' ', 'bar', 'none.bar'),
            array('"%§&&§"', 'bar', '_______.bar'),
            array(' ../such/directory/very/path', 'bar', '_such_directory_very_path.bar'),
        );
    }
}
