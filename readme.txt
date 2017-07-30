=== Tweets from the map ===
Contributors: stacey
Tags: tweets, map, theme, search, map, shortcode
License: GPLv2 or later
Displays a list of tweets for a given topic and coordinates using a shortcode.

== Description ==
This plugin allows you to place on any page of your WordPress powered blog or website, using a shortcode, a list of tweets on the given topic and coordinates. The administrative interface makes setting options easy.

= Features (non-exhaustive) = 
* The API settings page for Google maps and Twitter. Twitter api gets tweets within a given radius and on the corresponding theme, saving in the database to prevent multiple requests. Using Google api it is possible to specify the coordinates of the tweet search on the map.
* Tweets search settings page contains:
- subject field
- fields for latitude / longitude
- field for radius + range slider
- polygon on the map, with a marker in the center (the size of the circle depends on the selected radius), which allows you to: set the marker by clicking on the map (marker coordinates are inserted into the corresponding field); pointing  the coordinates of the marker manually (the marker on the map reacts to changes in the corresponding fields)
* Using wp_schedule_event the cron script accesses twitter api at 1 o'clock and updates Tweets in the database
* Using shortcode, you can place the last 20 tweets in a neat container on the site pages, and also set some stylistic settings for the container.
* The page in the admin area where you can view all the tweets in the form of a table that are saved in the database. There are actions (delete, edit), Bulk actions (delete), pagination, search, sorting by date and by topic, the number of elements on one page is made to be set (Screen options).
* The page with the tweets table has links to download the table in csv and xml. The files display all the information on tweets, which is stored in the database.

= Translations =
Tweets from the map plugin support language packs. Now the plugin is available in two languages: English and Russian.

