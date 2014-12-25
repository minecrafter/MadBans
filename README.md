# MadBans

**MadBans** is a flexible web-based administration interface for several banning plugins.

By default, support for BungeeAdminTools and BanManager is available. Other providers can be written as needed.

## Features

* Clean interface
* Written in PHP using [Silex](http://silex.sensiolabs.org/)
* UUID-safe (converts from player names and UUIDs automatically)
* Highly extensible

## Installation

### Requirements

* PHP 5.4 (MadBans makes extensive use of shorthand array syntax and traits)
* [Composer](https://getcomposer.org/) (only when compiling via Git)

### From Git

* Clone this repository.
* Run `composer update` and `composer dump-autoload`.
* Continue to the section after the next section.

### From Bundle

* Download the bundle and upload it to your web server.
* Unzip the bundle.

### Install the Software

* Navigate to where the software was installed. You will see the installer. Fill the form provided.
* The installer will generate configuration; you will need to place this in the `configuration` directory.
* Afterwards, the install will be complete and the web interface will be functional.

## Demo

A demo instance is available at [minecraft.minimum.io/madbans-demo](http://minecraft.minimum.io/madbans-demo). The username
is `demo` and the password is `demo`. The demo simulates a offline-mode server running BungeeAdminTools.

The demo is wiped every day at 12AM Eastern Time.