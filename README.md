WP Admin Menu
=========================

Template for automatically creating link menu based on menu in WordPress’s Menu in Appearance.

### Use

    use WaughJ\WPAdminMenu\WPAdminMenu;
    $menu = new WPAdminMenu( 'header-nav', 'Header Nav' );
    $menu->printMenu();

## Changelog

### 0.7.0
* Add ability to automatically set current page
* Optimize menu use so that it only runs the list converter once @ initialization

### 0.6.0
* Add Ability to Not Show Link for Current Page

### 0.5.0
* Add Current Link class to Possible Attributes

### 0.4.0
* Add Custom Attributes for Printing

### 0.3.0
* Add Ability to Stringify Instance

### 0.2.0
* Make Nav Registration Automatically Get Theme Name

### 0.1.0
* Initial Release
