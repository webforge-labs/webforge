# Migrate to 1.10.x

- refer to the upgrade docs of webforge/process 1.1 to 1.2.x
- you need to require "webforge/configuration-tester" as a dependency if you were using it before
- the directory locations have changed drastically (refer to docs) to be backwars compatible add:
```json
  "extra": {
    "directory-locations": {
      "lib": "lib/",
      "cms-tpl": "resources/tpl/",
      "tpl": "resources/tpl/",
      "resources": "resources",
      "prototypes": "resources/prototypes/",
      "assets-src": "resources/assets/"
    }
  }
``` to your composer.json
