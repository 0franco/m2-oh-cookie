<?php

declare(strict_types=1);

namespace OH\Cookie\Model\Source;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Pages
 * @package OH\Cookie\Model\Source
 */
class Pages implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $cmsCollectionFactory;

    /**
     * @var array
     */
    private $options;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->cmsCollectionFactory = $collectionFactory;
    }

    /**
     * Retrieve cms options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $allPages = $this->cmsCollectionFactory->create();

            foreach ($allPages as $page) {
                $this->options[$page->getId()] = $page->getTitle();
            }
        }

        return $this->options;
    }
}
