# SilverStripe Content Blocks

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pixelneat/silverstripe-content-blocks/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pixelneat/silverstripe-content-blocks/?branch=master)

Accessories for [Blocks](https://github.com/sheadawson/silverstripe-blocks) module. This repository are always up-to-date with new features of blocks. 

## Blocks includes

* [Content Image](docs/CONTENT_IMAGE_BLOCK.md) - block can store multiple images with sortable feature, but it required to have [SortableUploadField](https://github.com/bummzack/sortablefile) dependency installed. Also have a nice feature like to choose the template for your layout.

* [Google Maps](docs/GOOGLE_MAPS_BLOCK.md) - block with interactive google maps at the CMS. Can be set multiple markers, detecting zoom and position coordinates. Can be set global marker image or each marker individually.

## Global usage

* If you did not want one of those accessory blocks, simple set the blocks configuration

```yaml
    BlockManager:
      themes:
        YourTheme:
          disabled_blocks:
            - BaseBlock # this should be always set
            - ContentImageBlock # this extra block to be hidden
```

## Contributing

To contributing a module [read here](docs/CONTRIBUTING.md) for compliance with the rules and understand how it's working. If you have any questions, feel free to ask maintainer of Donatas <donatas@navidonskis.com>.

## TODO

 1. Each class should be in namespace, but waiting when [silverstripe-gridfieldextensions](https://github.com/silverstripe-australia/silverstripe-gridfieldextensions) their support the namespace'd class'es when creating a new block.
 2. 