FacebookBundle
==============
[![Latest Stable Version](https://poser.pugx.org/core23/facebook-bundle/v/stable)](https://packagist.org/packages/core23/facebook-bundle)
[![Latest Unstable Version](https://poser.pugx.org/core23/facebook-bundle/v/unstable)](https://packagist.org/packages/core23/facebook-bundle)
[![License](https://poser.pugx.org/core23/facebook-bundle/license)](LICENSE.md)

[![Total Downloads](https://poser.pugx.org/core23/facebook-bundle/downloads)](https://packagist.org/packages/core23/facebook-bundle)
[![Monthly Downloads](https://poser.pugx.org/core23/facebook-bundle/d/monthly)](https://packagist.org/packages/core23/facebook-bundle)
[![Daily Downloads](https://poser.pugx.org/core23/facebook-bundle/d/daily)](https://packagist.org/packages/core23/facebook-bundle)

[![Continuous Integration](https://github.com/core23/FacebookBundle/workflows/Continuous%20Integration/badge.svg)](https://github.com/core23/FacebookBundle/actions)
[![Code Coverage](https://codecov.io/gh/core23/FacebookBundle/branch/master/graph/badge.svg)](https://codecov.io/gh/core23/FacebookBundle)

This bundle adds some basic auth mechanisum for using the [Facebook API] inside symfony.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require core23/facebook-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Core23\FacebookBundle\Core23FacebookBundle::class => ['all' => true],
];
```

### Configure the Bundle

Create a configuration file called `core23_facebook.yaml`:

```yaml
# config/packages/core23_facebook.yaml

core23_facebook:
    api:
        field:       'app_id'
        class:       'app_secret'
        permissions: ['public_profile', 'user_likes']
```

## License

This bundle is under the [MIT license](LICENSE.md).

[Facebook API]: https://developers.facebook.com/
