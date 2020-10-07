# PHP Machine Learning & Data Mining Class (Apriori)

> This is ML and DM Class written in PHP OOP, currently only support "Apriori"
>
> This package is made for my college research purpose. But I made it opensource, so everyone can use this class for their projects (if needed)
> Written by: Fahmi Rizalul

[![Build Status](http://img.shields.io/travis/badges/badgerbadgerbadger.svg?style=flat)](https://travis-ci.org/badges/badgerbadgerbadger) [![License](http://img.shields.io/:license-mit-blue.svg?style=flat)](http://badges.mit-license.org)

## Installation

All you need is just install this package into your project using <a href="https://getcomposer.org/" target="_blank">composer</a>.

```shell
$ composer require rfahmi/ai
```

That's all.

## Features

Currently only support Apriori

## Usage

#### Initialize

```php
$apriori = new Apriori();
$apriori->setSupport(3);
$apriori->setConfidence(0.7);
```

#### Train Model

```php
$items = ['A', 'B', 'C', 'D', 'E'];
$transactions = [
    ['A', 'B', 'C'],
    ['A', 'C'],
    ['A', 'B', 'D'],
    ['A', 'D'],
    ['B', 'C', 'E'],
];
$apriori->train($items, $transactions);
```

#### Get Rules & Frequent Set

```php
$apriori->getRules();
$apriori->getFrequentset();
```

### Prediction

```php
$apriori->predict(['A']);
```

## Support

Reach out to me at one of the following places!

- Website at <a href="https://rfahmi.com" target="_blank">`rfahmi.com`</a>
- Instagram at <a href="https://instagram.com/nm.rfahmi" target="_blank">`Fahmi Rizalul`</a>

## Buy me coffee and snacks

[![paypal](https://www.paypalobjects.com/webstatic/mktg/logo/PP_AcceptanceMarkTray-NoDiscover_243x40.png)](https://www.paypal.com/paypalme/nmrfahmi)

## License

- **[MIT license](http://opensource.org/licenses/mit-license.php)**
- Copyright 2020 © <a href="https://rfahmi.com" target="_blank">Fahmi Rizalul</a>.
