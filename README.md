# Composer Auto Normalize

[![Latest Stable Version](https://poser.pugx.org/cupoftea/composer-auto-normalize/v/stable)](https://packagist.org/packages/cupoftea/composer-auto-normalize)
[![Total Downloads](https://poser.pugx.org/cupoftea/composer-auto-normalize/downloads)](https://packagist.org/packages/cupoftea/composer-auto-normalize)
[![Latest Unstable Version](https://poser.pugx.org/cupoftea/composer-auto-normalize/v/unstable)](https://packagist.org/packages/cupoftea/composer-auto-normalize)
[![License](https://poser.pugx.org/cupoftea/composer-auto-normalize/license)](./LICENSE)

Provides a composer plugin that extends [localheinz/composer-normalize][composer-normalize], and automatically
normalizes your composer.json on install or update.

## Installation

Composer Auto Normalize can be installed either globally, or as a per project installation. When using it in a project,
we recommend installing it as a dev-dependency.

```bash
$ composer global require cupoftea/composer-auto-normalize
$ composer require --dev cupoftea/composer-auto-normalize
```

## Usage

Once installed, your composer.json file will be normalized every time a composer install or update is executed.
Because this plugin extends [localheinz/composer-normalize][composer-normalize], you can also manually normalize your
composer.json by running `composer normalize`.

## Configuration

You can set any [arguments and options available][composer-normalize-docs] on the `composer normalize` command as a
default for auto normalization. You can configure this both in your global composer.json, or per project in your
project's composer.json file. Arguments should be set in `extra.auto-normalize` by their name, and options in
`extra.auto-normalize.options`. For a full list of available arguments and options, see the
[Composer Normalize Documentation][composer-normalize-docs].

```json
{
    "extra": {
        "auto-normalize": {
            "options": {
                "indent-size": 2,
                "indent-type": "space"
            }
        }
    }
}
```


[composer-normalize]: https://github.com/localheinz/composer-normalize
[composer-normalize-docs]: https://github.com/localheinz/composer-normalize#arguments
