# MailerLite WHMCS Addon Modules #

## Intoduction
The MailerLite integration leverages MailerLite's API to synchronize customer, order and abandoned cart data with your MailerLite account. 
This allows you to take full advantage of MailerLite's advanced e-commerce automations to setup manual and automated campaigns including:
- Thank you's to new customers
- Automated follow-up's on abandoned carts
- To provide on-boarding/drip-feed campaigns to new customers
- Attempt to win back lapsed customers who haven't bought anything recently
- Reward your best customers based on order count or total spent

## Installation
1. On a server create a `mailerlite` directory inside `addons/modules`
2. Clone the code:
```
cd addons/modules/mailerlite
git clone https://github.com/mint-hosting/whmcs-addon-mailerlite.git .
```
3. Run `composer` inside `addons/modules/mailerlite` to install required libraries:
```
composer install
``

## Initial Setup

1. Navigate to `Configuration () > System Settings > Addon Modules` or, prior to WHMCS 8.0, `Setup > Addon Modules`.
2. Locate the "'MailerLite'" module and click `Activate`.
3. Assign your admin user role group (typically Full Administrator), access to the addon.
4. Navigate to `Addons > MailerLite` to access
The first time you access the MailerLite addon you will be guided through a setup process that connects your WHMCS installation with your MailerLite account.

## Development
An addon module allows you to add additional functionality to WHMCS. It can provide both client and admin facing user interfaces, as well as utilise hook functionality within WHMCS.

For more information, please refer to the online documentation at https://developers.whmcs.com/addon-modules/

## Tests

We strongly encourage you to write unit tests for your work. Within this SDK we provide a sample unit test based upon the widely used PHPUnit.

## Resources
* [Developer Portal](https://developers.whmcs.com/)
* [Hook Documentation](https://developers.whmcs.com/hooks/)
* [API Documentation](https://developers.whmcs.com/api/)

