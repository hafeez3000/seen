# SEEN

[![Build Status](https://travis-ci.org/thelfensdrfer/seen.svg)](https://travis-ci.org/thelfensdrfer/seen) [![Dependencies Status](http://depending.in/thelfensdrfer/seen.png)](http://depending.in/thelfensdrfer/seen)

## Requirements

* PHP >= 5.4 (with intl extension)
* MySQL / MariaDB
* Node.js

## Installation

* Clone repository
* Create development folder `config/development` with contents of `config/testing/` and adjust settings
* `composer global require "fxp/composer-asset-plugin:1.0.*"`
* `composer install`
* `grunt`
* Copy social glyphicons web fonts (not included) into the `web/fonts/` directory
* Copy `web/.env.sample.php` to `web/.env.php` and insert rollbar access token
* Create database tables with `./yii migrate`

## Tests

* Create `tests/codeception/_localurl.php` which returns the development url as a string e.g. `<?php return '/seen/index-test.php';`
