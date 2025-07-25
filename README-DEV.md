# TiltUp Crypto Payments Module for PrestaShop - Dev Guide

This is a developer's guide for our official TiltUp Crypto Payments Module for PrestaShop. For a user guide, refer to
our [Help center article](https://tiltup.zendesk.com/hc/en-001/articles/13196260302994-How-to-integrate-Payment-Gateway-with-Prestashop-).

## Local env setup

### Windows

1. Clone this repo
2. Download & install [Xampp](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/7.4.33/) as local PHP
   interpreter.
3. Download & install [Composer](https://getcomposer.org/download/) (use the installer)

## Bootstrapping the application

1. `composer install`
2. `composer up` will spin up a Dockerized MariaDB & Prestashop instance.
3. The repo folder is mounted onto the Prestashop container (aka hot reload), so all your changes will be immediately
   reflected in the shop.
4. Navigate to `localhost:8080` to access your shop.
5. Navigate to `localhost:8080/admin-dev` to access admin panel.

info about test credentials
is [in confluence](https://ari10help.zendesk.com/hc/en-001/articles/22733841848338-How-to-integrate-Payment-Gateway-with-Prestashop)

## Debugging

### IntelliJ IDEA

1. Install the official PHP Plugin.
2. The local container already comes with Xdebug enabled, so no extra config needed.
3. Setup server and path mappings as follows:![intellij-debug-setup.png](assets/readme/intellij-debug-setup.png)
4. Click `Start listening to PHP Debug Connections` to start debugging.

## Prestashop

### Module installation

The module is installed by a post-install script. However, it can also be installed/uninstalled from command line:

```shell
php bin/console prestashop:module install tiltupcryptopaymentsmodule
```

```shell
php bin/console prestashop:module uninstall tiltupcryptopaymentsmodule
```

### Currency Configuration

**Important**: Polish Zloty (PLN) currency is automatically enabled during the first boot process. This is handled by the post-install script which:

- Checks if PLN currency is already active
- Activates PLN currency if not present
- Configures it for immediate use
- Enables the tiltupcryptopaymentsmodule for PLN currency

Operators do not need to manually enable PLN currency as it is available by default after deployment.

### Module configuration

1. Navigate to Admin -> Modules -> Module Manager.
2. Search for `tiltup` and click `Configure`.

## Logging

The logs `INFO` and `ERROR` messages to `var/logs/tiltup_module.log`.

## Release process

Release artifacts are created automatically on merge to `main` by increasing the patch version. To manually increment
the version,
refer to [GitVersion docs](https://gitversion.net/docs/reference/version-increments).

## Useful links

- [Presta docs](https://devdocs.prestashop-project.org/1.7/modules/creation/tutorial/)
- [Official payment module skeleton](https://github.com/PrestaShop/paymentexample)
- [Blumedia payment module](https://github.com/bluepayment-plugin/prestashop-plugin-1.7)
- [PayU payment module](https://github.com/PayU-EMEA/plugin_prestashop)
- [PayNow payment module](https://github.com/pay-now/paynow-prestashop)

