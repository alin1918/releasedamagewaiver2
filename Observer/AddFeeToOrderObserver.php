<?php
namespace SalesIgniter\DamageWaiver\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddFeeToOrderObserver implements ObserverInterface
{
    /**
     * Set payment damagewaiver to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        $CustomFeeFee = $quote->getDamagewaiver();
        $CustomFeeBaseFee = $quote->getBaseDamagewaiver();
        if (!$CustomFeeFee || !$CustomFeeBaseFee) {
            return $this;
        }
        //Set damagewaiver data to order
        $order = $observer->getOrder();
        $order->setData('damagewaiver', $CustomFeeFee);
        $order->setData('base_damagewaiver', $CustomFeeBaseFee);

        return $this;
    }
}
