## 2.10.0
* Minor CSS fixes and updates
* Adding a check to make sure we don't output an availability button in the floorplan archive if there shouldn't be one (if we don't have a link)

## 2.9.0
* Remove the properties that are no longer in the setting

## 2.8.0
* Fix error on single when the API returns an error instead of images for a property

## 2.7.0
* Better min/max calculations for rent

## 2.6.0
* Only show available properties in footers (so now only properties with at least one available floorplan will show anywhere on the site)

## 2.5.0
* Add specials to floorplans layouts
* Add specials to properties layouts
* Add specials to floorplansgrid block

## 2.4.0
* More reliable cancelling of upcoming actions when paused or deleted is set
* Pulling in beds and baths when decimals are used without accidentally converting those to integers
* Minor style adjustments to the filters (removing max width, as we might not always have tons of filters to fill the space)

## 2.3.1 
* Add a filter to urls for the property website links in case they don't have them. This filter is also available for themes to dynamically change those URLs if needed.

## 2.3.0
* Adding support for updating properties (whoops! We had just missed this one, other than the amenities and pets, as those items are actually only handled as part of an update task, as we want to ensure the post actually exists before hooking it up with external stuff).

## 2.2.0
* Reworking the floorplans and getting those ready for other sites to use.

## 2.1.0
* Adding the favorites functionality

## 2.0.2 
* Add settings on backend for the price filter values

## 2.0.1 
* Prevent load flash on filters

## 2.0.0
* Search now fully functional

## 1.19.0
* Flatpickr in place

## 1.18.0
* Price-based search complete

## 1.17.0
* Better functionality for the price-based search

## 1.15.0
* Fixing several php notices
* Adding new logic for the availability dates (fixing a major bug where all floorplans from a given property were getting the same date)

## 1.14.0
* Performance optimization

## 1.13.0
* Adding map toggle reset functionality (it was breaking without it)

## 1.12.0
* Fix line break issue in property archives
* Add toggle to maps
* Remove the double dollar sign on properties in the map

## 1.11.0
* Add new content area for properties

## 1.10.0
* Add setting for the max properties to show in the footer grid

## 1.9.0
* Adding improvements to the properties footer (showing even when unavailable)
* Adding the neighborhood to the single-properties template

## 1.8.0
* Adding nearby properties to the single-properties template
## 1.7.0
* All buttons added to the floorplans archives

## 1.6.0
* Adding the layout for floorplans archives
* Adding the floorplans archive slider
* Adding fancyboxes in floorplans archives
* Adding in the availability data into the floorplans archives

## 1.4.1
* Minor updates

## 1.4.0
* Map is now pretty functional, still default pins

## 1.3.0
* Adding properties to the neighborhoods pages

## 1.2.1
* Bugfix on maps

## 1.2.0
* Getting the API key pulled correctly for Google maps

## 1.1.0
* Registering some content types correctly only when option set

## 1.0.0
* Map in place and working

## 0.42.0
* Improving hover effects on the sliders

## 0.41.0
* Adding better versions of the sliders on the home page (all of the styles, better load times)

## 0.40.0
* Adding the slider

## 0.39.1
* BUGFIX: search was pulling results when the floorplan search was empty but the properties search has results

## 0.39.0
* Property images on the properties-single template

## 0.38.0
* ACP compat with other plugins and themes that use local storage

## 0.37.2
* Style changes to the search

## 0.37.1
* Fix empty value in pets

## 0.37.0
* Get and save the property images

## 0.36.0
* Add option to limit the number of amenities shown
* Adjustments to the backend of properties

## 0.35.0
* Adding the pet policy search

## 0.34.0
* Adding capability to pull pet policy from Yardi

## 0.33.0
* Amenities search now working

## 0.32.0
* Adding capability to grab amenities from Yardi

## 0.31.0
* Single-properties: adding dropdowns by number of floorplans

## 0.30.0
* Adding initial styles for the properties

## 0.29.1
* Button style fixes

## 0.29.0
* Adding the property type taxonomy and enabling it in the search if there are any types

## 0.27.0
* Add the home page search form basics

## 0.26.0
* Remove unrealistically low results from the big search

## 0.25.3
* Attempt to fix empty properties being added to the database

## 0.25.1
* Only use Relevanssi if it's installed
* Only add the search term if there is a search term

## 0.24.1
* Updating the columns for neighborhoods

## 0.24.0
* Converting the search over to find properties

## 0.23.0
* Adding new filters, but not the new functionality

## 0.22.1
* Adding Relevanssi functionality to search the custom fields

## 0.22.0
* Adding text-based search (simple version, only finds titles)

## 0.21.2
* Adding the columns for relationships

## 0.21.1
* Adding ACP for neighborhoods

## 0.21.0
* Adding neighborhoods registration back into the plugin

## 0.20.1
* Better way of doing reset (just reload the page without parameters)

## 0.20.0
* Adding GET parameter detection to beds and baths on the property search

## 0.19.0
* Adding a shortcode to do an ajax search of the floorplans

## 0.18.1
* Adding ability to target a specific floorplan with javascript more easily.

## 0.18.0
* Continuing work on the single-properties template

## 0.17.0
* Starting on the basic version of the single-properties template

## 0.16.0
* Add single template detection and hotswapping

## 0.15.0
* Initial functionality to pull properties

## 0.14.0
* Addition of functionality to detect and delete floorplans attached to properties which are no longer syncing
* Addition of functionality to cancel upcoming sync actions for floorplans attached to properties which are no longer syncing

## 0.13.0
* Fixes to logic forcing deletes of processes

## 0.12.1
* Fixing minor bugs in the floorplans block

## 0.12.0
* Performance improvements when there are many properties to query (we were running into the async triggers themselves causing performance problems).

## 0.10.0
* Adding bedroom filters

## 0.9.1 
* Separating the block into functions (everything has access to a new settings object now), so that we can actually organize a bit better. Needed in order to do the filters the right way, as that will have to be its own function.

## 0.9.0
* Adding floorplan limits capabilities to the Floorplans block

## 0.8.1
* Adding the gravity forms lightbox

## 0.8.0
* Adding action scheduler directly, as submodules don't update properly

## 0.7.0
* Fancybox functionality
* Local featured images working in the Gutenberg block

## 0.6.0
* Gutenberg block added

## 0.5.0
* Adding gulp, initial styles for the floorplans layout

## 0.4.1
* Changing the stable branch to 'main' for PUC

## 0.4.0
* Adding syncing for the acf fields

## 0.3.0
* Adding syncing for the admin columns pro columns

## 0.2.0
* Sync functionality basically in place for Yardi