# Configuration

Webforge tries to use no configuration at all. But sometimes its very handy to store some values on your host, so that you won't have to supply them again.

## host configuration

Some global configuration settings that are specialized to the current host you're working on (your development machine) are stored in a configuration file. This file is called the host-config. Usually it is a simple php file that defines an array. You have to configure this host-config only once for every new development machine (or webserver). 
The host configuration should be the place for sensible information on your host because you never want to check in the host-configuration. You could store your mysql root password here to allow webforge to create your databases without the fear that you'll ever forget to ignore it.
That's why the host configuration is usually stored in your $HOME directory. Webforge creates a `.webforge` directory in it.  You'll find a host-config template in etc/host-config.dist.php from the webforge repository. If you do not like this place for the webforge storage, you can alter it with setting the env variable WEBFORGE. It should point to a directory where the webforge user is able to write to.

## packages index

When you register a package with webforge it stores the index (and the location) of the package in the `packages.json` in your .webforge directory (WEBFORGE env variable). Be sure to check this file if webforge cannot boot, sometimes a package has moved and webforge didn't notice.

## debug

Webforge can tell you where to find your host configuration (or where to put it) with the CLI interface.

```
webforge info
```