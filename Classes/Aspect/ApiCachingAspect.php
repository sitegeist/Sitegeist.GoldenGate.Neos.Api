<?php
namespace Sitegeist\GoldenGate\Neos\Api\Aspect;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Cache\Frontend\VariableFrontend;
use Sitegeist\GoldenGate\Neos\Api\Eel\CachingHelper;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class ApiCachingAspect
{
    /**
     * @Flow\Inject
     * @var VariableFrontend
     */
    protected $shopwareApiCache;

    /**
     * @Flow\Inject
     * @var CachingHelper
     */
    protected $shopwareTagHelper;

    /**
     * @Flow\Around("setting(Sitegeist.GoldenGate.Neos.Api.enableCache) && method(Sitegeist\GoldenGate\Neos\Api\Eel\ApiHelper->(getProduct|getCategory)())")
     * @param JoinPointInterface $joinPoint The current join point
     * @return mixed
     */
    public function cacheItemResults(JoinPointInterface $joinPoint)
    {
        $arguments = $joinPoint->getMethodArguments();
        $methodName = $joinPoint->getMethodName();
        $entryIdentifier = $methodName . '_' . md5(serialize($arguments));
        if ($this->shopwareApiCache->has($entryIdentifier)) {
            $result = unserialize($this->shopwareApiCache->get($entryIdentifier));
        } else {
            $result = $joinPoint->getAdviceChain()->proceed($joinPoint);
            $this->shopwareApiCache->set(
                $entryIdentifier,
                serialize($result),
                [$this->shopwareTagHelper->itemTag($result)]
            );
        }
        return $result;
    }

    /**
     * @Flow\Around("setting(Sitegeist.GoldenGate.Neos.Api.enableCache) && method(Sitegeist\GoldenGate\Neos\Api\Eel\ApiHelper->(getProductReferences|getCategoryReferences)())")
     * @param JoinPointInterface $joinPoint The current join point
     * @return mixed
     */
    public function cacheListResults(JoinPointInterface $joinPoint)
    {
        $arguments = $joinPoint->getMethodArguments();
        $methodName = $joinPoint->getMethodName();
        $entryIdentifier = $methodName . '_' . md5(serialize($arguments));
        if ($this->shopwareApiCache->has($entryIdentifier)) {
            $result = unserialize($this->shopwareApiCache->get($entryIdentifier));
        } else {
            $result = $joinPoint->getAdviceChain()->proceed($joinPoint);
            $this->shopwareApiCache->set(
                $entryIdentifier,
                serialize($result),
                $this->shopwareTagHelper->listTag($result)
            );
        }
        return $result;
    }
}