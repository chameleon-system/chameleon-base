<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;

class TPkgCmsStringUtilities_PathUtilsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['DOCUMENT_ROOT'] = '/my/documentroot';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_SERVER['DOCUMENT_ROOT'] = null;
    }

    public function testSavePathUnderDocumentRoot()
    {
        $path = '/my/documentroot/foo/bar';
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testUnSavePathUnderDocumentRoot()
    {
        $path = '/my/documentroot/foo/../../bar';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testUnSavePathWithMultipleSegmentsUnderDocumentRoot()
    {
        $path = '/my/documentroot/foo/../foo/../../bar';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testUnSavePathWithMultipleSegmentsAndLastSegmentUnderDocumentRoot()
    {
        $path = '/my/documentroot/foo/../foo/../bar/../../';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testSavePathComingBackUnderDocumentRoot()
    {
        $path = '/my/documentroot/foo/../../documentroot';
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testSneakyPathComingBackButNotUnderDocumentRoot()
    {
        $path = '/my/documentroot/foo/../../documentroot2';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testSneakyPathComingBackUnderDocumentRoot()
    {
        $path = '/my/documentroot/foo/../../baz/../documentroot2';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testPathComingToDocumentRoot()
    {
        $path = '/foo/bar/../../my/documentroot/foo/../../baz/../documentroot';
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testPathGettingUnderRoot()
    {
        $path = '/foo/bar/../../../my/documentroot/foo/../../baz/../documentroot2';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testRealPathUnderRoot()
    {
        $path = realpath(__DIR__.'/../testdirs/foo/bar');
        $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../testdirs/foo');
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testRealPathLinkedUnderRoot()
    {
        $path = realpath(__DIR__.'/../testdirs/foo/barlinked');
        $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../testdirs/foo/bar');
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testRealPathBothLinkedUnderRoot()
    {
        $path = realpath(__DIR__.'/../testdirs/foo/barlinked');
        $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../testdirs/foo/barlinked');
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isSaveBelowDocumentRoot($path));
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::$saveBacauseOfRealPathCheck);
    }

    public function testRelativePathWithAbsolutePath()
    {
        $path = '/foo/bar/baz';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveRelativePath($path));
    }

    public function testRelativePath()
    {
        $path = 'foo/bar/baz';
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isSaveRelativePath($path));
    }

    public function testRelativePathBelowDocumentRoot()
    {
        $path = '../../foo/bar/baz';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveRelativePath($path));
    }

    public function testRelativePathBelowDocumentRootBetweenSegments()
    {
        $path = 'foo/../../bar/baz';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveRelativePath($path));
    }

    public function testRelativePathBelowDocumentRootComingBack()
    {
        $path = 'foo/../foo/bar/../baz';
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isSaveRelativePath($path));
    }

    public function testRelativePathBelowDocumentRootNotComingBack()
    {
        $path = 'foo/foo/../../../bar/baz';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveRelativePath($path));
    }

    public function testRelativePathBelowDocumentRootNotComingBackWithDoubleSlashes()
    {
        $path = 'foo///foo/../..///../bar/baz';
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isSaveRelativePath($path));
    }

    public function testRelativePathBelowDocumentRootComingBackWithDoubleSlashes()
    {
        $path = 'foo///foo/..//../bar//baz';
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isSaveRelativePath($path));
    }

    public function testIsRelativePath()
    {
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isRelativePath('foo/bar'));
    }

    public function testIsNotRelativePath()
    {
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isRelativePath('/foo/bar'));
    }

    public function testIsNotRelativePathWithMoreThanOneSeparator()
    {
        $this->assertFalse(TPkgCmsStringUtilities_PathUtils::isRelativePath('////foo/bar'));
    }

    public function testIsRelativePathEmptyInput()
    {
        $this->assertTrue(TPkgCmsStringUtilities_PathUtils::isRelativePath(''));
    }
}
