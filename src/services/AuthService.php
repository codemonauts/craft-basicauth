<?php

namespace codemonauts\basicauth\services;

use codemonauts\basicauth\BasicAuth;
use Craft;
use craft\base\Component;
use craft\helpers\StringHelper;

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

            $isAuthenticated = false;

            $hasCredentials = (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']));

            if ($hasCredentials) {
                switch ($type) {
                    case 'any':
                        $isAuthenticated = true;
                        break;

                    case 'valid':
                        $isAuthenticated = $this->validateCredentials($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
                        break;

                    case 'user':
                        if ($entity != $_SERVER['PHP_AUTH_USER']) {
                            $isAuthenticated = false;
                        } else {
                            $isAuthenticated = $this->validateCredentials($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
                        }
                        break;

                    case 'group':
                        $isAuthenticated = $this->validateCredentials($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $entity);
                        break;

                    default:
                        $isAuthenticated = false;
                }
            }

            if (!$isAuthenticated) {
                Craft::$app->getResponse()->setStatusCode(401, 'Authorization Required');
                Craft::$app->getResponse()->getHeaders()->set('WWW-Authenticate', 'Basic realm="'.$realm.'"');
                Craft::$app->end();
            }
        }
    }

    public function getUser()
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return null;
        }

        return $_SERVER['PHP_AUTH_USER'];
    }

    public function validateCredentials($user, $password, $groupMember = null) {

        $creds = BasicAuth::getInstance()->getSettings()->credentials;

        $creds = empty($creds) ? [] : $creds;

        foreach ($creds as $cred) {
            if ($cred[0] == $user && Craft::$app->security->validatePassword($password, $cred[1])) {

                $groupCheck = ($groupMember !== null);
                if ($groupCheck) {
                    return (in_array($groupMember, StringHelper::split($cred[2])));
                }

                return true;
            }
        }

        return false;
    }
}
