# [Modular Gaming Payment](http://www.modulargaming.com)

Payment is a module for [Modular Gaming](https://github.com/modulargaming/modulargaming), a modular [persistent browser based game](http://www.pbbg.org) framework.

It adds support for processing payments within Modular Gaming, using the [Omnipay Library](https://github.com/adrianmacneil/omnipay).

## Supported Gateways

* PayPal Express Checkout
* PayPal Recurring Payments (Express Checkout)

## Requirements

* PHP 5.3.3+
* MySQL
* [Composer](http://getcomposer.org) (Dependency Manager)

## Installation

Payment is installed using composer, simply add it as a dependency to your ```composer.json``` file:
```javascript
{
    "require": {
        "modulargaming/payment": "~1.0.0"
    }
}
```

