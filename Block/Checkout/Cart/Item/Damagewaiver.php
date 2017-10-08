<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIgniter\DamageWaiver\Block\Checkout\Cart\Item;

class Damagewaiver extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'SalesIgniter_DamageWaiver::checkout/cart/item/damagewaiver.phtml';
    /**
     * @var array
     */
    protected $jsLayout;

    /**
     * @var \SalesIgniter\DamageWaiver\Block\Checkout\AttributeMerger
     */
    private $merger;
    /**
     * @var \SalesIgniter\Rental\Helper\Data
     */
    private $helperRental;

    /**
     * @param \Magento\Framework\View\Element\Template\Context          $context
     * @param \SalesIgniter\Rental\Helper\Data                          $helperRental
     * @param \SalesIgniter\DamageWaiver\Block\Checkout\AttributeMerger $merger
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \SalesIgniter\Rental\Helper\Data $helperRental,
        \SalesIgniter\DamageWaiver\Block\Checkout\AttributeMerger $merger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->merger = $merger;
        $this->helperRental = $helperRental;
    }

    /**
     * This function is good to update the fields on runtime
     * Might be needed at some point but is very important to know that only
     * component and config are used. For now because we are using the template
     * processor is better to update on there if there are some dynamic fields.
     * Can be used to update some dynamic text or do element specific stuff.
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;

        return $this->helperRental->serialize($jsLayout, true);
    }
}
