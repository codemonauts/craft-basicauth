<?php

namespace codemonauts\basicauth;

use codemonauts\basicauth\models\Settings;
use codemonauts\basicauth\services\AuthService;
use codemonauts\basicauth\twig\Extension;
use Craft;
use craft\base\Plugin;
use craft\events\ModelEvent;
use craft\helpers\UrlHelper;
use yii\base\Event;

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

        Event::on(Plugin::class, Plugin::EVENT_BEFORE_SAVE_SETTINGS, function(ModelEvent $event) {
            $settings = $this->getSettings();

            if (is_array($settings->credentials) && !empty($settings->credentials)) {

                // Set new entered passwords
                foreach ($settings->newPasswords as $key => $newPassword) {
                    if (trim($newPassword) != '') {
                        $settings->credentials[$key][1] = Craft::$app->security->hashPassword($newPassword);
                    }
                }

                // Set passwords for new rows
                foreach ($settings->credentials as $key => $cred) {
                    if (preg_match('/^\$2.\$/i', $cred[1]) !== 1) {
                        $settings->credentials[$key][1] = Craft::$app->security->hashPassword($cred[1]);
                    }
                }

                $settings->newPasswords = [];

                $this->getSettings()->setAttributes($settings->toArray());

                // Check values
                if ($this->getSettings()->credentials) {
                    foreach ($this->getSettings()->credentials as $cred) {
                        if ($cred[0] == '' || $cred[1] == '') {
                            $event->isValid = false;
                            return;
                        }
                    }
                }
            }
        });
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
        $settings = $this->getSettings();

        $creds = empty($settings->credentials) ? [] : $settings->credentials;

        foreach ($creds as $key => $cred) {
            $placeholder = $cred[1] != '' ? '****' : '';
            $creds[$key][1] = '<input type="hidden" name="credentials['.$key.'][1]" value="'.$cred[1].'"><textarea name="newPasswords['.$key.']" placeholder="'.$placeholder.'" rows="1"></textarea>';
        }

        return Craft::$app->getView()->renderTemplate('basicauth/settings', [
                'settings' => $this->getSettings(),
                'creds' => $creds,
                'credentialsCols' => [
                    [
                        'heading' => 'Username*',
                        'type' => 'singleline',
                    ],
                    [
                        'heading' => 'Password*',
                        'type' => 'html',
                        'class' => 'textual',
                    ],
                    [
                        'heading' => 'Groups',
                        'info' => 'Optional comma-seperated list of group names.',
                        'type' => 'singleline',
                    ]
                ],
                'allowlistCols' => [
                    [
                        'heading' => 'IP address or subnet*',
                        'info' => 'IPv4 or IPv6 address or subnet in CIDR notation',
                        'type' => 'singleline',
                    ],
                ],
            ]
        );
    }
}
