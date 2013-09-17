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
		"modulargaming/payment": "~0.1.0"
	}
}
```

## Rewards

**Note: Rewards are only for regular payments NOT Recurring.**

The reward system is driver based, this allows you to define multiple rewards for a single package.
Currently only Points are supported, however it is quite easy to implement your own driver.

```php
class Payment_Reward_Type extends Payment_Reward {

	private $_reward;

	public function __construct($reward)
	{
		$this->_reward = $reward;
	}

	public function reward(Model_User $user)
	{
		// TODO: Write the reward code.
	}

}
```

Example structure for rewards row in payment_packages table
```javascript
{
   "Points": 200
}
```
This will give the buyer 200 points.

For examples, check the current drivers, Payment/Reward.