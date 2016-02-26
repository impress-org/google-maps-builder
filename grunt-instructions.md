## Using Grunt
### Before Using Grunt
* Install Node, NPM, and grunt-cli globally if not already installed.
    * https://github.com/joyent/node/wiki/installing-node.js-via-package-manager
* Install grunt-init globally if not already installed.
    * `npm install -g grunt-init`
* Install composer to usr/local/bin/composer if not already installed
* Switch to this dir
* Install node modules
    * `npm install`
    * You may need to use `sudo npm install`

### Setup Dev Environment
* `grunt setup-dev`
    * Right now, just runs `composer update` will do other tasks in future.

### Building/Releasing
* Release a new version:
    * Change version number in package.json
    * `grunt release`
    * This makes new zip, git tag and updates version number.
    * NOTE: ZIP will only be committed to branch you are on, so you probably want to merge to master first.
* Make a build with out a release
    * `grunt just_build`
    * This makes zip, but does not tag.
