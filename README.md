# Xerographer

_**"PHP Framework to help relieve your WordPression."**_

This is the main composer library used in various related OOP WordPress projects. The name Xerographer is partially a
cheeky play on WordPress' Gutenberg editor. Basically stating; It is like WordPress, only using modern techniques. It is
also a nod to _Smalltalk_ the first class based OOP programming language, which was developed at **Xerox's**
_**P**alo **A**lto **R**esearch **C**enter, or **PARC**_.

The only project using this so far is [jascha030/wp-environment](https://github.com/jascha030/wp-environment) (W.I.P.),
a WordPress starter environment, heavily inspired by/based on [roots/bedrock](https://github.com/roots/bedrock).

**Other plans include:**

A small plugin development library as spiritual successor
to [jascha030/wp-plugin-lib](https://github.com/jascha030/wp-plugin-lib), based around a set of interfaces and a class,
aiming to make WordPress' actions and filters faster by implementing the lazy-loading of class-methods with help of
a `psr-11` compatible DI container and hook definitions provided by their classes statically.

And eventually a small starter theme based around `twig/twig` to improve templating, and a configuration system
comparable to that of libraries like Symfony or Laravel. Preferably based a round Symfony components.

**All these projects are based on problems and annoyances I personally encounter regularly, when developing for
WordPress.**

## Prerequisites

* PHP `8.0`
* Composer `2+`
* WordPress `5+`

To use this to it's full potential, the following is also required:

* `laravel/valet`

## Installation

Require as global dependency with composer.

```sh
composer global require jascha030/xerographer
```

## Unit testing

To run the full testsuite, one setup step is required, together with an installed version of `laravel/valet`. Create a
new `.env` file in `tests/.env` by copying the `.env.example` file inside the test directory. 

Fill in your mysql user password and root pass (looking for another solution for that last one).

This will make sure the `Jascha030\Xerox\Tests\Console\Command\InitCommandTest` class test the execution of
the `xerographer init` console command.

When you have taken these steps, you can run the full testsuite through the composer script command:

```sh
composer run phpunit
```
