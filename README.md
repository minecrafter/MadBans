# MadBans

This project was supposed to be a flexible administration GUI for bans, mutes, and warnings. Due to a lack of time and no desire to write any PHP, I am open-sourcing the project in its present state. You are free to contribute code to the project and complete.

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
