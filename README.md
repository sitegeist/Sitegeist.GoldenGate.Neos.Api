# Sitegeist.GoldenGate.Neos.Api
## Lowlevel-api for Neos-Shopware communication

This package contains the api and eel helpers for reading product data.
Additionally this package brings helpers for shopware specific cache tags and endpoints to notify
neos about changes in shopware to trigger cache flushing if needed

### Authors & Sponsors

* Martin Ficzel - ficzel@sitegeist.de

*The development and the public-releases of this package is generously sponsored
by our employer http://www.sitegeist.de.*

## EEl Helpers

### Shopware.Api

 - `Shopware.Api.productReferences( shopIdentifier = 'default', minPrice = null, maxPrice = null, filterGroupOptionIds = [], categoryIds = [])`
   : Find productReferences and optionally pass arguments.
 - `Shopware.Api.product( shopIdentifier = 'default', product)` : Complete product, accepts productReference or id.
 - `Shopware.Api.categoryReferences( shopIdentifier = 'default')` : All available category-references.
 - `Shopware.Api.category( shopIdentifier = 'default', category)` : Complete category, accepts categoryReference or id.
 - `Shopware.Api.filterGroupReferences( shopIdentifier = 'default')` : All available filterGroup-references.
 - `Shopware.Api.filterGroup( shopIdentifier = 'default',  filterGroupReference )` : Complete FilterGroup with all options.

Please note: The *References helper will return reference objects that only contain `id` and `label`.
This is for searching filtering and providing selector interfaces on the neos side. The Reference can be
used to get the whole item for rendering.

### Shopware.Cache

The helpers are used to render cache tags for the FusionContentCache and the Shopaware API Cache.
By tagging fusion results with such tags it can be ensured that this items are reevaluated once
items were updated in shopwadre (if Neos is notified via Event.API below).

 - `Shopware.Api.Cache.productTag` : Render cache tags for shopware-products, accepts IDs, Products, ProductReferences and arrays of those
 - `Shopware.Api.Cache.categoryTag` : Render cache tags for shopware-categories, accepts IDs, Products, ProductReferences and arrays of those

## Caches

## Events

## Logs

## Status

THIS IS EXPERIMENTAL CODE. EVERYTHING IN HERE MAY CHANGE AND MAY EVEN BE TOTALLY ABANDONED.
IF YOU WANT TO USE FOR A PROJECT THIS CONTACT US FOR MORE INFORMATIONS OR CREATE A PERSONAL FORK.

## Installation

THIS PACKAGE IS NOT YET PUBLISHED.

## Contribution

We will gladly accept contributions. Please send us pull requests.
