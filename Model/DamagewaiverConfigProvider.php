<?php

namespace SalesIgniter\DamageWaiver\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DamagewaiverConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \SalesIgniter\DamageWaiver\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \SalesIgniter\DamageWaiver\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\Session        $checkoutSession
     * @param \Psr\Log\LoggerInterface               $logger
     */
    public function __construct(
        \SalesIgniter\DamageWaiver\Helper\Data $dataHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger

    ) {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $damageWaiverConfig = [];
        $damageWaiverConfig['damagewaiver_label'] = $this->dataHelper->getDamagewaiverLabel();
        $damageWaiverConfig['damagewaiver_amount'] = $this->dataHelper->getDamagewaiverAmount();
        $damageWaiverConfig['show_hide_damagewaiver_block'] = true;
        $damageWaiverConfig['show_hide_damagewaiver_shipblock'] = false;

        return $damageWaiverConfig;
    }
}
