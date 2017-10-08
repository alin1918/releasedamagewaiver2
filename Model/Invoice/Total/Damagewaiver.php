<?php

namespace SalesIgniter\DamageWaiver\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Damagewaiver extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setDamagewaiver(0);
        $invoice->setBaseDamagewaiver(0);

        $amount = $invoice->getOrder()->getDamagewaiver();
        $invoice->setDamagewaiver($amount);
        $amount = $invoice->getOrder()->getBaseDamagewaiver();
        $invoice->setBaseDamagewaiver($amount);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getDamagewaiver());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getDamagewaiver());

        return $this;
    }
}
