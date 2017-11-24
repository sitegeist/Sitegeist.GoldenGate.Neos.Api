<?php
namespace Sitegeist\GoldenGate\Neos\Api\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Request;
use Neos\Flow\Http\Uri;
use Neos\Flow\Http\Client\Browser;
use Neos\Flow\Http\Client\CurlEngine;
use Sitegeist\GoldenGate\Neos\Api\Exception;

/**
 * @Flow\Scope("singleton")
 */
class ApiService
{

    /**
     * @var ConfigurationService
     * @Flow\Inject
     */
    protected $configurationService;

    /**
     * Request data from the shopware api
     *
     * @param string $shopIdentifier
     * @param string $path
     * @param array $parameters
     */
    public function apiCall($shopIdentifier, $path, $parameters = [])
    {
        $shopConfiguration = $this->configurationService->getShopConfiguration($shopIdentifier);

        $engine = new CurlEngine();
        $engine->setOption(CURLOPT_TIMEOUT, 120);
        $engine->setOption(CURLOPT_CONNECTTIMEOUT, 3);

        $browser = new Browser();
        $browser->setRequestEngine($engine);

        $baseUrl = $shopConfiguration['protocol'] . '://' .  $shopConfiguration['host'];
        $baseUrl .= $shopConfiguration['port'] ? ':' . $shopConfiguration['port'] : '';
        $baseUrl .= $shopConfiguration['base'] ? $shopConfiguration['base'] . 'api/' : '/api/';

        $uri = new Uri( $baseUrl . $path . ($parameters ? '?' . http_build_query($parameters) : ''));

        $request = Request::create($uri, 'GET');
        $request->setHeader(
            'Authorization',
            'Basic ' . base64_encode($shopConfiguration['user'] . ':' . $shopConfiguration['password'])
        );

        $response = $this->browser->sendRequest($request);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300 ){
            $data = json_decode($response->getContent(), true);
        } else {
            $data = null;
        }

        return $data;
    }
}
