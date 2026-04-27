<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCacheBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ChameleonSystemCmsCacheExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');

        $cacheableTables = $this->getDefaultCacheableTables();
        $cacheableTables = array_merge($cacheableTables, $config['dbal_query_cache']['additional_cacheable_tables']);
        $cacheableTables = array_values(array_unique($cacheableTables));

        if ([] !== $config['dbal_query_cache']['excluded_cacheable_tables']) {
            $cacheableTables = array_values(array_diff($cacheableTables, $config['dbal_query_cache']['excluded_cacheable_tables']));
        }

        $container->setParameter('chameleon_system_cms_cache.dbal_query_cache.cacheable_tables', $cacheableTables);

        $active = $container->getParameter('chameleon_system_core.cache.memcache_activate');
        if ($active) {
            $cacheDefinition = $container->getDefinition('chameleon_system_cms_cache.cache');
            $cacheDefinition->replaceArgument(2, $container->getDefinition('chameleon_system_cms_cache.storage.memcache'));
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'wrapper_class' => 'ChameleonSystem\CmsCacheBundle\Doctrine\CachingConnectionWrapper',
            ],
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function getDefaultCacheableTables(): array
    {
        return array_merge(
            $this->getChameleonBaseCacheableTables(),
            $this->getChameleonShopCacheableTables()
        );
    }

    /**
     * @return array<int, string>
     */
    private function getChameleonBaseCacheableTables(): array
    {
        return [
            'cms_language',
            'cms_portal',
            'cms_locals',
            'data_extranet',
            'esono_ab_test',
            'esono_ab_test_variant',
            'esono_ab_test_variant_page_mapping',
            'esono_ab_test_variant_static_module_mapping',
            'pkg_cms_theme',
            'cms_tbl_conf',
            'cms_field_conf',
            'cms_field_type',
            'cms_tpl_page',
            'cms_master_pagedef',
            'cms_master_pagedef_spot',
            'cms_master_pagedef_spot_parameter',
            'cms_master_pagedef_spot_access',
            'cms_tpl_page_cms_master_pagedef_spot',
            'cms_portal_system_page',
            'cms_tree',
            'pkg_cms_theme_block',
            'pkg_cms_theme_block_layout',
            'pkg_cms_theme_pkg_cms_theme_block_layout_mlt',
            'cms_config_imagemagick',
            'pkg_cms_text_block',
            'cms_portal_domains',
            'pkg_external_tracker',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function getChameleonShopCacheableTables(): array
    {
        return [
            'shop',
            'shop_cms_portal_mlt',
            'pkg_shop_currency',
            'shop_attribute',
            'shop_category',
            'shop_article_image_size',
            'shop_stock_message_trigger',
            'shop_module_article_list',
            'shop_module_article_list_filter',
            'shop_module_articlelist_orderby',
            'pkg_shop_listfilter_item',
            'shop_module_article_list_shop_article_group_mlt',
        ];
    }
}
