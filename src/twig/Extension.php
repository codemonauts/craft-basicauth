<?php

namespace codemonauts\basicauth\twig;

use codemonauts\basicauth\BasicAuth;
use codemonauts\basicauth\twig\tokenparsers\BasicAuthTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class Extension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenParsers(): array
    {
        return [
            new BasicAuthTokenParser(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getGlobals(): array
    {
        return [
            'basicAuthUsername' => BasicAuth::getInstance()->auth->getUsername(),
            'basicAuthPassword' => BasicAuth::getInstance()->auth->getPassword(),
        ];
    }
}
