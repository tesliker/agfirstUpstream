#Cyberwoven Theme Suggestions

This module provides a few theme suggestions that we tend to want during themeing of Drupal 8 sites.  If you find you need theme suggestions that are not project specific consider adding them to this module.

 We should also watch these issues to see if general improvements arrive in core:
 * https://www.drupal.org/node/2270883
 * https://www.drupal.org/project/drupal/issues/2808481

##Custom Block Suggestions:

Adds an additional theme suggestion for the custom block type (from the Custom block library) that is less specific than the
suggestion for the block id. This uses the _machine name_ of the block, so it can be used for all blocks of that type.

* block--shareyourstoryblock.html.twig
* **block--custom-type--call-to-action.html.twig**
* block--block-content--bf80b027-5dd5-4bf3-beef-a8986c4ccf61.html.twig
* block--block-content.html.twig
* block.html.twig

There is also a preprocess hook for custom blocks that add better cache tags to their arrays to improve automatic cache clearing.

##Image Style Suggestions:

Adds an additional theme suggestion for images that are displayed using an image style.

   * **image-style--mma-slide.html.twig**
   * image-style.html.twig

##Taxonomy Term Suggestions:
Core doesn't provide theme suggestions at the display mode level.

* taxonomy-term--65.html.twig
* **taxonomy-term--serivce-types--default.html.twig**
* taxonomy-term--service-types.html.twig
* taxonomy-term.html.twig

##Views Field Suggestions

Add field/display specific suggestions more clearly than Core's:

 **views-view-fields--[$view->id()]--[$display['id']];**

##Error Page Suggestions

Adds page suggestions for 401, 403, and 404 pages:

* page--system--401.html.twig
* page--system--403.html.twig
* page--system--404.html.twig
