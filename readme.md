# Postscript
Contributors: hearvox
Donate link: http://hearingvoices.com/tools
Tags: script, javascript, styles, stylesheet, css
Requires at least: 3.3
Tested up to: 4.4
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Data visionaries and multi-mediators, make any post sing with scripts and splendid with styles, all from within WordPress.

## Description

### Enqueue Registered Styles and Scripts (by Handle)

Use the Postscript meta box (Edit Post screen) to enqueue registered of styles and script, listed in checkboxes by handle.).

### Enqueue Unregistered Styles, Scripts, and Data Files (by URL)

For each post, you can also enqueue unregistered files, by entering in the meta boxes text fields for:
* A stylesheet URL.
* A JavaScript URL.
* A data URL (e.g, JSON).

### Add Post and Body Classes

For each post, you can also add:
* Body classes, to the HTML `<body>` tag (requires `body_class()` in theme).
* Post classes, to the WordPress inserted `class="post"` list (requires `post_class()` in theme).

## Installation

To install the use the Postscript plugin:

1. Upload the `postscript` directory and content to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the Setting: Postscript options screen.

## Frequently Asked Questions

### A question that someone might have

An answer to that question.

## Contribute

Postscript is now on [GitHub](https://github.com/hearvox/postscript). Pull Requests welcome.

### What about foo bar?

Answer to foo bar dilemma.

## Screenshots

1. Postscript meta box on the Edit Post screen, with checkboxes for selecting admin-approved handles of registered scripts and styles, text fields for adding  an URL of an unregistered one styles and two scripts, and text fields for inserting classes via body_class() and post_classs().
2. This is the second screen shot

## Changelog

### 1.0
* A change since the previous version.
* Another change.

### 0.5
* List versions from most recent at top to oldest at bottom.

## Upgrade Notice

### 1.0
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

### 0.5
This version fixes a security related bug.  Upgrade immediately.

## A brief Markdown Example

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`
