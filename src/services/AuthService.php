<?php

namespace codemonauts\basicauth\services;

use Craft;
use craft\base\Component;

class AuthService extends Component
{
    public function check($type, $entity = null, $siteHandle = null, $env = null, $realm = null)
    {
        $matchedEnv = true;
        $matchedSite = true;

        if ($env !== null) {
            if ($env != Craft::$app->config->env) {
                $matchedEnv = false;
            }
        }

        if ($siteHandle !== null) {
            if ($siteHandle != Craft::$app->sites->getCurrentSite()->handle) {
                $matchedSite = false;
            }
        }

        if ($matchedSite && $matchedEnv) {



        }
    }

    public function getUser()
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return null;
        }

        return $_SERVER['PHP_AUTH_USER'];
    }
}
