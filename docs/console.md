# Console

## Release

Webforge uses the awesome [liip/RMT](https://github.com/liip/RMT) for managing releases. Bascially it adds another config (rmt.json) to your project root and makes your releases automatic. It makes some checks before and asks for your increment options, etc.

With webforge RMT is easy to run:

```
webforge release
```

If it's not installed yet, It will pull in the liip/rmt package into your project. After that commit and adjust the config.
Run the webforge release again to release the first version of your component. The defaults from webforge are:
  - tags are persisted in git tasks
  - your versions are semantic
  - your working copy is clean

To just see the current version use:

```
webforge release current
```

