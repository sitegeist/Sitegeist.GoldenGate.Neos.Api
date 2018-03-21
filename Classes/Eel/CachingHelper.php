<?php
namespace Sitegeist\GoldenGate\Neos\Api\Eel;

use Neos\Flow\Annotations as Flow;
use Neos\Eel\ProtectedContextAwareInterface;
use Sitegeist\GoldenGate\Dto\Structure\Structure;
use Sitegeist\GoldenGate\Dto\Structure\Product;
use Sitegeist\GoldenGate\Dto\Structure\ProductReference;
use Sitegeist\GoldenGate\Dto\Structure\Category;
use Sitegeist\GoldenGate\Dto\Structure\CategoryReference;
use Sitegeist\GoldenGate\Neos\Api\Log\ApiLoggerInterface;

/**
 * Class CacheHelper
 * @package Sitegeist\GoldenGate\Neos\Api\Eel
 */
class CachingHelper implements ProtectedContextAwareInterface
{

    const PRODUCT_TAG_PREFIX = 'ShopwareProduct';

    const CATEGORY_TAG_PREFIX = 'ShopwareCategory';

    /**
     * @var array
     * @Flow\InjectConfiguration
     */
    protected $configuration;

    /**
     * @var ApiLoggerInterface
     * @Flow\Inject
     */
    protected $apiLogger;

    /**
     * @param string $shopIdentifier
     * @param mixed $products
     * @return array tag identifiers
     */
    public function productTags($shopIdentifier, $products)
    {
        if (is_array($products) || $products instanceof \Traversable) {
            $result = [];
            foreach ($products as $product) {
                $tag = $this->productTag($shopIdentifier, $product);
                if ($tag) {
                    $result[] = $tag;
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * @param string $shopIdentifier
     * @param string|Product|ProductReference $data
     * @return string the tagIdentifier
     */
    public function productTag($shopIdentifier, $product)
    {
        if (is_string($product)) {
            return $this->sanitizeTagIdentifier(self::PRODUCT_TAG_PREFIX . '_' . $shopIdentifier . '_' . (string)$product);
        } elseif ($product instanceof Product || $product instanceof ProductReference) {
            return $this->sanitizeTagIdentifier(self::PRODUCT_TAG_PREFIX . '_' . $shopIdentifier . '_' . $product->getId());
        }
    }

    /**
     * @param string $shopIdentifier
     * @param mixed $pro$categoriesucts
     * @return array tag identifiers
     */
    public function categoryTags($shopIdentifier, $categories)
    {
        if (is_array($categories) || $categories instanceof \Traversable) {
            $result = [];
            foreach ($categories as $category) {
                $tag = $this->categoryTag($shopIdentifier, $category);
                if ($tag) {
                    $result[] = $tag;
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * @param string $shopIdentifier
     * @param string|Category|CategoryReference $category
     * @return string the tagIdentifier
     */
    public function categoryTag($shopIdentifier, $category)
    {
        if (is_string($category)) {
            return $this->sanitizeTagIdentifier(self::CATEGORY_TAG_PREFIX . '_' . $shopIdentifier . '_' . (string)$category);
        } elseif ($category instanceof Category || $category instanceof CategoryReference) {
            return $this->sanitizeTagIdentifier(self::CATEGORY_TAG_PREFIX . '_' . $shopIdentifier . '_' . $category->getId());
        }
    }

    /**
     * Replace all characters that are not alphanumeric with underscores
     *
     * @param string $tagIdentifier
     * @return string
     */
    protected function sanitizeTagIdentifier(string $tagIdentifier) {
        return preg_replace('/[^a-zA-Z0-9_-]/u', '_', $tagIdentifier);
    }

    /**
     * @param string $methodName
     * @return bool
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }

}
