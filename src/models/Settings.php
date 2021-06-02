<?php

namespace codemonauts\basicauth\models;

use craft\base\Model;
use yii\validators\IpValidator;

class Settings extends Model
{
    /**
     * @var array The credentials for Basic Auth.
     */
    public $credentials = [];

    /**
     * @var array The allowlist of IP addresses or ranges that overwrites credentials.
     */
    public $allowlist = [];

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
            ['allowlist', 'validateIps']

        ];
    }

    public function validateIps($attribute)
    {
        $values = $this->$attribute;

        if (is_array($values)) {
            $ipValidator = new IpValidator(['subnet' => null]);
            foreach ($values as $ip) {
                $error = [];
                if (!$ipValidator->validate($ip[0], $error)) {
                    $this->addError($attribute, $ip[0] . ': ' . $error);
                }
            }
        }
    }
}
