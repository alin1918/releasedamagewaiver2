<?php

namespace SalesIgniter\DamageWaiver\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Damagewaiver extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getDamagewaiverInvoiced() > 0) {
            $DamagewaiverLeft = $order->getDamagewaiverInvoiced() - $order->getDamagewaiverRefunded();
            $baseDamagewaiverLeft = $order->getBaseDamagewaiverInvoiced() - $order->getBaseDamagewaiverRefunded();
            if ($baseDamagewaiverLeft > 0) {
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $DamagewaiverLeft);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseDamagewaiverLeft);
                $creditmemo->setDamagewaiver($DamagewaiverLeft);
                $creditmemo->setBaseDamagewaiver($baseDamagewaiverLeft);
            }
        } else {
            $Damagewaiver = $order->getDamagewaiverInvoiced();
            $baseDamagewaiver = $order->getBaseDamagewaiverInvoiced();
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $Damagewaiver);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseDamagewaiver);
            $creditmemo->setDamagewaiver($Damagewaiver);
            $creditmemo->setBaseDamagewaiver($baseDamagewaiver);
        }

        return $this;
    }
}
