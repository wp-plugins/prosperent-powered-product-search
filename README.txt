=== Prosperent Product Search ===
Contributors: Prosperent Brandon
Tags: Prosperent, products, search, money, SEO, affiliate
Requires at least: 3.0
Tested up to: 3.5
Stable tag: NA at this time
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a product search box widget to your blog, which viewers can use to search and make purchases and you will earn a commission.

== Description ==

**[Prosperent Suite](http://wordpress.org/extend/plugins/prosperent-suite/)** *The newest Prosperent Plugin, includes all other Prosperent plugins in one.*

* Please use the Prosperent Suite in place of your Product Search.



*Prosperent Product Search*

This plugin will help make/increase earnings from your blog. It will add a product search box to your WordPress site, which viewers can use to search for products.
When they click the product through your site they will be redirected to the Merchant's site. If they make a purchase you will earn a commission from the sale.

Now you can place the shortcode [prosper_store][/prosper_store] anywhere, on any other page and it will give you another instance of the store.
The search box is now inside its own widget, and has shortcode available as well. [prosper_search][/prosper_search] will give you a search bar in any location you'd like it. And you can alter its CSS with the 'Additional CSS' setting.

*Why Prosperent Product Search?*

Prosperent Product Search uses Prosperent's API, which is a very advanced API that offers you access to 3000 merchants and 50 million products from all of the top
online retailers. Stores like Zappos, 6pm, Best Buy, Overstock, REI, Advance Auto Parts, Kohl's, Gap, Banana Republic, Cabelas, and thousands more. If it is sold online,
we probably have it in our system.

We have an ever growing community, which is always willing to answer questions and lend a helping hand, and our team here at Prosperent is also available on the forum.

== Installation ==

1.	Head over to [Prosperent](http://goo.gl/6X8OT) and click Join, its Free to do so. Create your account and once you are signed in, click the API tab up top. This page will give you the information you need to
    know about the API. But all you need to do here at the moment is click API Keys on the submenu and click Add New API Key. This will get you the API key you'll need so commissions can be tracked back to you.
    Name it whatever you'd like and you'll see that its created a key for you.
2.	Save that key somewhere as you'll be needing it later.
3.	Upload the `prosperent-powered-product-search` folder to the `/wp-content/plugins/` directory.
4.	Activate the plugin through the 'Plugins' menu in WordPress.
5.  Under 'Appearance', go to 'Widgets' and add the 'Prosperent Store' widget where you'd like it. You can give it a title if you want as well.
    * If you have Accessibility Mode activated it will show 'Add' next to the widget, click 'Add' instead of trying to drag it.
6.	Go to the `Prosperent Settings` under Settings and edit those that you'd like, don't forget to add your API key here.

        * API Key, input the API Key that you got earlier.
        * Facets, True or False, this setting enables or disables the use of facets. Facets allow someone to narrow down a result by brand and merchant. (Facets are enabled by default.)
        * Limit, The is the API limit, the max is 100. Set anywhere between 1-100. (Default is 100.)
        * Default Sort, when someone first searches for a product, this is how data will be returned. relevance desc = Relevancy, price asc = Low to High, price desc = High to Low. (Relevance desc is default.)
        * Parent Directory- if you make the product page with a parent directory, insert that here with the leading slash (/) example: /sample-page (Default is blank, no parent.)
        * Merchant Facets and Brand Facets. The number of each facets to show in the primary list. (Brand default is 12 and Merchant default is 10.)
        * Negative Merchant and Brand Facets. Merchants and Brands to discard from results.
        * Starting Query. When first visited, the site will use this query if one has not been given by the user. If no starting query is set, it shows the no results page.
        * Base URL. If you have a different url from 'your-blog.com/product' that you want the search query to be posted to (For example, if you created a 'Shop' page, with the url your-blog.com/shop, change this to shop).
        * Additional CSS. Additional CSS for the shortcode search bar if used.
        * Logo Image and Logo Image-Small, these are the logos associated with search input box. Only set one to TRUE. (Logo Image is by default set to TRUE.)

7.	The plugin automatically creates a `New Page` called product. Go into that page and change the title to whatever you would like to be visible. Also change the template to your liking. (On the twenty-eleven theme I used
    the `showcase` template.)
    Also, now that the results is shortcoded, you can add [prosper_store][/prosper_store] to any page.
    Reminder, if you use a different page from '/products' to display search results change the 'Base_URL' Setting.

* Congratulations, you have a fully functioning product search engine embedded on your WordPress page.
* Log in to Prosperent.com every so often to check your stats.



Additionally:

* You do not necessarily need to add the search bar to a page, as the Product page has a search bar included with the search results. But it is recommended to increase the use.

* If you want to add the search box in the header of your page, add `<?php prospere_header(); ?>` in your themes `header.php` file where you'd like it to be located.

== Frequently Asked Questions ==

= What is Prosperent? =

Prosperent is a company that is serious about getting you the tools that simplify your life as an affiliate marketer. We manage relationships with merchants, clean data feeds, and provide a variety of publisher tools to get products on your site quickly and easily.

= How many merchants does Prosperent work with? =

Currently over 2,000 and growing.

= How many products does Prosperent have? =

We currently index and search against almost 50 million products.

= Where can publishers go to get help? =

Our Community Forums are a fantastic resource. Our entire team is active on a daily basis, and we are always here to lend a helping hand no matter what the question may be.

= How do I get paid? =

Prosperent pays publishers net30 which means we pay you 30 days after commission event takes place. This gives merchants time to see if a product is returned, or otherwise needs to be delayed for whatever reason.

= How can we track our earnings? =

We have a comprehensive reporting system in place that allows you to see which pages are generating earnings, which city/state/country the sales are coming from, and which individual products and retailers are providing those sales.

= What is the revenue split? =

We take a 30% commission and pay you the other 70%. If you are a larger publisher this split changes to 80/20.

= What are the commissions paid and terms? =

The commission rates vary from merchant to merchant, but we are always negotiating the highest rates in the industry. We pay out net30 like most networks. The only exception is when a merchant that we work with extends a commission based on their return policy. Our reporting interface reflects this and allows you to see the status of each commission. It's the same as what you would experience with any of the other affiliate networks like commission junction.

== Changelog ==

= 3.1 =

* changed image servers
* fixed small bug

= 2.9 =

* fixed bug in 2.8

= 2.8 =

*Unique Titles on search pages, display query and your site name

= 2.5 =

* Responsive design CSS fix

= 2.5 =

* Responsive design, first implementation. Let me know how this works for you. I'll make adjustments if needed.

= 2.4 =

* Works for PHP 5.2.x again, I was using some 5.3 methods in the last update that broke some people's shops (May want to update to your PHP version if you are still running 5.2 if you can)

= 2.2 =

* You now have access to the Coupon API and the Celebrity API
* When a Filter is clicked, it will put the Merchant/Brand/Celebrity picture next to the filter name
* Pagination works again
* Pagination will clear if a new query, sort method, or filter is used.


= 2.0 =
Another big Update

* The main search bar is now a widget that you can place in your sidebars or footer areas.
* The search bar has also been added as a shortcode for those who want to place it somewhere else. [prosper_search][/prosper_search]
* Added a search bar to the search results page for easy access and searching.
* Changed the look of the search bar.
* Added additional settings, and placed the 'Prosperent Settings' under the settings menu now.

= 1.9 =
DEACTIVATE and REACTIVATE this plugin to make the new shortcode changes take effect.

* Fixed collisions that the pagination class was having with other classes in some instances.
* Changed the shortcode to be [prosper_store][/prosper_store] (Placing that on any other pages will give you another store.
* Updated pagination, now includes First, Last, Previous and Next buttons.

= 1.8 =
* Fixed issues with Starting Query when a facet was clicked.

= 1.7 =
* Changed how the product links work.

= 1.6 =
* Updated Admin Options.

= 1.5 =
* API updated
* Query Strips Slashes so products with 's work correctly
* Added a 'Starting Query' in Admin Options, the product page uses this query first if none has been given by the user. That way
  it doesn't just show a no results page.
* Fixed some CSS issues

= 1.2 =
* Complete optimization
* Product file redone

= 1.1 =
* include() error fixed
* round() error fixed

= 1.0 =
* First Release

== Notes ==

If your `product` page has a parent, make sure you assign that in the `Prosperent Settings` for `Parent Directory` with the leading slash.

You may also have to alter a little CSS as to the exact placement of the input box and the logo. Ask if you need any assistance doing so. The file to edit will be `productSearch.css` in the CSS folder.

If you have any questions or suggestions, please feel free to ask me here or on the [Prosperent Community](http://community.prosperent.com/forum.php), or email me Prosperent Brandon at brandon@prosperent.com
