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
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TToolsTest extends TestCase
{
    use ProphecyTrait;

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
     *
     * @dataProvider getIsValidEMailData
     *
     * @param string $emailAddress
     * @param Countable $expectedValidatorResult
     * @param bool $expectedResult
     */
    public function itShouldCheckIfEmailAddressesAreValid($emailAddress, $expectedValidatorResult, $expectedResult)
    {
        $this->givenAValidator($emailAddress, $expectedValidatorResult);
        $this->whenIsValidEMailIsCalled($emailAddress);
        $this->thenItShouldReturnIfTheEMailAddressIsValid($expectedResult);
    }

    /**
     * @param string $emailAddress
     * @param Countable $expectedValidatorResult
     */
    private function givenAValidator($emailAddress, $expectedValidatorResult)
    {
        $container = $this->prophesize(ContainerInterface::class);
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate($emailAddress, [
            new Email(),
            new NotBlank(),
        ])->willReturn($expectedValidatorResult);
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
        return [
            [
                'test@example.com',
                new ArrayObject([]),
                true,
            ],
            [
                'test@exampleä.com',
                new ArrayObject([]),
                true,
            ],
            [
                'testä@example.com',
                new ArrayObject([]),
                false,
            ],
            [
                'test@example.com,test2@example.com',
                new ArrayObject([]),
                false,
            ],
            [
                'testwithescaped\@character@example.com',
                new ArrayObject([]),
                true,
            ],
            [
                'space test@example.com',
                new ArrayObject([]),
                false,
            ],
            [
                'space\ test@example.com',
                new ArrayObject([]),
                false,
            ],
            [
                'spacetest@ex ample.com',
                new ArrayObject([]),
                false,
            ],
            [
                't<>e<st>@example.com',
                new ArrayObject([]),
                false,
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider sanitize_filenameDataProvider
     */
    public function itShouldSanitizeFilenames($filenameToSanitize, $forceExtension, $expectedSanitizedFilename)
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
        return [
            ['', null, 'none'],
            ['foo', null, 'foo'],
            ['foo.bar', null, 'foo.bar'],
            ['foé.bar', null, 'fo_.bar'],
            ['foé bár.baz', null, 'fo__b_r.baz'],
            ['foo🄬 .bar', null, 'foo__.bar'],
            ['föö', null, 'f__'],
            ['foo   ', null, 'foo'],
            [' foo  ', null, 'foo'],
            ['  foo  ', null, 'foo'],
            ['  .  .  foo . ', null, 'foo'],
            ['foo-bar', null, 'foo-bar'],
            ['foo\/§%&bar', null, 'foo_____bar'],
            ['http://foo', null, 'foo'],
            ['xxx://foo', null, 'foo'],
            [' xxx://foo', null, 'foo'],
            ['/////', null, '_____'],
            ['..foo', null, 'foo'],
            ['../foo', null, '_foo'],
            ['foo..', null, 'foo'],
            ['foo/..', null, 'foo_'],
            ['...', null, 'none'],
            ['foo()', null, 'foo__'],

            ['foo', 'bar', 'foo.bar'],
            ['foo.', 'bar', 'foo.bar'],
            ['foo..', 'bar', 'foo.bar'],
            ['foo.bar', 'bar', 'foo.bar'],
            ['foo.baz', 'bar', 'foo.bar'],
            ['foé.baz', 'bar', 'fo_.bar'],
            ['foo...baz', 'bar', 'foo__.bar'],
            [' ', 'bar', 'none.bar'],
            ['"%§&&§"', 'bar', '_______.bar'],
            [' ../such/directory/very/path', 'bar', '_such_directory_very_path.bar'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider allowedDocumentFileTypesDataProvider
     */
    public function itShouldContainExpectedAllowedDocumentFileTypes($extension)
    {
        $this->assertContains($extension, TTools::GetAllowedDocumentFileTypes());
    }

    /**
     * @test
     *
     * @dataProvider disallowedDocumentFileTypesDataProvider
     */
    public function itShouldNotContainDisallowedDocumentFileTypes($extension)
    {
        $this->assertNotContains($extension, TTools::GetAllowedDocumentFileTypes());
    }

    public function allowedDocumentFileTypesDataProvider()
    {
        return [
            ['pdf'],
            ['docx'],
            ['xlsx'],
            ['zip'],
            ['jpg'],
            ['mp4'],
            ['tar.gz'],
        ];
    }

    public function disallowedDocumentFileTypesDataProvider()
    {
        return [
            ['php'],
            ['html'],
            ['css'],
            ['js'],
            ['exe'],
            ['jar'],
            ['sql'],
            ['conf'],
        ];
    }
}
