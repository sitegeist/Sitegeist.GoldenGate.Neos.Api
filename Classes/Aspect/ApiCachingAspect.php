<?php
namespace Sitegeist\GoldenGate\Neos\Api\Aspect;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Cache\Frontend\StringFrontend;
use Sitegeist\Goldengate\Dto\Structure\Category;
use Sitegeist\Goldengate\Dto\Structure\CategoryReference;
use Sitegeist\Goldengate\Dto\Structure\ProductReference;
use Sitegeist\GoldenGate\Neos\Api\Eel\CachingHelper;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class ApiCachingAspect
{
    /**
     * @Flow\Inject
     * @var StringFrontend
     */
    protected $shopwareApiCache;

    /**
     * @Flow\Inject
     * @var CachingHelper
     */
    protected $shopwareTagHelper;

    /**
     * @Flow\Around("setting(Sitegeist.GoldenGate.Neos.Api.enableCache) && method(Sitegeist\GoldenGate\Neos\Api\Eel\ApiHelper->(product|category)())")
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
            $tags = [];
            if ($result instanceof ProductReference || $result instanceof Product) {
                $tags = [$this->shopwareTagHelper->productTag($arguments['shopIdentifier'], $result)];
            } elseif ($result instanceof CategoryReference || $result instanceof Category) {
                $tags = [$this->shopwareTagHelper->categoryTag($arguments['shopIdentifier'], $result)];
            }
            $this->shopwareApiCache->set(
                $entryIdentifier,
                serialize($result),
                $tags
            );
        }
        return $result;
    }

    /**
     * @Flow\Around("setting(Sitegeist.GoldenGate.Neos.Api.enableCache) && method(Sitegeist\GoldenGate\Neos\Api\Eel\ApiHelper->(productReferences|categoryReferences)())")
     * @param JoinPointInterface $joinPoint The current join point
     * @return mixed
     */
    public function cacheListResults(JoinPointInterface $joinPoint)
    {
        $arguments = $joinPoint->getMethodArguments();
        $methodName = $joinPoint->getMethodName();
        $entryIdentifier = $methodName . '_' . md5(serialize($arguments));
        if ($this->shopwareApiCache->has($entryIdentifier)) {
            $results = unserialize($this->shopwareApiCache->get($entryIdentifier));
        } else {
            $results = $joinPoint->getAdviceChain()->proceed($joinPoint);
            $tags = [];
            foreach ($results as $result) {
                if ($result instanceof ProductReference || $result instanceof Product) {
                    $tags[] = $this->shopwareTagHelper->productTag($arguments['shopIdentifier'], $result);
                } elseif ($result instanceof CategoryReference || $result instanceof Category) {
                    $tags[] = $this->shopwareTagHelper->categoryTag($arguments['shopIdentifier'], $result);
                }
            }
            $this->shopwareApiCache->set(
                $entryIdentifier,
                serialize($results),
                $tags
            );
        }
        return $results;
    }

    /**
     * @Flow\Around("setting(Sitegeist.GoldenGate.Neos.Api.enableCache) && method(Sitegeist\GoldenGate\Neos\Api\Eel\ApiHelper->(filterGroup|filterGroupReferences)())")
     * @param JoinPointInterface $joinPoint The current join point
     * @return mixed
     */
    public function cacheFilterResults(JoinPointInterface $joinPoint)
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
                [],
                86400
            );
        }
        return $result;
    }
}
