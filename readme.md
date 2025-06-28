# Plentiful

A Drupal module that provides a block, listing the users from a third party service(https://reqres.in).

## Requirements
- Drupal 9/10

## Installation
1. Update Drupal project's `composer.json` **repositories** field with the following:
      ```
      "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/ikebk/plentiful.git"
            }
        ],
      ```
2. Run `composer require ikebk/plentiful`.

3. Run `drush en plentiful` if Drush is already installed or use the admin interface - navigate to */admin/modules* and search 'plentiful'.

4. Navigate to */admin/structure/block* and add Plentiful block to desired region.
