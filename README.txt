=== Rbl-Navigator ===
Plugin Name: Rbl-Navigator
Contributors: rberthou
Plugin URI: http://www.berthou.com/us/
Donate link: http://berthou.com/donate/
Tags: widget, treeview, menu, posts, bookmarks, categories, list
Requires at least: 2.8
Tested up to: 2.8
Stable tag: 1.0.0

This plugin add a Treeview menu widget in your blog 

== Description ==
Rbl-Navigator is a treeview menu widget with this plugin you can add a treeview menu for you WP bookmarks, last news, last news by categories and more..

== Screenshots ==

1. Sample Widget screen 
2. Widget parameters

== Installation ==

1. Upload rbl-navigator.zip to your Wordpress plugins directory, usually `wp-content/plugins/` and unzip the file.  It will create a `wp-content/plugins/rbl-navigator/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Add "Tliste Navi" widget in your sidebar.

== Usage ==

Define all widget parameters :
 - Title : widget title
 - Data  : Treeview definition
 - Image Base : Base image directory (and image prefix)  default img/ot 
 - Separator : Data seperator default ' § '
 - Max size : max item title size (char length) 

**Example 1 : **
0 § Home     § 14 § /fr/index.php § ?Welcome
0 § %%LAST-5 § Last news § 1 
0 § %%CAT-19,20-5 § Mes categs 19 et 20 § 1
0 § %%CAT-4-5 § Ma categ 4 § -1
0 § %%LNK-Java-10 § Java-Links § -1
0 § A propos   § 19 § /fr/?p=2 

Liste 5 last news 
and 5 last news in categories 19 and 20 (with icon 1)
and 5 last news in categories 4 without "ma categ "item (icon = -1)
and 10 last bookmarks in java categories

**Example 2 : **
0 § Home     § 14 § /fr/index.php § ?Welcome
0 § %%LAST-5 § Last news § 1 
0 § %%CAT-ALL-3 § All categories § 1
0 § %%LNK-ALL-10 § All categories § 1
0 § A propos   § 19 § /fr/?p=2 

Liste 5 last news 
and 3 last news in all categories
and all bookmarks

**Example 2 : **
0 § Home     § 14 § /fr/index.php § ?Welcome
0 § %%LAST-5 § Last news § 1 
0 § %%REP-/www/webdir/fr/tliste/-tliste/ § Liste repertoire § 1

Liste 5 last news 
and
liste all file (and directory) in /www/webdir/fr/tliste/  and use prefix url "tliste/" 

== Changelog ==

== 1.1.0 ==
Add bookmarks / links support
Converts and tests for 2.8.x
Bug correction with sub-categories

== 1.0.0 ==
First internal release
