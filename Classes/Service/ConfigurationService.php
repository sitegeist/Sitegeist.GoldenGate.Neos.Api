<?php
/**
 * Created by PhpStorm.
 * User: MFI
 * Date: 24.11.17
 * Time: 11:45
 */

namespace Sitegeist\GoldenGate\Neos\Api\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;
use Sitegeist\GoldenGate\Neos\Api\Exception;

class ConfigurationService
{
    /**
     * @var array
     * @Flow\InjectConfiguration
     */
    protected $configuration;

    /**
     * @return mixed
     */
    public function getAllShopConfigurations() {
        return Arrays::getValueByPath($this->configuration, 'shops');
    }

    /**
     * @param $shopIdentifier
     */
    public function hasShopConfiguration($shopIdentifier = 'default') {
        return (Arrays::getValueByPath($this->configuration, ['shops', $shopIdentifier] ) ? true : false);
    }

    /**
     * @param $shopIdentifier
     */
    public function getShopConfiguration($shopIdentifier = 'default') {
        $shopConfigurationuration = Arrays::getValueByPath($this->configuration, ['shops', $shopIdentifier]);

        if (!$shopConfigurationuration) {
            throw new Exception(sprintf('No connection for shop-identifier "%s" found.', $shopIdentifier));
        }
        return $shopConfigurationuration;
    }
}
