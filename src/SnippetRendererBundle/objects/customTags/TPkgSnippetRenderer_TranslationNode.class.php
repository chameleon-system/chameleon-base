<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * the class is based on / copied from the symfony TransNode class.
 *
 * @deprecated since 6.3.0 - no longer used.
/**/
class TPkgSnippetRenderer_TranslationNode extends Twig_Node
{
    public function __construct(\Twig_Node $body, \Twig_Node $domain, \Twig_Node_Expression $count = null, \Twig_Node_Expression $vars = null, \Twig_Node_Expression $locale = null, $lineno = 0, $tag = null)
    {
        parent::__construct(array('count' => $count, 'body' => $body, 'domain' => $domain, 'vars' => $vars, 'locale' => $locale), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $vars = $this->getNode('vars');
        $defaults = new \Twig_Node_Expression_Array(array(), -1);
        if ($vars instanceof \Twig_Node_Expression_Array) {
            $defaults = $this->getNode('vars');
            $vars = null;
        }
        list($msg, $defaults) = $this->compileString($this->getNode('body'), $defaults);

        $compiler
            ->write('echo TGlobal::Translate(')
            ->subcompile($msg)
        ;

        $compiler->raw(', ');

        if (null !== $vars) {
            $compiler
                ->raw(' array_merge(')
                ->subcompile($defaults)
                ->raw(', ')
                ->subcompile($this->getNode('vars'))
                ->raw(')')
            ;
        } else {
            $compiler->subcompile($defaults);
        }

        $compiler->raw(");\n");
    }

    protected function compileString(\Twig_Node $body, \Twig_Node_Expression_Array $vars)
    {
        if ($body instanceof \Twig_Node_Expression_Constant) {
            $msg = $body->getAttribute('value');
        } elseif ($body instanceof \Twig_Node_Text) {
            $msg = $body->getAttribute('data');
        } else {
            return array($body, $vars);
        }

        preg_match_all('/(?<!%)%([^%]+)%/', $msg, $matches);

        if (version_compare(\Twig_Environment::VERSION, '1.5', '>=')) {
            foreach ($matches[1] as $var) {
                $key = new \Twig_Node_Expression_Constant('%'.$var.'%', $body->getLine());
                if (!$vars->hasElement($key)) {
                    $vars->addElement(new \Twig_Node_Expression_Name($var, $body->getLine()), $key);
                }
            }
        } else {
            $current = array();
            foreach ($vars as $name => $var) {
                $current[$name] = true;
            }
            foreach ($matches[1] as $var) {
                if (!isset($current['%'.$var.'%'])) {
                    $vars->setNode('%'.$var.'%', new \Twig_Node_Expression_Name($var, $body->getLine()));
                }
            }
        }

        return array(new \Twig_Node_Expression_Constant(str_replace('%%', '%', trim($msg)), $body->getLine()), $vars);
    }
}
