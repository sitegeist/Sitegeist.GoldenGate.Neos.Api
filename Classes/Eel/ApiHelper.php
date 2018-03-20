<?php
namespace Sitegeist\GoldenGate\Neos\Api\Eel;

use Neos\Flow\Annotations as Flow;
use Neos\Eel\ProtectedContextAwareInterface;
use Sitegeist\Goldengate\Dto\Structure\CategoryReference;
use Sitegeist\Goldengate\Dto\Structure\FilterGroupOptionReference;
use Sitegeist\Goldengate\Dto\Structure\Product;
use Sitegeist\Goldengate\Dto\Structure\ProductReference;

use Sitegeist\Goldengate\Dto\Structure\Filter;
use Sitegeist\Goldengate\Dto\Structure\FilterGroup;
use Sitegeist\Goldengate\Dto\Structure\FilterGroupOption;
use Sitegeist\Goldengate\Dto\Structure\FilterGroupReference;

use Sitegeist\Goldengate\Dto\Serializer\ProductSerializer;
use Sitegeist\Goldengate\Dto\Serializer\ProductReferenceSerializer;
use Sitegeist\Goldengate\Dto\Serializer\CategorySerializer;
use Sitegeist\Goldengate\Dto\Serializer\CategoryReferenceSerializer;
use Sitegeist\Goldengate\Dto\Serializer\FilterSerializer;
use Sitegeist\Goldengate\Dto\Serializer\FilterGroupSerializer;
use Sitegeist\Goldengate\Dto\Serializer\FilterGroupReferenceSerializer;

use Sitegeist\GoldenGate\Neos\Api\Exception;
use Sitegeist\GoldenGate\Neos\Api\Service\ApiService;

/**
 * Class ShopwareHelper
 * @package Sitegeist\GoldenGate\Neos\Api\Eel
 */
class ApiHelper implements ProtectedContextAwareInterface
{
    /**
     * @var ApiService
     * @Flow\Inject
     */
    protected $apiService;

    /**
     * @var ProductSerializer
     */
    protected $productSerializer;

    /**
     * @var ProductReferenceSerializer
     */
    protected $productReferenceSerializer;

    /**
     * @var CategorySerializer
     */
    protected $categorySerializer;

    /**
     * @var CategoryReferenceSerializer
     */
    protected $categoryReferenceSerializer;

    /**
     * @var FilterSerializer
     */
    protected $filterSerializer;

    /**
     * @var FilterGroupSerializer
     */
    protected $filterGroupSerializer;

    /**
     * @var FilterGroupReferenceSerializer
     */
    protected $filterGroupReferenceSerializer;

    /**
     * ApiHelper constructor.
     */
    public function __construct()
    {
        $this->productSerializer = new ProductSerializer();
        $this->productReferenceSerializer = new ProductReferenceSerializer();
        $this->categorySerializer = new CategorySerializer();
        $this->categoryReferenceSerializer = new CategoryReferenceSerializer();
        $this->filterSerializer = new FilterSerializer();
        $this->filterGroupSerializer = new FilterGroupSerializer();
        $this->filterGroupReferenceSerializer = new FilterGroupReferenceSerializer();
    }

    /**
     * @param string|ProductReference $productReference
     * @param string $shopIdentifier
     * @return Product
     */
    public function product($shopIdentifier = 'default', $productReference)
    {
        if ($productReference instanceof ProductReference) {
            $id = $productReference->getId();
        } elseif (is_string($productReference)) {
            $id = $productReference;
        } else {
            throw new Exception(sprintf("id-string or ProductReference was expected but %s given" , get_class($productReference)));
        }

        $data = $this->apiService->apiCall($shopIdentifier, 'SitegeistProduct/' . $id);
        $product =  $this->productSerializer->deserialize($data);
        return $product;
    }

    /**
     * @param string $shopIdentifier
     * @param Filter $filter
     * @param float $minPrice
     * @param float $maxPrice
     * @param FilterGroupOptionReference[] $filterGroupOptionReferences
     * @param CategoryReferences[] $categoryReferences
     * @return ProductReference[]
     */
    public function productReferences($shopIdentifier = 'default', $minPrice = null, $maxPrice = null, $filterGroupOptionReferences = [], $categoryReferences = [] )
    {
        $parameters = [];

        if ($minPrice || $maxPrice || $filterGroupOptionReferences) {
            $filter = new Filter();
            $filter->setMinPrice($minPrice);
            $filter->setMaxPrice($maxPrice);
            $filter->setFilterGroupOptionReferences($filterGroupOptionReferences);
            $parameters['filter'] = $this->filterSerializer->serialize($filter);
        }

        if ($categoryReferences) {
            $parameters['categories'] = $this->categoryReferenceSerializer->serializeArray($categoryReferences);
        }

        $jsonData = $this->apiService->apiCall($shopIdentifier, 'SitegeistProduct', $parameters);
        $productReferences = $this->productReferenceSerializer->deserializeArray($jsonData);
        return $productReferences;
    }

    /**
     * @param string|CategoryReference $categoryReference
     * @param string $shopIdentifier
     * @return Product
     */
    public function category($categoryReference, $shopIdentifier = 'default')
    {
        if ($categoryReference instanceof CategoryReference) {
            $id = $categoryReference->getId();
        } elseif (is_string($categoryReference)) {
            $id = $categoryReference;
        } else {
            throw new Exception(sprintf("id-string or CategoryReference was expected but %s given" , get_class($categoryReference)));
        }

        $data = $this->apiService->apiCall($shopIdentifier, 'SitegeistCategory/' . $id);
        $category =  $this->categorySerializer->deserialize($data);
        return $category;
    }

    /**
     * @param string $shopIdentifier
     * @return CategoryReference[]
     */
    public function categoryReferences($shopIdentifier = 'default')
    {
        $data = $this->apiService->apiCall($shopIdentifier, 'SitegeistCategory');
        $categoryReference = $this->categoryReferenceSerializer->deserializeArray($data);
        return $categoryReference;
    }

    /**
     * @param string|FilterGroupReference $filterGroupReference
     * @param string $shopIdentifier
     * @return Product
     */
    public function filterGroup($filterGroupReference, $shopIdentifier = 'default')
    {
        if ($filterGroupReference instanceof FilterGroupReference) {
            $id = $filterGroupReference->getId();
        } elseif (is_string($filterGroupReference)) {
            $id = $filterGroupReference;
        } else {
            throw new Exception(sprintf("id-string or FilterGroupReference was expected but %s given" , get_class($filterGroupReference)));
        }

        $data = $this->apiService->apiCall($shopIdentifier, 'SitegeistFilterGroup/' . $id);
        $category =  $this->filterGroupSerializer->deserialize($data);
        return $category;
    }

    /**
     * @param string $shopIdentifier
     * @return ProductReference[]
     */
    public function filterGroupReferences($shopIdentifier = 'default')
    {
        $jsonData = $this->apiService->apiCall($shopIdentifier, 'SitegeistFilterGroup');
        $filterGroups = $this->filterGroupReferenceSerializer->deserializeArray($jsonData);
        return $filterGroups;
    }

    /**
     * Restrict call of all het methods via eel
     *
     * @param string $methodName
     * @return bool
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }

}
