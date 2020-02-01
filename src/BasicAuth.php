<?php

namespace codemonauts\basicauth;

use codemonauts\basicauth\models\Settings;
use codemonauts\basicauth\services\AuthService;
use codemonauts\basicauth\twig\Extension;
use codemonauts\basicauth\variables\BasicAuthVariable;
use Craft;
use craft\base\Plugin;
use craft\helpers\UrlHelper;

/**
 * Class BasicAuth
 *
 * @property AuthService auth
 * @package codemonauts\basicauth
 */
class BasicAuth extends Plugin
{
    public $hasCpSettings = true;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->setComponents([
            'auth' => AuthService::class,
        ]);

        if (Craft::$app->request->getIsSiteRequest()) {
            $extension = new Extension();
            Craft::$app->view->registerTwigExtension($extension);
        }
    }

    /**
     * @inheritDoc
     */
    public function afterInstall()
    {
        parent::afterInstall();

        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            return;
        }

        Craft::$app->getResponse()->redirect(
            UrlHelper::cpUrl('settings/plugins/basicauth')
        )->send();
    }

    /**
     * @inheritDoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritDoc
     */
    protected function settingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('basicauth/settings', [
                'settings' => $this->getSettings(),
            ]
        );
    }
}
