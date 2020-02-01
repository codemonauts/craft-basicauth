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

        $compiler
            ->write(BasicAuth::class."::getInstance()->auth->check(".$type.", ")
            ->subcompile($this->getNode('entity'))
            ->write(', ')
            ->subcompile($this->getNode('siteHandle'))
            ->write(', ')
            ->subcompile($this->getNode('env'))
            ->write(', ')
            ->subcompile($this->getNode('realm'))
            ->write(");\n");
    }
}
