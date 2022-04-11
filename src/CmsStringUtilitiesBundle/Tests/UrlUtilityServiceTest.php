<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ShopBundle\Tests;

use ChameleonSystem\CmsStringUtilitiesBundle\Service\UrlUtilityService;
use PHPUnit\Framework\TestCase;

class UrlUtilityServiceTest extends TestCase
{
    private $url;
    private $parameter;
    /**
     * @var UrlUtilityService
     */
    private $service;
    private $responseUrl;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->url = null;
        $this->parameter = null;
        $this->service = null;
        $this->responseUrl = null;
    }

    /**
     * @test
     * @dataProvider dataProviderAddParameterToUrl
     *
     * @param $url
     * @param $parameter
     * @param $expectedUrl
     */
    public function it_should_add_parameters_to_a_url($url, $parameter, $expectedUrl)
    {
        $this->given_the_url($url);
        $this->given_the_parameters($parameter);
        $this->given_an_instance_of_the_service();
        $this->when_we_call_addParameterToUrl();
        $this->then_we_expect_an_url_matching($expectedUrl);
    }

    private function given_the_url($url)
    {
        $this->url = $url;
    }

    private function given_the_parameters($parameter)
    {
        $this->parameter = $parameter;
    }

    private function given_an_instance_of_the_service()
    {
        $this->service = new UrlUtilityService();
    }

    private function when_we_call_addParameterToUrl()
    {
        $this->responseUrl = $this->service->addParameterToUrl($this->url, $this->parameter);
    }

    private function then_we_expect_an_url_matching($expectedUrl)
    {
        $this->assertEquals($expectedUrl, $this->responseUrl, "failed for url [{$this->url}] with parameter [".print_r($this->parameter, true).']');
    }

    public function dataProviderAddParameterToUrl()
    {
        return array(
            array(
                'http://suer:password@www.foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar#anchor',
                array('param2' => array('newparam' => 'value')),
                'http://suer:password@www.foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar&'.urlencode('param2[newparam]').'=value#anchor',
            ),
            array(
                '//suer:password@www.foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar#anchor',
                array('param2' => array('newparam' => 'value')),
                '//suer:password@www.foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar&'.urlencode('param2[newparam]').'=value#anchor',
            ),
            array(
                'http://:password@www.foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar#anchor',
                array('param2' => array('newparam' => 'value')),
                'http://:password@www.foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar&'.urlencode('param2[newparam]').'=value#anchor',
            ),
            array(
                'http://suer@www.foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar#anchor',
                array('param2' => array('newparam' => 'value')),
                'http://suer@www.foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar&'.urlencode('param2[newparam]').'=value#anchor',
            ),
            array(
                'http://www.foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar#anchor',
                array('param2' => array('newparam' => 'value')),
                'http://www.foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar&'.urlencode('param2[newparam]').'=value#anchor',
            ),
            array(
                'http://foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar#anchor',
                array('param2' => array('newparam' => 'value')),
                'http://foo.bar/my/path?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar&'.urlencode('param2[newparam]').'=value#anchor',
            ),

            array(
                'http://foo.bar/?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar#anchor',
                array('param2' => array('newparam' => 'value')),
                'http://foo.bar/?parameter1=foo&'.urlencode('param2[one]').'=foo&'.urlencode('param2[two]').'=bar&'.urlencode('param2[newparam]').'=value#anchor',
            ),

            array(
                'http://suer:password@www.foo.bar/my/path#anchor',
                array('param2' => array('newparam' => 'value')),
                'http://suer:password@www.foo.bar/my/path?'.urlencode('param2[newparam]').'=value#anchor',
            ),

            array(
                'http://suer:password@www.foo.bar/my/path?#anchor',
                array('param2' => array('newparam' => 'value')),
                'http://suer:password@www.foo.bar/my/path?'.urlencode('param2[newparam]').'=value#anchor',
            ),

            array(
                'http://suer:password@www.foo.bar/my/path',
                array('param2' => array('newparam' => 'value')),
                'http://suer:password@www.foo.bar/my/path?'.urlencode('param2[newparam]').'=value',
            ),
            array(
                '/my/path',
                array('param2' => array('newparam' => 'value')),
                '/my/path?'.urlencode('param2[newparam]').'=value',
            ),
        );
    }
}
