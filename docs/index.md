Menu Navigation
===============

The Menu Navigation scaffold adds a model for storing menu items and a number of presenters for generating
HTML for the menu that can be used directly or as a starting point.

The menu's supported by this module are standard single parent hierarchies i.e. where each menu has a single
parent.

Menu's with no parent are considered to be 'root' menu items.

## The MenuItem Model

The MenuItem model defines the following columns in a tblMenuItem schema:

MenuID
:   An autoincrementing menu ID.
ParentMenuID
:   The ID of this item's parent menu item.
MenuName
:   The name, or text, of the menu item
Url
:   The URL the menu directs to
SecurityOption
:   For use by your application or authentication scaffolds to control visibility of menu options based on
    security settings.
CssClassName
:   Used to give individual menu items specific CSS classes
Position
:   An integer value controlling the position of a menu item among its siblings. Higher numbers make a menu
    item appear before others.
ParentMenuIDs
:   A comma separated list of all the parents of this item right back to the root node.

