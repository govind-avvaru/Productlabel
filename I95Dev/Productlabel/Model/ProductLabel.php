<?php

declare(strict_types=1);

namespace I95Dev\Productlabel\Model;

use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ProductLabel\Api\Data\ProductLabelInterface;
use Smile\ProductLabel\Model\ImageLabel\FileInfo;
use Smile\ProductLabel\Model\ResourceModel\ProductLabel as ProductLabelResource;

/**
 * Product Label Model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductLabel extends \Smile\ProductLabel\Model\ProductLabel
{
    public const CACHE_TAG = 'smile_productlabel';

    protected StoreManagerInterface $storeManager;

    private ?ImageUploader $imageUploader;

    protected FileInfo $fileInfo;

    /**
     * Media directory object (writable).
     */
    protected WriteInterface $mediaDirectory;

    /**
     * @var string|array|bool
     */
    protected $_cacheTag = self::CACHE_TAG;

//    /**
//     * ProductLabel constructor.
//     *
//     * @param Context $context Context
//     * @param Registry $registry Registry
//     * @param StoreManagerInterface $storeManager Store Manager
//     * @param Filesystem $filesystem FileSystem Helper
//     * @param AbstractResource|null $resource Resource
//     * @param AbstractDb|null $resourceCollection Resource Collection
//     * @param ImageUploader $imageUploader Image uploader
//     * @param array $data Object Data
//     */
//    public function __construct(
//        Context               $context,
//        Registry              $registry,
//        StoreManagerInterface $storeManager,
//        Filesystem            $filesystem,
//        ?AbstractResource     $resource = null,
//        ?AbstractDb           $resourceCollection = null,
//        ?ImageUploader        $imageUploader = null,
//        array                 $data = []
//    ) {
//        $this->storeManager = $storeManager;
//        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
//        $this->imageUploader = $imageUploader;
//        parent::__construct(
//            $context,
//            $registry,
//            $resource,
//            $resourceCollection,
//            $data
//        );
//    }

//    /**
//     * Get field: category_id
//     */
//     public function getCategoryId(): int
//    {
//        return (int) $this->getData(self::CATEGORY_ID);
//    }
//
//    /**
//     * Get field: rule_id_type.
//     */
//    public function getRuleIdType(): bool
//    {
//        return (bool) $this->getData(self::RULE_ID_TYPE);
//    }
//
//    /**
//     * Set product label status
//     *
//     * @param bool $status The product label status
//     */
//    public function setRuleIdType(bool $status): ProductLabelInterface
//    {
//        return $this->setData(self::RULE_ID_TYPE, (bool) $status);
//    }
//
//    /**
//     * Set category_id.
//     *
//     * @param int $value The category_id
//     */
//    public function setCategoryId(int $value): ProductLabelInterface
//    {
//        return $this->setData(self::CATEGORY_ID, $value);
//    }
//
//    /**
//     * Populate from array
//     *
//     * @param array $values Form values
//     */

    public function populateFromArray(array $values): void
    {
        $this->setData(self::IS_ACTIVE, (bool) $values['is_active']);
        $this->setData(self::PRODUCTLABEL_NAME, (string) $values['name']);
//        $this->setData(self::RULE_ID_TYPE, (bool) $values['rule_id_type']);
        $this->setData('rule_id_type', (bool) $values['rule_id_type']);
//        echo ;
//        exit();
        if($this->getData('rule_id_type')==1)
        {
            $this->setData('category_id', (int) $values['category_id']=0);
//            $this->setData(self::CATEGORY_ID, (int) $values['category_id']=0);
            $this->setData(self::ATTRIBUTE_ID, (int)$values['attribute_id']);
            $this->setData(self::OPTION_ID, (int)$values['option_id']);
        }
        else{
//            echo $this->getExtensionAttributes()->getCategoryId();
//            exit();
            $this->setData('category_id', (int) $values['category_id']);
//            $this->setData(self::CATEGORY_ID, (int) $values['category_id']);
            $this->setData(self::ATTRIBUTE_ID, (int)$values['attribute_id']=93);
            $this->setData(self::OPTION_ID, (int)$values['option_id']=rand(1,10));
        }
        $this->setData(self::PRODUCTLABEL_IMAGE, $values['image'][0]['name']);
        $this->setData(self::PRODUCTLABEL_POSITION_CATEGORY_LIST, (string) $values['position_category_list']);
        $this->setData(self::PRODUCTLABEL_POSITION_PRODUCT_VIEW, (string) $values['position_product_view']);
        $this->setData(self::PRODUCTLABEL_DISPLAY_ON, implode(',', $values['display_on']));
        $this->setData(self::PRODUCTLABEL_ALT, (string) $values['alt']);
        $this->setData(self::STORE_ID, implode(',', $values['stores'] ?? $values['store_id']));
    }
    /**
     * Construct.
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ProductLabelResource::class);
    }

    /**
     * Get image uploader
     */
    private function getImageUploader(): ImageUploader
    {
        if ($this->imageUploader === null) {
            // @phpstan-ignore-next-line
            $this->imageUploader = ObjectManager::getInstance()->get(
                \Smile\ProductLabel\ProductLabelImageUpload::class
            );
        }

        return $this->imageUploader;
    }
}
