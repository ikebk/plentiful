## Installation:

1. Update Drupal project's `composer.json` **repositories** field with the following:
  `"repositories": [
      {
          "type": "vcs",
          "url": "https://github.com/ikebk/plentiful.git"
      }
  ],`

2. run `composer update`.

3. run `drush en plentiful` if Drush is already installed or use the admin interface - navigate to */admin/modules* and search 'plentiful'.

4. Navigate to */admin/structure/block* and add Plentiful block to desired region.