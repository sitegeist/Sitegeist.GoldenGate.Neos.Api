<?php
namespace Sitegeist\GoldenGate\Neos\Api\Controller;

use Neos\Cache\Frontend\FrontendInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Mvc\View\JsonView;

use Sitegeist\GoldenGate\Dto\Serializer\ProductReferenceSerializer;
use Sitegeist\GoldenGate\Dto\Serializer\CategoryReferenceSerializer;

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

    protected function __construct()
    {
        $this->productReferenceSerializer = new ProductReferenceSerializer();
        $this->categoryReferenceSerializer = new CategoryReferenceSerializer();
    }

    /**
     * Notify about changes for a specific product
     *
     * @param string $product
     * @param string $apiToken
     * @param string $shopIdentifier
     */
    public function productAction($product, $shopIdentifier = 'default') {
        $productReference = $this->productSerializer->deserialize($product);
        $this->flushCachesByTag($this->shopwareTagHelper->itemTag($shopIdentifier, $productReference));
        $this->view->assign('value', ['success' => true]);
    }

    /**
     * Notify about changes for a specific category
     *
     * @param string $category
     * @param string $apiToken
     * @param string $shopIdentifier
     */
    public function categoryAction($category, $shopIdentifier = 'default') {
        $categoryReference = $this->categoryReferenceSerializer->deserialize($category);
        $this->flushCachesByTag($this->shopwareTagHelper->itemTag($shopIdentifier, $categoryReference));
        $this->view->assign('value', ['success' => true]);
    }

    /**
     * @param string $tag
     */
    protected function flushCachesByTag($tag) {
        $this->fusionContentCache->flushByTag($tag);
        $this->shopwareApiCache->flushByTag($tag);
    }

}
