<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\Twig\Extension;

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig_Environment;
use Twig_SimpleFilter;

class ChameleonStandardExtension extends AbstractExtension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'chameleon_system_standard_extension';
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'cmsGetPathTheme', static fn () => \TGlobal::GetPathTheme(), array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'cmsthumb' => new TwigFilter('cmsthumb', 'TPkgSnippetRendererFilter::getThumbnail'),
            'staticurl' => new TwigFilter('staticurl', 'TGlobal::getStaticUrl'),
            'cms_static_url_web_lib' => new TwigFilter('cms_static_url_web_lib', 'TGlobal::GetStaticURLToWebLib'),

            // escaping
            'escape' => new TwigFilter(
                'escape',
                '\ChameleonSystem\ViewRendererBundle\Twig\Extension\ChameleonStandardExtension::chameleonTwigEscapeFilter',
                array('needs_environment' => true, 'is_safe' => ['html', 'js', 'css'])
            ),
            'e' => new TwigFilter(
                'e',
                '\ChameleonSystem\ViewRendererBundle\Twig\Extension\ChameleonStandardExtension::chameleonTwigEscapeFilter',
                array('needs_environment' => true, 'is_safe' => ['html', 'js', 'css'])
            ),
            'sanitizeurl' => new TwigFilter(
                'sanitizeurl',
                '\ChameleonSystem\ViewRendererBundle\Twig\Extension\ChameleonStandardExtension::sanitizeUrl',
                array('needs_environment' => true, 'is_safe' => ['html', 'js', 'css'])
            ),
        );
    }

    /**
     * chameleonTwigEscapeFilter wraps the original twig escape extension to make sure the authenticity token string
     * won't get escaped by Twig and thus be rendered useless.
     *
     * @param Environment      $env
     * @param mixed            $string
     * @param string|null      $strategy
     * @param string|null      $charset
     * @param bool|null        $autoescape
     *
     * @return string
     *
     * @throws RuntimeError
     */
    public static function chameleonTwigEscapeFilter(Environment $env, $string, $strategy = 'html', $charset = null, $autoescape = false)
    {
        if (!is_string($string)) {
            return twig_escape_filter($env, $string, $strategy, $charset, $autoescape);
        }

        static $authenticityTokenName = null;
        if (null === $authenticityTokenName) {
            $authenticityTokenName = sprintf('[{%s}]', AuthenticityTokenManagerInterface::TOKEN_ID);
        }

        if (false === strpos($string, $authenticityTokenName)) {
            return twig_escape_filter($env, $string, $strategy, $charset, $autoescape);
        }

        $placeholder = '___CHAMELEON_AUTHENTICITY_TOKEN___';
        $string = str_replace($authenticityTokenName, $placeholder, $string);
        $escaped = twig_escape_filter($env, $string, $strategy, $charset, $autoescape);

        $escaped = str_replace($placeholder, $authenticityTokenName, $escaped);

        return $escaped;
    }

    /**
     * @param Environment      $env
     * @param mixed            $string
     * @param string           $strategy
     * @param string|null      $charset
     * @param bool             $autoescape
     *
     * @return string
     *
     * @throws RuntimeError
     */
    public static function sanitizeUrl(Environment $env, $string, $strategy = 'html', $charset = null, $autoescape = false)
    {
        if (false === self::isAllowedUrl($string)) {
            return '#';
        }

        return self::chameleonTwigEscapeFilter($env, $string, $strategy, $charset, $autoescape);
    }

    /**
     * Forbids javascript: and data: URLs as well as URLs that are malformed enough that parse_url does not recognize
     * them.
     *
     * @param string $string
     *
     * @return bool
     */
    private static function isAllowedUrl($string)
    {
        if (false === is_string($string)) {
            return false;
        }
        /**
         * First remove invalid characters so that the scheme cannot be hidden, e.g. with a space character.
         */
        $string = \filter_var($string, FILTER_SANITIZE_URL);
        $scheme = \parse_url($string, PHP_URL_SCHEME);

        return null === $scheme
            || \in_array(\mb_strtolower($scheme), ['http', 'https'], true);
    }
}
