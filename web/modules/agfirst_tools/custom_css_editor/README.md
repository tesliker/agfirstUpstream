# Custom CSS Editor

This is the Custom CSS Editor module.This modules provides a field on the node
edit form to inject custom CSS.

## Installation

This module has no special installation requirements. Install it as you would
any other module.

## Use

For the CSS to be injected, a twig variable `{{ custom_css_editor }}` must be added to the **html.html.twig**
template of your custom theme. For best results, place the variable toward the bottom of the `<head>` section,
after all other css is loaded.
