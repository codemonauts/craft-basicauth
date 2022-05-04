<?php

namespace codemonauts\basicauth\services;

use codemonauts\basicauth\BasicAuth;
use Craft;
use craft\base\Component;
use craft\helpers\StringHelper;
use Symfony\Component\HttpFoundation\IpUtils;

class AuthService extends Component
{
    /**
     * Check if request has to authenticate.
     *
     * @param string $type The type of authentication: any, valid, user or group
     * @param string|null $entity The name of the entity.
     * @param string|null $siteHandle The site handle to check.
     * @param string|null $env The environment to check.
     * @param string|null $realm The realm string to use.
     *
     * @throws \craft\errors\SiteNotFoundException
     * @throws \yii\base\ExitException
     */
    public function check(string $type, ?string $entity = null, ?string $siteHandle = null, ?string $env = null, ?string $realm = null): void
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

            // Check if IP address matches allowlist
            $allowlist = BasicAuth::$settings->allowlist;
            if (!empty($allowlist)) {
                $allowlist = array_merge([], ...$allowlist);
                $ip = Craft::$app->request->getRemoteIP();
                if (IpUtils::checkIp($ip, $allowlist)) {
                    return;
                }
            }

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
                Craft::$app->getResponse()->getHeaders()->set('WWW-Authenticate', 'Basic realm="' . $realm . '"');
                Craft::$app->end();
            }
        }
    }

    /**
     * Returns the current authenticated username.
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return null;
        }

        return (string)$_SERVER['PHP_AUTH_USER'];
    }

    /**
     * Returns the current password used.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        if (!isset($_SERVER['PHP_AUTH_PW'])) {
            return null;
        }

        return (string)$_SERVER['PHP_AUTH_PW'];
    }

    /**
     * Validates the username and password and checks group membership.
     *
     * @param string $user The username to check.
     * @param string $password The password to check.
     * @param string|null $groupMember The group where the username should be a member of.
     *
     * @return bool
     */
    public function validateCredentials(string $user, string $password, ?string $groupMember = null): bool
    {
        foreach (BasicAuth::$settings->credentials as $cred) {
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
