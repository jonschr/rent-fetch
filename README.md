# ApartmentSync
A plugin which gathers properties and floorplans from the Yardi API and displays them in a number of different ways.
[Wiki](https://github.com/jonschr/apartment-sync/wiki)

## Dependencies (for full functionality)
- ACF Pro (bundled with this plugin): Used for setting up custom fields. You can install this alongside ApartmentSync, but you don't have to (it's included)
- [Admin Columns Pro](https://www.admincolumns.com/): Used for fancy columns allowing for birds-eye editing and seeing what data is in each floorplan/property/neighborhood
- [Metabox.io](https://wordpress.org/plugins/meta-box/): used for CPT connections between neighborhoods and properties, not bundled
- [Metabox.io Relationships](https://docs.metabox.io/extensions/mb-relationships/)

## Content types
- Floorplans
- Properties
- Neighborhoods

## Taxonomies
- Property types (properties)
- Amenities (properties)
- Areas (neighborhoods)

## Shortcodes
```
[propertymap]
[propertysearch]
[favoriteproperties]
```
[More information](https://github.com/jonschr/apartment-sync/wiki/Included-shortcodes)

## Gutenberg blocks
- Floorplans: shows a configurable grid of the floorplans, using either local information or information from an API that's been synced into a content type.

## Customization
- [Labels](https://github.com/jonschr/apartment-sync/wiki/Customizing-labels-for-beds,-baths,-and-square-feet): you can customize the labels for bedrooms, bathrooms, and square footage. Useful for setting "0 bedroom" to be "studio" instead.
