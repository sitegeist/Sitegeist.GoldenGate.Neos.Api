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
     * @param string|ProductReference $productReference
     * @param string $shopIdentifier
     * @return Product
     */
    public function getProduct($productReference, $shopIdentifier = 'default')
    {
        if ($productReference instanceof ProductReference) {
            $id = $productReference->getId();
        } elseif (is_string($productReference)) {
            $id = $productReference;
        } else {
            throw new Exception(sprintf("id-string or ProductReference was expected but %s given" , get_class($productReference)));
        }

        $data = $this->apiService->apiCall($shopIdentifier, 'product/' . $id);
        $product =  $this->productSerializer->deserialize($data);
        return $product;
    }

    /**
     * @param string $shopIdentifier
     * @param Filter $filter
     * @param CategoryReferences[] $categoryReferences
     * @return ProductReference[]
     */
    public function getProductReferences($shopIdentifier = 'default', $filter = null, $categoryReferences = [] )
    {
        $data = $this->apiService->apiCall($shopIdentifier, 'product');
        $productReferences = $this->productReferenceSerializer->deserializeArray($data);
        return $productReferences;
    }

    /**
     * @param string|CategoryReference $categoryReference
     * @param string $shopIdentifier
     * @return Product
     */
    public function getCategory($categoryReference, $shopIdentifier = 'default')
    {
        if ($categoryReference instanceof CategoryReference) {
            $id = $categoryReference->getId();
        } elseif (is_string($categoryReference)) {
            $id = $categoryReference;
        } else {
            throw new Exception(sprintf("id-string or CategoryReference was expected but %s given" , get_class($categoryReference)));
        }

        $data = $this->apiService->apiCall($shopIdentifier, 'productCategory/' . $id);
        $category =  $this->categorySerializer->deserialize($data);
        return $category;
    }

    /**
     * @param string $shopIdentifier
     * @return CategoryReference[]
     */
    public function getCategoryReferences($shopIdentifier = 'default')
    {
        $data = $this->apiService->apiCall($shopIdentifier, 'productCategory');
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
        return false;
    }

}
