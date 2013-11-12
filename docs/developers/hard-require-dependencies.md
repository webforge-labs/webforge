# Require dependencies

> webforge/webforge requires webforge/common (latest dev) 
means: If I want to update webforge/common I have to change the requirement in webforge/webforge

webforge/webforge requires webforge/common (latest dev) 
webforge/webforge requires nikic/php-parser (latest stable)
webforge/webforge requires webforge/types, webforeg/console, webforge/config (latest stable)
webforge/webforge requires guzzle/guzzle (latest stable)
webforge/webforge requires psc-cms in require-dev (you have to update psc-cms webforge major/minor version to pass the acceptance test on travis)
webforge/webforge requires webforge/console (currently dev should be stable)

webforge/common requires webforge/process (latest dev)
webforge/common requires webforge/collections (latest stable)
webforge/common requires seld/jsonlint (latest stable)

webforge/collections requires doctrine/collections (latest something)

psc-cms requires webforge/webforge and builds a circly with the require-dev from webforge/webforge
