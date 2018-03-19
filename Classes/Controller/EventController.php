<?php
namespace Sitegeist\GoldenGate\Neos\Api\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Mvc\View\JsonView;
use Neos\Cache\Frontend\FrontendInterface;

use Sitegeist\Goldengate\Dto\Serializer\ProductSerializer;
use Sitegeist\Goldengate\Dto\Serializer\ProductReferenceSerializer;
use Sitegeist\Goldengate\Dto\Serializer\CategorySerializer;
use Sitegeist\Goldengate\Dto\Serializer\CategoryReferenceSerializer;

use Sitegeist\GoldenGate\Neos\Api\Eel\CachingHelper;
use Sitegeist\GoldenGate\Neos\Api\Service\ConfigurationService;

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
    protected $shopwareTagHelper;

    /**
     * @var FrontendInterface
     * @Flow\Inject
     */
    protected $shopwareApiCache;

    /**
     * @var FrontendInterface
     * @Flow\Inject
     */
    protected $fusionContentCache;

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
            $productTag = $this->shopwareTagHelper->itemTag($shopIdentifier, $product);
            $this->flushCachesByTag($productTag);
        } elseif ($productReference) {
            $productReferenceSerializer = new ProductReferenceSerializer();
            $productReference = $productReferenceSerializer->deserialize($productReference);
            $productTag = $this->shopwareTagHelper->itemTag($shopIdentifier, $productReference);
            $this->flushCachesByTag($productTag);
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
            $categoryTag = $this->shopwareTagHelper->itemTag($shopIdentifier, $category);
            $this->flushCachesByTag($categoryTag);

        } elseif ($categoryReference) {
            $categoryReferenceSerializer = new CategoryReferenceSerializer();
            $categoryReference = $categoryReferenceSerializer->deserialize($categoryReference);
            $categoryTag = $this->shopwareTagHelper->itemTag($shopIdentifier, $categoryReference);
            $this->flushCachesByTag($categoryTag);
        } else {
            $this->throwStatus(401, 'No viable input found');
        }

        $this->view->assign('value', ['success' => true]);
    }

    /**
     * @param string $tag
     */
    protected function flushCachesByTag($tag)
    {
        $this->fusionContentCache->flushByTag($tag);
        $this->shopwareApiCache->flushByTag($tag);
    }

}
