<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\UpdateManager;

use ChameleonSystem\CoreBundle\UpdateManager\StripVirtualFieldsFromQuery;
use ChameleonSystem\CoreBundle\UpdateManager\VirtualFieldManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class StripNonExistingFieldsFromQueryTest extends TestCase
{
    use ProphecyTrait;

    private $virtualFields;
    private $query;
    /**
     * @var StripVirtualFieldsFromQuery
     */
    private $service;
    private $modifiedQuery;

    /**
     * @test
     * @dataProvider dataProviderVirtualFields
     *
     * @param $query
     * @param $expectedResult
     * @param $virtualFields
     */
    public function it_should_strip_virtual_fields($query, $expectedResult, $virtualFields)
    {
        $this->given_the_virtual_fields($virtualFields);
        $this->given_a_query($query);
        $this->given_an_instance_of_the_service();
        $this->when_we_call_stripNonExistingFields();
        $this->then_we_expect_to_get($expectedResult);
    }

    private function given_the_virtual_fields($virtualFields)
    {
        $this->virtualFields = $virtualFields;
    }

    private function given_a_query($query)
    {
        $this->query = $query;
    }

    private function given_an_instance_of_the_service()
    {
        /** @var $virtualFields VirtualFieldManagerInterface|ObjectProphecy */
        $virtualFields = $this->prophesize('ChameleonSystem\CoreBundle\UpdateManager\VirtualFieldManagerInterface');
        foreach ($this->virtualFields as $table => $virtualField) {
            $virtualFields->getVirtualFieldsForTable($table)->willReturn($virtualField);
        }

        $this->service = new StripVirtualFieldsFromQuery($virtualFields->reveal());
    }

    private function when_we_call_stripNonExistingFields()
    {
        $this->modifiedQuery = $this->service->stripNonExistingFields($this->query);
    }

    private function then_we_expect_to_get($expectedResult)
    {
        $this->assertEquals($expectedResult, $this->modifiedQuery, 'failed with inputfields: '.print_r($this->virtualFields, true));
    }

    public function dataProviderVirtualFields()
    {
        return array(
            array(
                "INSERT INTO `cms_tpl_module` SET `name` = '', `description` = '', `cms_tbl_conf_mlt` = '',",
                "INSERT INTO `cms_tpl_module` SET `name` = '', `cms_tbl_conf_mlt` = '',",
                array('cms_tpl_module' => array('description')),
            ),
            array(
                "INSERT INTO `cms_tpl_module` SET `name` = '', `description` = '', `cms_tbl_conf_mlt` = '',",
                "INSERT INTO `cms_tpl_module` SET `description` = '', `cms_tbl_conf_mlt` = '',",
                array('cms_tpl_module' => array('name')),
            ),
            array(
                "UPDATE `cms_tpl_module` SET `name` = '', `description` = '', `cms_tbl_conf_mlt` = '',",
                "UPDATE `cms_tpl_module` SET `name` = '', `cms_tbl_conf_mlt` = '',",
                array('cms_tpl_module' => array('description')),
            ),
            array(
                "UPDATE `cms_tpl_module` SET `name` = '', `description` = '', `cms_tbl_conf_mlt` = '',",
                "UPDATE `cms_tpl_module` SET `description` = '', `cms_tbl_conf_mlt` = '',",
                array('cms_tpl_module' => array('name')),
            ),

            array(
                "INSERT INTO `cms_tpl_module` SET `name` = '', `description` = 'asdfasdfsdf', `cms_tbl_conf_mlt` = ''",
                "INSERT INTO `cms_tpl_module` SET `name` = '', `description` = 'asdfasdfsdf'",
                array('cms_tpl_module' => array('cms_tbl_conf_mlt')),
            ),
            array(
                "INSERT INTO `cms_tpl_module` SET `name` = '', `description` = 'asdfasdfsdf', `cms_tbl_conf_mlt` = '' WHERE",
                "INSERT INTO `cms_tpl_module` SET `name` = '', `description` = 'asdfasdfsdf' WHERE",
                array('cms_tpl_module' => array('cms_tbl_conf_mlt')),
            ),

            array(
                "UPDATE `cms_tpl_module` SET `name` = '', `description` = 'asdfasdfsdf', `cms_tbl_conf_mlt` = ''",
                "UPDATE `cms_tpl_module` SET `name` = '', `description` = 'asdfasdfsdf'",
                array('cms_tpl_module' => array('cms_tbl_conf_mlt')),
            ),
            array(
                "UPDATE `cms_tpl_module` SET `name` = '', `description` = 'asdfasdfsdf', `cms_tbl_conf_mlt` = '' WHERE",
                "UPDATE `cms_tpl_module` SET `name` = '', `description` = 'asdfasdfsdf' WHERE",
                array('cms_tpl_module' => array('cms_tbl_conf_mlt')),
            ),
            array(
                "INSERT INTO `cms_tpl_module` SET `name` = '', `description` = '', `cms_tbl_conf_mlt` = '', `icon_list` = 'application.png', `classname` = '', `view_mapper_config` = '', `view_mapping` = '', `revision_management_active` = '0', `is_copy_allowed` = '0', `show_in_template_engine` = '1', `position` = '', `is_restricted` = '0', `cms_usergroup_mlt` = '', `id`='a79843ef-6814-1ac8-a20f-0dceccd3281d'",
                "INSERT INTO `cms_tpl_module` SET `name` = '', `description` = '', `icon_list` = 'application.png', `classname` = '', `view_mapper_config` = '', `view_mapping` = '', `revision_management_active` = '0', `is_copy_allowed` = '0', `show_in_template_engine` = '1', `position` = '', `is_restricted` = '0', `cms_usergroup_mlt` = '', `id`='a79843ef-6814-1ac8-a20f-0dceccd3281d'",
                array('cms_tpl_module' => array('cms_tbl_conf_mlt')),
            ),
        );
    }
}
