<?php

namespace codemonauts\basicauth\twig\tokenparsers;

use codemonauts\basicauth\twig\nodes\BasicAuthNode;
use craft\web\twig\nodes\CacheNode;
use Twig\Parser;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class BasicAuthTokenParser extends AbstractTokenParser
{
    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'basicauth';
    }

    /**
     * @inheritdoc
     */
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        /** @var Parser $parser */
        $parser = $this->parser;
        $stream = $parser->getStream();

        $nodes = [];

        $attributes = [
            'type' => null,
        ];

        $stream->expect(Token::NAME_TYPE, 'require');

        $attributes['type'] = $stream->expect(Token::NAME_TYPE, ['user', 'group', 'valid', 'any'])->getValue();

        if (in_array($attributes['type'], ['user', 'group'])) {
            $nodes['entity'] = $parser->getExpressionParser()->parseExpression();
        }

        if ($stream->test(Token::NAME_TYPE, 'site')) {
            $stream->next();
            $nodes['siteHandle'] = $parser->getExpressionParser()->parseExpression();
        }

        if ($stream->test(Token::NAME_TYPE, 'env')) {
            $stream->next();
            $nodes['env'] = $parser->getExpressionParser()->parseExpression();
        }

        if ($stream->test(Token::NAME_TYPE, 'realm')) {
            $stream->next();
            $nodes['realm'] = $parser->getExpressionParser()->parseExpression();
        }

        if ($stream->test(Token::NAME_TYPE, 'if')) {
            $stream->next();
            $nodes['conditions'] = $parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new BasicAuthNode($nodes, $attributes, $lineno, $this->getTag());
    }
}
