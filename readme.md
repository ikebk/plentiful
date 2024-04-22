## Installation:

1. Update Drupal project's `composer.json` **repositories** file with the following:
  `"repositories": [
      {
          "type": "vcs",
          "url": "https://github.com/ikebk/plentiful.git"
      }
  ],`
2. run `drush en plentiful` if you already have Drush installed or use the admin interface - navigate to /admin/modules and search 'plentiful'.
3. Navigate to admin/structure/block and add Plentiful block to desired region.