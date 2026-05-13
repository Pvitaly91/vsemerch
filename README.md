Yii 2 Advanced Project Template
===============================

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii2-app-advanced/v/stable.png)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii2-app-advanced/downloads.png)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Build Status](https://travis-ci.org/yiisoft/yii2-app-advanced.svg?branch=master)](https://travis-ci.org/yiisoft/yii2-app-advanced)

OPTIMIZE SHOP IMAGES
--------------------

The `optimize-shop-images` console command compresses large imported product
images in `frontend/web/upload/shop` without changing file names, extensions, or
database records. It optimizes files above `--minSizeKb` and also resizes any
image that exceeds `--maxWidth` or `--maxHeight`, even when the file is smaller
than `--minSizeKb`. Dimension-based replacements may grow the file by up to
`--maxGrowthPercentForDimensions` percent; the default is `30`. PNG files are
skipped to avoid increasing upload size during GD recompression. Images below
`--skipDimensionResizeBelowKb` are not resized by dimensions; the default is
`90`. Skipped image file names are printed by default; use `--showSkipped=0` to
hide them on large runs.

Test dry-run:

```
php yii optimize-shop-images --dryRun=1 --verbose=1
```

Real run:

```
php yii optimize-shop-images
```

Cron after import:

```
php /path/to/project/yii optimize-shop-images --path=@frontend/web/upload/shop --minSizeKb=500 --maxWidth=1600 --maxHeight=1600 --quality=82 --maxGrowthPercentForDimensions=30 --skipDimensionResizeBelowKb=90 --showSkipped=1
```

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
tests                    contains various tests for the advanced application
    codeception/         contains tests developed with Codeception PHP Testing Framework
```
