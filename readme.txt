=== Plugin Name ===
Contributors: robotastic
Donate link: http://example.com/
Tags: recipe, schema.org, microformat
Requires at least: 3.4
Tested up to: 3.5
Stable tag: 1.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add thumbnails to the Google results for your Recipes. Make your food blog shine!

== Description ==

= Features =
*	NEW -> WYSIWYG Ingredient and Direction entries
*	NEW -> Customizable templates
*	NEW -> Amazon Associate integration
*	NEW -> Support for [Whisk](http://whisk.com)! 
*	Get those thumbnails next to your Google results
*	Quickly import recipes from existing posts by Selecting and Tagging recipe sections
*	Recipes are automatically formated and can be customized using CSS
*	Create new recipes using free-form text entry, instead of annoying fields for each line
*	Special formatting and a print button, makes it easy for visitors to print recipes
*	Allow readers to easily save recipes to their Facebook Timeline

This plugin makes it simple to add the extra markup Google and other search engines need to better understand your recipes. Most importantly, this is how you get those thumbnails to show up next to your recipes in Google. When recipes are entered using Recipe Schema, it correctly formats the recipe when it is displayed so that search engines can pull in additional information. There are a number of different formats for doing this. This plugin use the [Schema.org](http://schema.org/Recipe) format that was jointly created by Google, Microsoft, and Yahoo, and is the most widely recognized.

There are a number of different Wordpress Plugins for doing this, however what makes Recipe Schema so awesome is how easy it is to add recipes. Quickly add recipe from existing posts by selecting the recipe, and then highlighting and tagging the different parts. No retyping needed! It is just as easy to add recipes from other website. Simply copy, paste, highlight and tag! If you are entering a new recipe from scratch, the free-form entry for directions and ingredients meant that you don't have to worry about getting the right info into the right field.

= How to Use =
Once you have Recipe Schema installed, it is easy to start adding your recipes. Recipe Schema creates a new type of Posts, not shockingly called recipes. Multiple recipes can be inserted into a single Post and a single recipe can be in a bunch of different Posts. 

You need to create a Recipe before you can insert it into a Post. There are 3 different ways to create a new recipe:

**New Recipe**

1.	Click on Recipes on the left hand side of the Wordpress Admin menu
1.	Click on **Add New** under the Recipes submenu
1.	Enter the Recipe, placing the appriopate sections in the right place.
1.	To select a thumbnail for the Recipe, set the Featured Image. This can also be done when you insert a recipe to a post.

**Import from an Existing Post**

1.	Click on Recipes on the left hand side of the Wordpress Admin menu.
1.	Click on **Import from Post** under the Recipes submenu. 
1.	Select the Post that wish to Import a recipe from.
	*	You can filter out Posts that have already been Imported by clicking on **Hide Posts With Recipes**
	*	You can also filter by Post Category and Search the Posts.
1.	The Post will be shown in a Textbox. Select the recipe text in the post and click the **Import** button. 
1.	The selected recipe will be Cut from the Post and the imported Recipe will inserted into the Post.
1.	Select different portions of the recipe and click the appropriate button below the Textbox.
	*	For example, select the recipe title and click the Title button. The title will be copied to the Title field further down the form.
1.	Continue designating the rest of the relevant information.
1.	Scroll down and double check that all of the information has been filled in correctly. Make sure you fill in the Source and URL for where you copied the recipe from.
	*	If the Directions or Ingredients had Section headers you will need to add these. Simply goto the line and hit the Header button.
1.	When you have finished, click **Save Changes**. You will see a preview of the recipe. You can click **Edit Recipe** if you need to make any changes.
1.	The Recipe is automatically Inserted into the Post.

**Import from Web**

1.	Click on Recipes on the left hand side of the Wordpress Admin menu.
1.	Click on **Copy, Paste & Import** under the Recipes submenu, it will open a page with Textbox where you will paste the recipe you wish to import.
1.	Goto the website of the recipe you wish to import. Select and copy the text for the recipe, and paste it into the Textbox.
1.	Select different portions of the recipe and click the appropriate button below the Textbox.
	*	For example, select the recipe title and click the Title button. The title will be copied to the Title field further down the form.
1.	Continue designating the rest of the relevant information.
1.	Scroll down and double check that all of the information has been filled in correctly. Make sure you fill in the Source and URL for where you copied the recipe from.
	*	If the Directions or Ingredients had Section headers you will need to add these. Simply goto the line and hit the Header button.
1.	When you have finished, click Save Changes. You will see a preview of the recipe. You can click "Edit Recipe" if you need to make any changes. You can also edit the Post if needed.


= Insert a Recipe into a Post =
Now that you have created a Recipe, you can insert it into any Post. A Recipe can be inserted into multiple Posts, and a Post can have multiple Recipes.
Here is how to do it:

1.	Click on **Posts** on the left hand side of the Wordpress Admin menu
1.	Select **Edit** on an existing Post you would like to insert a Recipe into
1.	Scroll down the page til you see the Recipe Schema box
1.	Begin typing the title of the Recipe you would. The Title will autocomplete based upon the Recipes which have been entered. Select the Recipe you would like to insert.
1.	Repeat if you would like to add additional Recipes.
1.	The Featured Image for the Recipe will be shown next it. If there is nothing there, or if you want to change it, click **Change Image** and select an image.

By default all of the selected Recipes get added to the end of the Post. If you would like the recipes to be in the middle of the Post, you can use a shortcode. Add the shortcode **[recipes]** into the Post where you would like the Recipes to appear.

== Installation ==

You can download and install the Recipe Schema Plugin using the built-in WordPress plugin installer.

If you download the Recipe Schema Plugin manually, unzip it and upload the *recipe-schema* folder to your website. The foler should go in the */wp-content/plugins/* directory.

Activate the Recipe Schema Plugin in the "Plugins" admin panel using the "Activate" link.

= Settings =
There are a couple of Settings for Recipe Schema. To set them, click on Recipe Schema under the Settings menu in the Wordpress Admin screen.
*	You can let readers bookmark recipes on Facebook through Mr. Cookbook. This is a small website I created that makes it possible to collect recipes across website using this plugin.
*	You can hide/show the Printer button.
*	The Featured Image for a Recipe can also be shown. This setting won't affect whether Google shows thumbnails or not.
*	You can add to the Recipe Type and Cuisine Taxonomies for Recipes

** Template **
To change the way Recipes get printed out, click on the Templates tab under settings. Make changes to the Template code here. Remove sections you don't wanted printed out, like the Prep time for example, or change how they are formatted. It is PHP code, so you will need to know a little about programming.

Your changes to the Template will get lost whenever this plugin gets updated. If you want to keep your changes, it is probably best to make a copy of /wp-content/plugins/recipe-schema/templates/default.php

In future version I am going to make this suck less.

== Frequently Asked Questions ==

= So, I entered a recipe, but my Google result hasn't changed? =

It takes a while for Google to update it listing. You can use the their Schema.org [testing tool](http://www.google.com/webmasters/tools/richsnippets) to make sure they are processing your page correctly. If you have your site configured in Google [Webmaster Central](https://www.google.com/webmasters/tools/), you can also request for them to scan you page, which might speed things up. 



== Screenshots ==

1. Easily enter existing recipes by Selecting and Tagging recipe sections
2. Recipes are automatically formated and can be customized using CSS
3. Create new recipes using free-form text entry, instead of annoying fields for each line

== Changelog ==

= 1.2.1 =
* Support for Whisk enabled

= 1.2 =
* Allows for WYSIWYG entry of Ingredients and Directions
* Customize how Recipes will look with an editable template
* Supports the new Media picker
* Add Amazon items that relate to a recipe and have them linked to your Amazon Associates account.

= 1.1 =
* Adds support through Mr. Cookbook to allow users to bookmark recipes on their Facebook page
* Hopefully fixes an error preventing Admins from getting the settings.

= 1.0 =
* This is the first release, so nothing to report yet



== Upgrade Notice ==

= 1.2 =
Better text entry and a customizable template

= 1.1 =
Let readers bookmark recipes on Facebook

= 1.0 =
Give it a try!


