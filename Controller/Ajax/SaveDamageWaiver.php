<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIgniter\DamageWaiver\Controller\Ajax;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveDamageWaiver extends \Magento\Framework\App\Action\Action
{
    /**
     * Catalog data.
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * @var \SalesIgniter\Rental\Helper\Calendar
     */
    private $calendarHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productModelFactory;

    /**
     * Registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \SalesIgniter\Rental\Helper\Product
     */
    private $productHelper;
    /**
     * @var \Magento\Catalog\Model\Session
     */
    private $catalogSession;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $pageFactory;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Magento\Quote\Api\CartItemRepositoryInterface
     */
    private $cartItemRepository;
    /**
     * @var \SalesIgniter\DamageWaiver\Controller\Ajax\CartItemOptionsProcessor
     */
    private $cartItemOptionsProcessor;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;
    /**
     * @var \SalesIgniter\DamageWaiver\Helper\Data
     */
    private $helperData;
    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * Price constructor.
     *
     * @param \Magento\Framework\App\Action\Context                                                                                        $context
     * @param \Magento\Catalog\Model\ProductFactory                                                                                        $productModelFactory
     * @param \Magento\Framework\Registry                                                                                                  $registry
     * @param \Magento\Catalog\Helper\Data                                                                                                 $catalogData
     * @param \Magento\Store\Model\StoreManagerInterface                                                                                   $storeManager
     * @param \Magento\Checkout\Model\Session                                                                                              $checkoutSession
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface                                                                            $priceCurrency
     * @param \Magento\Quote\Api\CartItemRepositoryInterface                                                                               $cartItemRepository
     * @param \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor|\SalesIgniter\DamageWaiver\Controller\Ajax\CartItemOptionsProcessor $cartItemOptionsProcessor
     * @param \Magento\Customer\Model\Session                                                                                              $customerSession
     * @param \SalesIgniter\Rental\Helper\Calendar                                                                                         $calendarHelper
     * @param \SalesIgniter\DamageWaiver\Helper\Data                                                                                       $helperData
     * @param \Magento\Quote\Model\Quote                                                                                                   $quote
     * @param \Magento\Framework\View\Result\PageFactory                                                                                   $pageFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface                                                                                   $cartRepository
     * @param \SalesIgniter\Rental\Helper\Product                                                                                          $productHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                                                                              $productRepository
     * @param \Magento\Catalog\Model\Session                                                                                               $catalogSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productModelFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        PriceCurrencyInterface $priceCurrency,
        CartItemRepositoryInterface $cartItemRepository,
        CartItemOptionsProcessor $cartItemOptionsProcessor,
        \Magento\Customer\Model\Session $customerSession,
        \SalesIgniter\Rental\Helper\Calendar $calendarHelper,
        \SalesIgniter\DamageWaiver\Helper\Data $helperData,
        \Magento\Quote\Model\Quote $quote,
        PageFactory $pageFactory,
        CartRepositoryInterface $cartRepository,
        \SalesIgniter\Rental\Helper\Product $productHelper,
        ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Session $catalogSession
    ) {
        $this->_catalogData = $catalogData;
        $this->_storeManager = $storeManager;
        $this->registry = $registry;
        $this->productModelFactory = $productModelFactory;
        parent::__construct($context);
        $this->calendarHelper = $calendarHelper;
        $this->productHelper = $productHelper;
        $this->catalogSession = $catalogSession;
        $this->pageFactory = $pageFactory;
        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->cartItemRepository = $cartItemRepository;
        $this->cartItemOptionsProcessor = $cartItemOptionsProcessor;
        $this->priceCurrency = $priceCurrency;
        $this->helperData = $helperData;
        $this->quote = $quote;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @return \Magento\Framework\Pricing\Render
     */
    protected function getPriceRender()
    {
        $resultPage = $this->pageFactory->create();

        return $resultPage->getLayout()->getBlock('product.price.render.default');
    }

    /**
     * @param int   $itemId
     * @param float $insurance
     * @param bool  $isRemoved
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function saveItemWithInsurance($itemId, $insurance, $isRemoved)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();

        try {
            /* Update existing item */
            $currentItem = $quote->getItemById($itemId);
            if ($currentItem) {
                $currentDamageWaiver = $this->helperData->getDamagewaiverAmount();

                $buyRequest = $currentItem->getBuyRequest();
                $itemOptions = $buyRequest->getOptions();
                $currentProduct = $currentItem->getProduct();
                foreach ($currentProduct->getOptions() as $option) {
                    if ($option->getTitle() === 'Damage Waiver:') {
                        $optionDamageWaiverId = $option->getId();
                        break;
                    }
                }
                if ($isRemoved === false) {
                    $itemOptions[$optionDamageWaiverId] = __('Yes').'(+'.strip_tags($this->priceCurrency->format($insurance)).')';
                    $currentDamageWaiver += $insurance;
                    $buyRequest->setHasInsurance(true);
                } else {
                    $itemOptions[$optionDamageWaiverId] = '';
                    $currentDamageWaiver -= $insurance;
                    $buyRequest->setHasInsurance(false);
                }
                if (is_object($buyRequest)) {
                    $buyRequest->setOptions($itemOptions);

                    $this->helperData->setDamagewaiverAmount($currentDamageWaiver);
                    /* Update item product options */
                    $this->registry->register('sirent_quote_id_frontend', $itemId);
                    $item = $quote->updateItem($itemId, $buyRequest);


                    $this->cartRepository->save($quote->collectTotals());
                    $itemId = $item->getId();
                    //$this->cartItemOptionsProcessor->addProductOptions($productType, $item);
                    //return $this->cartItemOptionsProcessor->applyCustomOptions($item);
                }
            }
        } catch (NoSuchEntityException $e) {
            throw $e;
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save quote').$e->getMessage());
        }
        $this->registry->unregister('sirent_quote_id_frontend');

        return $itemId;
        //$itemId = $item->getId();
        //foreach ($quote->getAllItems() as $quoteItem) {
        /* @var \Magento\Quote\Model\Quote\Item $quoteItem */
        //if ($itemId == $quoteItem->getId()) {
        //$item = $this->cartItemOptionsProcessor->addProductOptions($productType, $quoteItem);
        //return $this->cartItemOptionsProcessor->applyCustomOptions($item);
        //}
        //}
    }

    /**
     * Update Listing Prices.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \InvalidArgumentException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $params = json_decode($this->getRequest()->getContent(), true);
        $cartItemId = (int) $params['quote_item'];
        $isRemoved = (bool) $params['is_removed'];
        $insurance = (float) $params['insurance'];

        //$cartItems = $this->cartItemRepository->getList($this->checkoutSession->getQuoteId());
        $itemId = $cartItemId;
        if ($cartItemId) {
            $itemId = $this->saveItemWithInsurance($cartItemId, $insurance, $isRemoved);
        }
        $responseContent = [
            'quoteItemId' => $itemId,
        ];

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
