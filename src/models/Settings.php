<?php

namespace codemonauts\basicauth\models;

use craft\base\Model;
use yii\validators\IpValidator;

class Settings extends Model
{
    /**
     * @var array The credentials for Basic Auth.
     */
    public array $credentials = [];

    /**
     * @var array The allowlist of IP addresses or ranges that overwrites credentials.
     */
    public array $allowlist = [];

    /**
     * @var array The new passwords set by user
     */
    public array $newPasswords = [];

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['credentials', 'newPasswords'], 'safe'],
            ['allowlist', 'validateIps'],

        ];
    }

    /**
     * Validate IPs from the allowlist.
     *
     * @param string $attribute
     */
    public function validateIps(string $attribute)
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
