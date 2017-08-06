Mailcatcher
=================

This extension configures TYPO3 to write all sent emails into mbox files in typo3temp/var/mail.
It also provides a backend module where you can find all kind of information about the sent mails.

## Installation via Composer

Add the following lines to the repositories section of your composer.json

```
{
	"type": "git",
	"url": "git@github.com:Domi202/mailcatcher.git"
}
```

Then require the package via composer cli

```
composer require domi202/mailcatcher
```
