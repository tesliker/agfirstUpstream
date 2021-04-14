# Overview

The install script adds the consistently used modules and configuration to upstream generated sites. 

# Site Spin-up Instructions

1. In your Pantheon Dashboard click to Create a New Site.
2. Enter your site name and choose the AgFirst Farm Credit Bank Organization and click continue. You will be prompted to agree and confirm. 
3. Choose to Deploy the AgFirst Upstream. This will take a few minutes.
4. Go to the Dev site and install Drupal selecting English language, Minimal profile, and entering your site-specific configuration information.
5. Once your Drupal site is installed, go back to Pantheon and commit any changes from the install. 
6. In Pantheon, switch the Development mode to Git. 
7. Git clone the site to your sandbox. 
8. Edit the default.settings.local.php file to update the database name to a site-specific name.
9. Copy the default.settings.local.php file to settings.local.php
10. ```drush sd``` to pull down the database.
11. In the project root, run ```bash install/install.sh```
12. Once complete, commit the files, including the config export and push to dev. 
13. In your sandbox run ```terminus drush SITESLUG.dev cim```. Ex. ```terminus drush agsouthfc.dev cim```
14. This will install the general modules and config, and once this completes, you can begin the rest of the site build.  
