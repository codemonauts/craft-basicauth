<?php

/**
 * Lab-Seeder für basicauth.
 */

use yii\console\ExitCode;

return function (\craft\console\Controller $c): int {
    $plugins = Craft::$app->getPlugins();

    $plugin = $plugins->getPlugin('basicauth');
    if ($plugin === null) {
        $c->stderr("Plugin 'basicauth' ist nicht installiert.\n");
        return ExitCode::UNSPECIFIED_ERROR;
    }

    // [username, password (Klartext), group]
    $credentials = [
        ['admin', 'admin', 'admins'],
        ['guest', 'guest', ''],
    ];

    if (!$plugins->savePluginSettings($plugin, ['credentials' => $credentials])) {
        $errors = print_r($plugin->getSettings()->getErrors(), true);
        $c->stderr("basicauth-Settings konnten nicht gespeichert werden:\n{$errors}\n");
        return ExitCode::UNSPECIFIED_ERROR;
    }

    $c->stdout("basicauth: " . count($credentials) . " Credentials gesetzt — admin/admin (Gruppe admins), guest/guest.\n");
    return ExitCode::OK;
};
