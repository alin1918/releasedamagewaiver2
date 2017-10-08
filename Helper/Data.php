<?php

namespace SalesIgniter\DamageWaiver\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * Custom damagewaiver config path.
     * Damage waiver amount should be moved here
     * also the rest of configs should be moved
     * pricing should be updated.
     */
    const DAMAGEWAIVER_GENERAL_STATUS = 'damagewaiver/general/status';
    const DAMAGEWAIVER_GENERAL_NAME = 'damagewaiver/general/name';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $_resourceProduct;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @param \Magento\Framework\App\Helper\Context        $context
     * @param \Magento\Store\Model\StoreManagerInterface   $storeManager
     * @param \Magento\Catalog\Model\Session               $catalogSession
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceProduct
     * @param \Magento\Framework\Registry                  $coreRegistry
     * @param \Magento\Framework\App\State                 $appState
     * @param \Magento\Checkout\Model\Session              $checkoutSession
     * @param \Magento\Customer\Model\Session              $customerSession
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\State $appState,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_storeManager = $storeManager;
        $this->_catalogSession = $catalogSession;
        $this->_coreRegistry = $coreRegistry;
        $this->_resourceProduct = $resourceProduct;
        $this->_customerSession = $customerSession;
        $this->_appState = $appState;
        $this->request = $context->getRequest();
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return mixed
     */
    public function isModuleEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isEnabled = $this->scopeConfig->getValue(self::DAMAGEWAIVER_GENERAL_STATUS, $storeScope);

        return $isEnabled;
    }

    /**
     * Get custom damagewaiver.
     *
     * @return mixed
     */
    public function getDamagewaiverAmount()
    {
        $fee = (float) $this->checkoutSession->getQuote()->getDamagewaiver();

        return $fee;
    }

    /**
     * Set custom Damagewaiver.
     *
     * @param $amount
     *
     * @return mixed
     */
    public function setDamagewaiverAmount($amount)
    {
        $this->checkoutSession->getQuote()->setDamagewaiver($amount);
    }

    /**
     * Get custom damagewaiver.
     *
     * @return mixed
     */
    public function getDamagewaiverAmountSerial()
    {
        $damageWaiverAmountArr = [];
        if ($this->checkoutSession->getDamageWaiverAmount()) {
            $damageWaiverAmountArr = unserialize($this->checkoutSession->getDamageWaiverAmount());
        }
        $damageWaiverAmount = isset($damageWaiverAmountArr[$this->checkoutSession->getQuote()->getId()]) ? $damageWaiverAmountArr[$this->checkoutSession->getQuote()->getId()] : 0;
        $fee = (float) $damageWaiverAmount;

        return $fee;
    }

    /**
     * Set custom Damagewaiver.
     *
     * @param $amount
     *
     * @return mixed
     */
    public function setDamagewaiverAmountSerial($amount)
    {
        $damageWaiverAmountArr = [];
        if ($this->checkoutSession->getDamageWaiverAmount()) {
            $damageWaiverAmountArr = unserialize($this->checkoutSession->getDamageWaiverAmount());
        }

        $damageWaiverAmountArr[$this->checkoutSession->getQuote()->getId()] = (float) $amount;
        $this->checkoutSession->setDamageWaiverAmount(serialize($damageWaiverAmountArr));
    }

    /**
     * Returns true if current scope is frontend.
     *
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isFrontendCheckoutCart()
    {
        return $this->_appState->getAreaCode() === 'frontend' &&
            $this->request->getFullActionName() === 'checkout_cart_index';
    }

    /**
     * Get custom damagewaiver.
     *
     * @return mixed
     */
    public function getDamagewaiverLabel()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $feeLabel = $this->scopeConfig->getValue(self::DAMAGEWAIVER_GENERAL_NAME, $storeScope);

        return $feeLabel;
    }
}
