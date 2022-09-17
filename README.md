# Lourie Basic Framework

![GitHub tag (latest SemVer)](https://img.shields.io/github/v/tag/projector22/lourie-basic-framework)

This project will function as a basic framework and template for future webapps. Versions in the code generally refer to the release version on [Lourie Registration System](https://gitlab.com/projector22/lourie-registration-system), for which they were developed. Framework versions are marked `LBF`. For example:

```php
/**
 * @since   LRS 3.25.0  <- LRS version
 * @since   LBF 0.1.0   <- LBF version
 */
```

## Note

This app is in an early BETA state. Please do not use on anything anywhere near production.

## How to Use

Install and configure [composer](https://getcomposer.org/) for your app if you haven't already, then run the following command:

```sh
composer require projector22/lourie-basic-framework
```

Then you can simply begin calling the tools you need. For example:

```php
<?php

use LBF\HTML\HTML;

HTML::div( ['class' => 'example'] );
// ... Other code
HTML::close_div();
```

## Features

- Auth Tools.
- Dababase Tools.
- Dev Tools.
- A simple Markdown interface. Abstraction from [Parsedown](https://github.com/erusev/parsedown)
- Error handling pages.
- An autoloader class for performing autoloading tasks.
- Generic but useful functions including:
  - various sting handling tools.
  - token parsing tools.
  - etc. There really are quite a lot. Please see `src/Functions/functions.php` for details.
- HTML Generation:
  - Simple HTML elements such as `<div>`, `<span>` or `<h1>` etc.
  - Buttons
  - HTML shortcuts, for example, to generating line breaks.
  - Form elements.
  - Javascript shortcuts.
  - Generating Tables.
  - Generating terminal like feedback.
- Environment loader.
- CLI Tools.
- Cron Tools - _Very broken, do not use_.
- CSV reading and writing.
- Downloader handling.
- Excel files reading and writing.
- File system handler tool.
- JSON handler tool.
- LDP handler tool.
- Email interface tool. Abstraction from [PHPMailer](https://github.com/PHPMailer/PHPMailer).
- PDF generation tool. Abstraction from [TCPDF](https://github.com/tecnickcom/tcpdf).
- A simple HTML spreadsheet creation tool.
- An Upload handler.
- A post update tool called `Trek`.

## Attribution

### Lead Designer

- Gareth Palmer ([Github](https://github.com/projector22), [Gitlab](https://gitlab.com/projector22))

### Open Source

Besides my own, this framework use the following open source tools with grateful thanks.

- [parsedown](https://github.com/erusev/parsedown)
- [tcpdf](https://github.com/tecnickcom/tcpdf)
- [php-feather](https://github.com/Pixelrobin/php-feather)
- [phpmailer](https://github.com/tecnickcom/tcpdf)
