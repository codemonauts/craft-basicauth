# HTTP Basic Authentication for Craft CMS 3.x

![Icon](resources/basicauth.png)

A plugin for Craft CMS that provides HTTP Basic Authentication (BasicAuth) within templates.

## Requirements

 * Craft CMS >= 3.2.0

## Installation

Open your terminal and go to your Craft project:

``` shell
cd /path/to/project
composer require codemonauts/craft-basicauth
./craft install/plugin basicauth
```

## Credentials

On the settings page in the control panel you can add credentials to use for authentication.

![Screenshot](resources/credentials.png)

These settings supports the project config if enabled. 

## {% basicauth %} Tag

In your templates you can require BasicAuth:

```twig
{% basicauth require valid env "stage" %}
 ```

### Parameters

The `{% basicauth %}` tag supports the following parameters:`

#### `require`

This specifies the type of check to be made. You can check against a single `user`, a `group` of user, any `valid` user and also accept `any` provided credentials. 

```twig
{# require a single user #}
{% basicauth require user "bob" %}

{# require the user to be a member of a group #}
{% basicauth require group "admins" %}

{# accept any valid credentials #}
{% basicauth require valid %}

{# accept any credentials without checking them #}
{% basicauth require any %}
```

This parameter is **required**.

#### `site`

This specifies the site handle for which the authentication should be made. The Authentication is only enforced when the current site handle is the same as specified.

```twig
{# require the user "bob" if the current site handle is "acme" #}
{% basicauth require user "bob" site "acme" %}
```

#### `env`

This specifies the environment for which the authentication should be made. The Authentication is only enforced when the current environment is the same as specified.

```twig
{# require the user to be a member of the group "customer" if the current environment is "stage" #}
{% basicauth require group "customer" env "stage" %}
```

#### `if`

Only activates the BasicAuth if a certain condition is met.

```twig
{% basicauth require valid if craft.app.request.isMobileBrowser() %}
```

### Globals

This plugin provides als two global variables with the credentials of the user:

```twig
<p>Hello {{ basicAuthUsername }}!</p>
<p>Your password is: {{ basicAuthPassword }}</p>
```

With ❤ by [codemonauts](https://codemonauts.com)
