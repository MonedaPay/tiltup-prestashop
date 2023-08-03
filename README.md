# tiltup-prestashop

TiltUp Prestashop plugin

## Local env setup

### Windows

1. Download & install [Xampp](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/7.4.33/) as local PHP
   interpreter.
2. Download & install [Composer](https://getcomposer.org/download/)
3. Download & unzip [Prestashop release](https://github.com/PrestaShop/PrestaShop/releases/tag/1.7.8.9) to a dedicated
   folder (to be later included as External Libraries in your IDE).
4. Clone this repo.
5. Add Prestashop "binaries" as External Library in your IDE.
6. Clone and add [this repo](https://github.com/julienbourdeau/PhpStorm-PrestaShop-Autocomplete) as External Library in
   your IDE for code autocompletion.

## Bootstrapping the application

1. `composer up` will spin up a Dockerized MariaDB & Prestashop instance.
2. Navigate to `localhost:8080` to access your shop.
3. The repo folder is mounted onto the Prestashop container (aka hot reload), so all your changes will be immediately
   reflected in the shop.

## Prestashop

### Module installation

On a vanilla environment the TiltUp module needs to be installed before it becomes usable. To that end, navigate
to `localhost:8080/admin` -> `Modules` -> `Module marketplace`, search for `tiltup` and hit `Install` on the module
tile.

