<?php
namespace Sitegeist\GoldenGate\Neos\Api\Eel;

use Neos\Flow\Annotations as Flow;
use Neos\Eel\ProtectedContextAwareInterface;
use Sitegeist\Goldengate\Dto\Structure\CategoryReference;
use Sitegeist\Goldengate\Dto\Structure\Product;
use Sitegeist\Goldengate\Dto\Structure\ProductReference;
use Sitegeist\Goldengate\Dto\Serializer\ProductSerializer;
use Sitegeist\Goldengate\Dto\Serializer\ProductReferenceSerializer;
use Sitegeist\Goldengate\Dto\Serializer\CategorySerializer;
use Sitegeist\Goldengate\Dto\Serializer\CategoryReferenceSerializer;

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
     * ApiHelper constructor.
     */
    public function __construct()
    {
        $this->productSerializer = new ProductSerializer();
        $this->productReferenceSerializer = new ProductReferenceSerializer();
        $this->categorySerializer = new CategorySerializer();
        $this->categoryReferenceSerializer = new CategoryReferenceSerializer();
    }

    /**
     * @param string|ProductReference $productReference
     * @param string $shopIdentifier
     * @return Product
     */
    public function product($productReference, $shopIdentifier = 'default')
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
     * @param CategoryReferences[] $categoryReferences
     * @return ProductReference[]
     */
    public function productReferences($shopIdentifier = 'default', $filter = null, $categoryReferences = [] )
    {
        $jsonData = $this->apiService->apiCall($shopIdentifier, 'SitegeistProduct');
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

        $data = $this->apiService->apiCall($shopIdentifier, 'SitegeistProductCategory/' . $id);
        $category =  $this->categorySerializer->deserialize($data);
        return $category;
    }

    /**
     * @param string $shopIdentifier
     * @return CategoryReference[]
     */
    public function categoryReferences($shopIdentifier = 'default')
    {
        $data = $this->apiService->apiCall($shopIdentifier, 'SitegeistProductCategory');
        $categoryReference = $this->categoryReferenceSerializer->deserializeArray($data);
        return $categoryReference;
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
