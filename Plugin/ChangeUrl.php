<?php

declare(strict_types=1);

namespace OH\Cookie\Plugin;

use Magento\Cms\Helper\Page;
use Magento\Cookie\Block\Html\Notices;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ChangeUrl
 * @package OH\Cookie\Plugin
 */
class ChangeUrl
{
    /**
     * @var string
     */
    const XML_CONFIG_PATH_COOKIE_ENABLED = 'oh_cookie/settings/enabled';

    /**
     * @var string
     */
    const XML_CONFIG_PATH_COOKIE_CMS_IDENTIFIER = 'oh_cookie/settings/page';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $storeConfig;

    /**
     * @var Page
     */
    private Page $cmsHelper;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        Page $cmsHelper,
        StoreManagerInterface $storeManager
    )
    {
        $this->logger = $logger;
        $this->storeConfig = $scopeConfig;
        $this->cmsHelper = $cmsHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * Get cookie policy url
     *
     * @param Notices $notices
     * @param $result
     * @return string|null
     */
    public function afterGetPrivacyPolicyLink(Notices $notices, $result): ?string
    {
        try {
            $currentStoreId = $this->storeManager->getStore()->getId();

            if (!$this->storeConfig->isSetFlag(
                self::XML_CONFIG_PATH_COOKIE_ENABLED,
                ScopeInterface::SCOPE_STORE,
                $currentStoreId)) {
                return $result;
            }

            if ($pageId = $this->storeConfig->getValue(
                self::XML_CONFIG_PATH_COOKIE_CMS_IDENTIFIER,
                ScopeInterface::SCOPE_STORE,
                $currentStoreId
            )
            ) {
                $result = $this->cmsHelper->getPageUrl($pageId);
            }
        } catch (NoSuchEntityException $noSuchEntityException) {
            $this->logger->error(__('Could not found store cookie CMS'));
        }

        return $result;
    }
}
