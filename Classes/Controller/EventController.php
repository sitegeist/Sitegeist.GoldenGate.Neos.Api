<?php
namespace Sitegeist\GoldenGate\Neos\Api\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Mvc\View\JsonView;

use Sitegeist\Goldengate\Dto\Serializer\ProductSerializer;
use Sitegeist\Goldengate\Dto\Serializer\ProductReferenceSerializer;
use Sitegeist\Goldengate\Dto\Serializer\CategorySerializer;
use Sitegeist\Goldengate\Dto\Serializer\CategoryReferenceSerializer;

use Sitegeist\GoldenGate\Neos\Api\Eel\CachingHelper;
use Sitegeist\GoldenGate\Neos\Api\Service\ConfigurationService;
use Sitegeist\GoldenGate\Neos\Api\Service\CacheService;

class EventController extends ActionController
{

    /**
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * @var ConfigurationService
     * @Flow\Inject
     */
    protected $configurationService;

    /**
     * @var CachingHelper
     * @Flow\Inject
     */
    protected $shopwareCachingHelper;

    /**
     * @var CacheService
     * @Flow\Inject
     */
    protected $cacheService;

    /**
     * Notify about changes for a specific product
     *
     * @param string $shopIdentifier
     * @param string $product
     * @param string $productReference
     */
    public function productAction($shopIdentifier = 'default', $product = null, $productReference = null)
    {
        if (!$this->configurationService->hasShopConfiguration($shopIdentifier)) {
            $this->throwStatus(401, 'No viable input');
        }

        if ($product) {
            $productSerializer = new ProductSerializer();
            $product = $productSerializer->deserialize($product);
            $productTag = $this->shopwareCachingHelper->itemTag($shopIdentifier, $product);
            $this->cacheService->flushCachesByTag($productTag);
        } elseif ($productReference) {
            $productReferenceSerializer = new ProductReferenceSerializer();
            $productReference = $productReferenceSerializer->deserialize($productReference);
            $productTag = $this->shopwareCachingHelper->itemTag($shopIdentifier, $productReference);
            $this->cacheService->flushCachesByTag($productTag);
        } else {
            $this->throwStatus(401, 'No viable input');
        }
        $this->view->assign('value', ['success' => true]);
    }

    /**
     * Notify about changes for a specific category

     * @param string $shopIdentifier
     * @param string $category
     * @param string $categoryReference
     */
    public function categoryAction($shopIdentifier = 'default', $category = null, $categoryReference = null)
    {
        if (!$this->configurationService->hasShopConfiguration($shopIdentifier)) {
            $this->throwStatus(401, 'No viable input');
        }

        if ($category) {
            $categorySerializer = new CategorySerializer();
            $category = $categorySerializer->deserialize($category);
            $categoryTag = $this->shopwareCachingHelper->itemTag($shopIdentifier, $category);
            $this->cacheService->flushCachesByTag($categoryTag);

        } elseif ($categoryReference) {
            $categoryReferenceSerializer = new CategoryReferenceSerializer();
            $categoryReference = $categoryReferenceSerializer->deserialize($categoryReference);
            $categoryTag = $this->shopwareCachingHelper->itemTag($shopIdentifier, $categoryReference);
            $this->cacheService->flushCachesByTag($categoryTag);
        } else {
            $this->throwStatus(401, 'No viable input found');
        }

        $this->view->assign('value', ['success' => true]);
    }



}
