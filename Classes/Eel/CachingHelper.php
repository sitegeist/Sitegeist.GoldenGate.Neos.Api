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
     * @param Structure $structure
     * @return string the tagIdentifier
     */
    public function itemTag($shopIdentifier, Structure $structure)
    {
        switch (true) {
            case $structure instanceof Product:
            case $structure instanceof ProductReference:
                return $this->sanitzeTagIdentifier(self::PRODUCT_TAG_PREFIX . '_' . $shopIdentifier . '_' . $structure->getId());
                break;
            case $structure instanceof Category:
            case $structure instanceof CategoryReference:
                return $this->sanitzeTagIdentifier(self::CATEGORY_TAG_PREFIX . '_' . $shopIdentifier . '_' . $structure->getId());
                break;
            default:
                throw new \Sitegeist\GoldenGate\Neos\Api\Exception(sprintf("Could not create tag for structure-item of type %s", get_class($structure)));
        }
    }

    /**
     * @param string $shopIdentifier
     * @param Structure[] $structures
     * @return string the tagIdentifier
     */
    public function listTag($shopIdentifier, $structures)
    {
        return array_map(
            function($structure) use ($shopIdentifier) {
                return $this->itemTag($shopIdentifier, $structure);
            },
            $structures
        );
    }

    /**
     * @param string $shopIdentifier
     * @param mixed $data
     * @return string the tagIdentifier
     */
    public function productTag($shopIdentifier, $data)
    {
        if (is_string($data)) {
            return [$this->sanitzeTagIdentifier(self::PRODUCT_TAG_PREFIX . '_' . $shopIdentifier . '_' . (string)$data)];
        } elseif ($data instanceof Product || $data instanceof ProductReference) {
            return [$this->sanitzeTagIdentifier(self::PRODUCT_TAG_PREFIX . '_' . $shopIdentifier . '_' . $data->getId())];
        } elseif (is_array($data) || $data instanceof \Traversable) {
            $subresults = array_map(
                function($item) use ($shopIdentifier) {
                    return $this->productTag($shopIdentifier, $item);
                },
                $data
            );
            return array_reduce(
                $subresults,
                function($carry, $item) {
                    foreach ($item as $part){
                        $carry[] = $part;
                    }
                },
                []
            );
        }
        return [];
    }

    /**
     * @param string $shopIdentifier
     * @param mixed $data
     * @return string the tagIdentifier
     */
    public function categoryTag($shopIdentifier, $data)
    {
        if (is_string($data)) {
            return [$this->sanitzeTagIdentifier(self::CATEGORY_TAG_PREFIX . '_' . $shopIdentifier . '_' . (string)$data)];
        } elseif ($data instanceof Category || $data instanceof CategoryReference) {
            return [$this->sanitzeTagIdentifier(self::CATEGORY_TAG_PREFIX . '_' . $shopIdentifier . '_' . $data->getId())];
        } elseif (is_array($data) || $data instanceof \Traversable) {
            $subresults = array_map(
                function($item) use ($shopIdentifier) {
                    return $this->categoryTag($shopIdentifier, $item);
                },
                $data
            );
            return array_reduce(
                $subresults,
                function($carry, $item) {
                    foreach ($item as $part){
                        $carry[] = $part;
                    }
                },
                []
            );
        }
        return [];
    }

    /**
     * Replace all characters that are not alphanumeric with underscores
     *
     * @param string $tagIdentifier
     * @return string
     */
    protected function sanitzeTagIdentifier(string $tagIdentifier) {
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
