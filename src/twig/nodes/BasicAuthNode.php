<?php

namespace codemonauts\basicauth\twig\nodes;

use codemonauts\basicauth\BasicAuth;
use Twig\Compiler;
use Twig\Node\Node;

class BasicAuthNode extends Node
{
    /**
     * @inheritdoc
     */
    public function compile(Compiler $compiler)
    {
        $type = '"'.$this->getAttribute('type').'"';
        $conditions = $this->hasNode('conditions') ? $this->getNode('conditions') : null;
        $entity = $this->hasNode('entity') ? $this->getNode('entity') : null;
        $siteHandle = $this->hasNode('siteHandle') ? $this->getNode('siteHandle') : null;
        $env = $this->hasNode('env') ? $this->getNode('env') : null;
        $realm = $this->hasNode('realm') ? $this->getNode('realm') : null;

        if ($conditions) {
            $compiler
                ->write('if (')
                ->subcompile($conditions)
                ->raw(") {\n")
                ->indent();
        }

        $compiler
            ->write(BasicAuth::class.'::getInstance()->auth->check('.$type);

        if ($entity) {
            $compiler
                ->raw(', ')
                ->subcompile($entity);
        } else {
            $compiler->raw(', null');
        }

        if ($siteHandle) {
            $compiler
                ->raw(', ')
                ->subcompile($siteHandle);
        } else {
            $compiler->raw(', null');
        }

        if ($env) {
            $compiler
                ->raw(', ')
                ->subcompile($env);
        } else {
            $compiler->raw(', null');
        }

        if ($realm) {
            $compiler
                ->raw(', ')
                ->subcompile($realm);
        } else {
            $compiler->raw(', null');
        }

        $compiler->raw(");\n");

        if ($conditions) {
            $compiler
                ->outdent()
                ->write("}\n");
        }
    }
}
