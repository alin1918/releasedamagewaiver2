<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIgniter\DamageWaiver\Model\Total;

class Damagewaiver extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    protected $helperData;

    /**
     * Collect grand total address amount.
     *
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     *
     * @return $this
     */
    protected $quoteValidator = null;

    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator,
                                \SalesIgniter\DamageWaiver\Helper\Data $helperData)
    {
        $this->quoteValidator = $quoteValidator;
        $this->helperData = $helperData;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }

        $enabled = $this->helperData->isModuleEnabled();

        if ($enabled) {
            $fee = $this->helperData->getDamagewaiverAmount();

            $quote->setDamagewaiver($fee);
            $quote->setBaseDamagewaiver($fee);

            $total->setTotalAmount('damagewaiver', $fee);
            $total->setBaseTotalAmount('base_damagewaiver', $fee);
            $total->setDamagewaiver($fee);
            $total->setBaseDamagewaiver($fee);
            // $total->setGrandTotal($total->getGrandTotal() + $fee);
            //$total->setBaseGrandTotal($total->getBaseGrandTotal() + $fee);
        }

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote               $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $enabled = $this->helperData->isModuleEnabled();

        $fee = $this->helperData->getDamagewaiverAmount();
        if ($enabled && $fee) {
            return [
                'code' => 'damagewaiver',
                'title' => $this->helperData->getDamagewaiverLabel(),
                'value' => $fee,
            ];
        } else {
            return [];
        }
    }

    /**
     * Get Subtotal label.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return $this->helperData->getDamagewaiverLabel();
    }
}
