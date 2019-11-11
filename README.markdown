OpenPSA [![Build Status](https://secure.travis-ci.org/flack/openpsa.svg?branch=master)](https://travis-ci.org/flack/openpsa)
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Ft438879%2Fopenpsa.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Ft438879%2Fopenpsa?ref=badge_shield)
=======

[OpenPSA](http://midgard-project.org/openpsa/) is a management suite for web agencies and consultants that provides a unified interface for handling many common business processes. It is built on a component architecture that makes it easy to integrate new components for specific requirements and is available as free software under the terms of the LGPL license.

OpenPSA 1.x was initially released as Open Source under the GNU GPL license by [Nemein](http://nemein.com/) on May 8th 2004 to support the [5th anniversary](http://www.midgard-project.org/updates/midgard-5th-anniversary.html) of the [Midgard Project](http://www.midgard-project.org/). The package was originally known as Nemein.Net.

The currently active branch (OpenPSA 9) is developed and supported by [CONTENT CONTROL](http://www.contentcontrol-berlin.de/).

Read more in <http://openpsa2.org/>

## Setup

You can either clone this repo or add `openpsa/midcom` to your `composer.json`

Then, change to your project's root dir and use Composer to install PHP dependencies

    $ wget http://getcomposer.org/installer && php installer
    $ php composer.phar install

This will setup the project directory for OpenPSA usage. Next you should make OpenPSA available under your document root:

    $ ln -s web /var/www/yourdomain

You can then create new database by running:

    $ ./vendor/bin/openpsa-installer midgard2:setup

This will also create a default user with `admin`/`password` which you can later use to access the admin interface. See the [openpsa-installer](https://github.com/flack/openpsa-installer) documentation for more details.

## Setting up Apache

Make sure that you have mod_rewrite enabled:

    a2enmod rewrite

And use something like this in your vhost config (or .htaccess file):

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ /openpsa/rootfile.php [QSA,L]
    
## Setting up Lighttpd

Alternatively, you can also run under lighttpd (or any other web server, for that matter). Enable `rewrite` and `fastcgi` modules in your Lighttpd config (by default `/etc/lighttpd/lighttpd.conf`):

    server.modules += (
        "mod_fastcgi",
        "mod_rewrite"
    )

Also enable FastCGI to talk to your PHP installation:

    fastcgi.server = (
        ".php" => (
            (
                "bin-path" => "/usr/bin/php-cgi",
                "socket" => "/tmp/php.socket"
            )
        )
    )

Then just configure your Lighttpd to pass all requests to the OpenPSA "rootfile":

    url.rewrite-once = (
        "^/openpsa2-static/OpenPsa2/(.*)$" => "/openpsa/themes/OpenPsa2/static/$1",
        "^/openpsa2-static/(.*)$" => "/openpsa/static/$1",
        "^([^\?]*)(\?(.+))?$" => "openpsa/rootfile.php$2"
    )

*Note:* this rewrite rule is a bit too inclusive, to be improved.



## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Ft438879%2Fopenpsa.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Ft438879%2Fopenpsa?ref=badge_large)