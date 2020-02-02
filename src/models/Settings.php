<?php

namespace codemonauts\basicauth\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * @var array The credentials for Basic Auth.
     */
    public $credentials = [];

    /**
     * @var array The new passwords set by user
     */
    public $newPasswords = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['credentials', 'newPasswords'], 'safe'],
        ];
    }
}
