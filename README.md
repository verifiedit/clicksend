Please see [this repo](https://github.com/laravel-notification-channels/channels) for instructions on how to submit a channel proposal.

# A Boilerplate repo for contributions

[![Packagist Version (including pre-releases)](https://img.shields.io/packagist/v/verifiedit/laravel-notification-channel-clicksend?include_prereleases&style=flat-square)](https://packagist.org/packages/verifiedit/laravel-notification-channel-clicksend)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![PHPUnit tests](https://github.com/verifiedit/clicksend/actions/workflows/tests.yml/badge.svg)](https://github.com/verifiedit/clicksend/actions/workflows/tests.yml)
[![Standards](https://github.com/verifiedit/clicksend/actions/workflows/standards.yml/badge.svg?branch=main)](https://github.com/verifiedit/clicksend/actions/workflows/standards.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/verifiedit/laravel-notification-channel-clicksend.svg?style=flat-square)](https://packagist.org/packages/verifiedit/laravel-notification-channel-clicksend)

This package makes it easy to send notifications using [ClickSend](https://www.clicksend.com/) with Laravel 5.5+, 6.x, 7.x, 8.x, 9.x, 10.x and 11.x.

## Contents

- [Installation](#installation)
	- [Setting up the ClickSend service](#setting-up-the-ClickSend-service)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation
You can install the package via composer:
``` bash
$ composer require verifiedit/laravel-notification-channel-clicksend
```
### Setting up the ClickSend service

Add your ClikSend details to your .env:
``` 
CLICKSEND_DRIVER=clicksend
CLICKSEND_ENABLED=true
CLICKSEND_USERNAME=XYZ
CLICKSEND_APIKEY=XYZ
CLICKSEND_SMS_FROM=XYZ
``` 

## Usage
You can use the channel in your via() method inside the notification:
``` php
use NotificationChannels\ClickSend\ClickSendChannel;
use NotificationChannels\ClickSend\ClickSendMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [ClickSendChannel::class];
    }

    public function toClickSend($notifiable)
    {
        return (new ClickSendMessage())
            ->setContent("Your {$notifiable->service} account was approved!");
    }
}
``` 
In order to let your Notification know which phone are you sending/calling to, the channel will look for the phone_number attribute of the Notifiable model. If you want to override this behaviour, add the routeNotificationForClickSend method to your Notifiable model.
``` php
public function routeNotificationForClickSend()
{
    return $this->phone_number;
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email developers@verified.com.au instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Verified International](https://github.com/verifiedit)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
