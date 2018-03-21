<?php
namespace Sitegeist\GoldenGate\Neos\Api\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Cache\Frontend\FrontendInterface;
use Sitegeist\GoldenGate\Neos\Api\Log\ApiLoggerInterface;

/**
 * @Flow\Scope("singleton")
 */
class CacheService
{

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
     * @var ApiLoggerInterface
     * @Flow\Inject
     */
    protected $apiLogger;

    /**
     * @param string $tag
     */
    public function flushCachesByTag($tag)
    {
        $this->fusionContentCache->flushByTag($tag);
        $this->shopwareApiCache->flushByTag($tag);
        $this->apiLogger->log(sprintf('Invalidated cache tag "%s"', $tag));
    }

}
