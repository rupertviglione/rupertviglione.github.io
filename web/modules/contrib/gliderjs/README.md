# Contents of this file

- Introduction
- Requirements
- Recommended modules
- Installation
- Configuration
- Maintainers

## Introduction

The Glider.js module lets you create sliders using the glider.js javascript library in your drupal projects. You can use the Glider.js field formatter in the image fields, or create a view and use the Glider.js view style for the slider creation.

- [For a full description of the module, visit the project page](https://www.drupal.org/project/gliderjs)

- [To submit bug reports and feature suggestions, or to track changes](https://www.drupal.org/project/issues/search/gliderjs)

## Requirements

- [Glider JS](https://asset-packagist.org/package/npm-asset/glider-js) - the Glider JS javascript library required by the module to create the sliders.
- [Drupal Once](https://asset-packagist.org/package/npm-asset/drupal--once) - the official drupal once library, until it's part of core.

## Recommended modules

- [Markdown](https://www.drupal.org/project/markdown) - improves the module help page formatting the README file into valid HTML.

## Installation

### Using Composer

As outlined in Drupal's guide to [Downloading third-party libraries using Composer](https://www.drupal.org/docs/develop/using-composer/using-composer-to-install-drupal-and-manage-dependencies#third-party-libraries), several changes need to be made to the root projects composer.json.

#### Add repository

Add Asset Packagist to the "repositories" section of your project's root composer.json:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://asset-packagist.org"
    }
]
```

#### Installer types and paths

Ensure that NPM assets are registered as new "installer-types" and, in addition to type:drupal-library, they are registered in "installer-paths" to be installed into Drupal's /libraries folder, within the "extra" section of your project's root composer.json file.

```json
"extra": {
    "installer-types": [
        "npm-asset"
    ],
    "installer-paths": {
        "web/libraries/{$name}": [
            "type:drupal-library",
            "type:npm-asset"
        ]
    }
}
```

#### Run composer

Require this package using composer:

```sh
composer require drupal/gliderjs
```

### Manually

#### Glider JS

- [Download the Glider JS library](https://github.com/NickPiscitelli/Glider.js)

- Unzip and copy the folder to /libraries, so you will have the library in the following path /libraries/glider-js/glider.min.js.

#### Once

- [Download the Drupal Once library](https://git.drupalcode.org/project/once/-/tree/1.0.x)

- Unzip and copy the folder to /libraries, so you will have the library in the following path /libraries/drupal--once/dist/once.min.js.

#### Module Install

- [Install as you would normally install a contributed Drupal module](https://www.drupal.org/docs/8/extending-drupal-8/installing-modules).

## Configuration

- Using the Glider.js field formatter:
  - To use the field formatter you need to create a new content type or use an existing one having an image field or an entity reference field. Go to /admin/structure/types/manage/YOUR_CONTENT_TYPE/display and set the formatter for your image field or reference field as Glider.js, and then you will be able to set the different configurations.

- Using the Glider.js views style:
  - To use the views style you need to create or edit an existing view and select Glider.js as the format. Then in the format settings, you will be able to set the different configurations.

## Maintainer

- [Adrian Cid Almaguer (adriancid)](https://www.drupal.org/u/adriancid)
- [Scott Joudry (slydevil)](https://www.drupal.org/u/slydevil)
- [Kyle Samson (ksam902)](https://www.drupal.org/u/ksam902)
- [Zack Shave (zshave)](https://www.drupal.org/u/zshave)
