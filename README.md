# Sitegeist.GoldenGate.Neos.Api
## Lowlevel-api for Neos-Shopware communication

*THIS IS EXPERIMENTAL CODE. EVERYTHING IN HERE MAY CHANGE AND MAY EVEN
BE TOTALLY ABANDONED. IF YOU WANT TO USE FOR A PROJECT THIS CONTACT US
FOR MORE INFORMATIONS OR CREATE A PERSONAL FORK.*

This package contains the api and eel helpers for reading product data.
Additionally this package brings helpers for shopware specific cache tags
and endpoints to notify neos about changes in shopware to trigger cache
flushing where needed.

*The main idea of the package is to achieve transparent data access
without error prone data synchronisation via caching of api results
and rendered resuls but use cache-entry tagging and cache invalidation
once Neos is notified about changes from Shopware*

### Authors & Sponsors

* Martin Ficzel - ficzel@sitegeist.de

*The development and the public-releases of this package is generously
sponsored by our employer http://www.sitegeist.de.*

## Shops

The shop connections are stored as settings. By default the shop with
the key `default` is used.

```
Sitegeist:
  GoldenGate:
    Neos:
      Api:
        shops:
          default:
            title: 'Default Shop'
            subshop: ~

            # connection
            protocol: ~
            host: ~
            port: ~
            base: ~

            # api access
            user: ~
            password: ~
```

## EEl Helpers

The package brings eel-helpers for reading access to shopware domain
data and for tagging cache-items.

### Shopware.Api

The api-helper allows to fetch shopaware DTOs as defined in package
sitegeist/goldengate-dto. See https://github.com/sitegeist/Sitegeist.GoldenGate.Dto

 - `Shopware.Api.productReferences( shopIdentifier = 'default', minPrice = null, maxPrice = null, filterGroupOptionIds = [], categoryIds = [])`
   : Find productReferences and optionally pass arguments.
 - `Shopware.Api.product( shopIdentifier = 'default', product)` : Complete product, accepts productReference or id.
 - `Shopware.Api.categoryReferences( shopIdentifier = 'default')` : All available category-references.
 - `Shopware.Api.category( shopIdentifier = 'default', category)` : Complete category, accepts categoryReference or id.
 - `Shopware.Api.filterGroupReferences( shopIdentifier = 'default')` : All available filterGroup-references.
 - `Shopware.Api.filterGroup( shopIdentifier = 'default',  filterGroupReference )` : Complete FilterGroup with all options.

Please note: The *References helper will return reference objects that
only contain `id` and `label` for peformance reasons. This is for
searching filtering and providing selector interfaces on the neos side.
The Reference can be used to get the whole item for rendering.

### Shopware.Cache

The helpers are used to render cache tags for the Caches
Neos_Fusion_Content and the Sitegeist_GoldenGate_Neos_Api. By tagging
fusion content with such tags it can be ensured that this items are
revaluated once items were updated in shopware. (see Event-API below).

 - `Shopware.Api.Cache.productTag` : Render cache tags for shopware-products, accepts IDs, Products, ProductReferences and arrays of those
 - `Shopware.Api.Cache.categoryTag` : Render cache tags for shopware-categories, accepts IDs, Products, ProductReferences and arrays of those

## Caches

The package has a cache for the results that ware fetched from the
shopware api. The `Sitegeist_GoldenGate_Neos_Api` cache has is enabled
via setting `Sitegeist.GoldenGate.Neos.Api.enableCache: true`.
If enabled the api results are stored and tagged with the Product or
Category tags.

## Event-API

The package contains a flow controller with two endpoints to recieve
notifications from shopware about changes and invalidate caches if
needed.

- `//sitegeist/goldengate/events/product` invalidate caches for product. Accepts arguments product and productReference as serialized json.
- `//sitegeist/goldengate/events/category` invalidate caches for category. Accepts arguments category and categoryReferebce as serialized json.

The controller will use the cache helper to generate the appropriate
cache-tags for each object and flush those tags in the
`Neos_Fusion_Content` and the `Sitegeist_GoldenGate_Neos_Api` Cache.

## Logging

The package logs api and cache invalidation events to the
Sitegeist_GoldenGate_Api log. The Settings for the log can be
controlled via path `Sitegeist.GoldenGate.Neos.Api.log`. By default
the Log will contain all api events in `Development` context and
ERROR events in `Production` context.

## Installation

THIS PACKAGE IS NOT YET PUBLISHED.

## Contribution

We will gladly accept contributions. Please send us pull requests.
