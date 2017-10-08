<?php

namespace SalesIgniter\DamageWaiver\Plugin\Catalog;

use Magento\Framework\View\Page\Config\Reader\Html;

/**
 * Class Template.
 *
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.OverallComplexity)
 */
class Template
{
    /**
     * @var \SalesIgniter\Rental\Helper\Data
     */
    protected $_helperDamage;
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;
    /**
     * @var \SalesIgniter\Rental\Helper\Data
     */
    private $helperRental;
    /**
     * @var \SalesIgniter\Rental\Helper\Calendar
     */
    private $helperCalendar;

    /**
     * @param \SalesIgniter\DamageWaiver\Helper\Data  $helperDamage
     * @param \SalesIgniter\Rental\Helper\Data        $helperRental
     * @param \SalesIgniter\Rental\Helper\Calendar    $helperCalendar
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Registry             $coreRegistry
     */
    public function __construct(
        \SalesIgniter\DamageWaiver\Helper\Data $helperDamage,
        \SalesIgniter\Rental\Helper\Data $helperRental,
        \SalesIgniter\Rental\Helper\Calendar $helperCalendar,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_helperDamage = $helperDamage;
        $this->layout = $layout;
        $this->coreRegistry = $coreRegistry;
        $this->helperRental = $helperRental;
        $this->helperCalendar = $helperCalendar;
    }

    private function removeHtmlTags($html)
    {
        //$html = preg_replace("/<html[^>]+\>/i", '', $html);
        $html = str_replace('<html>', '', $html);
        $html = str_replace('</html>', '', $html);
        // $html = str_replace('<!DOCTYPE html>', '', $html);
        // $html = str_replace('<br></br>', '<br />', $html);
        return $html;
    }

    /**
     * Retrieve block view from file (template).
     *
     * @param \Magento\Framework\View\Element\Template $subject
     * @param \Closure                                 $proceed
     * @param string                                   $fileName
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundFetchView(
        \Magento\Framework\View\Element\Template $subject,
        \Closure $proceed,
        $fileName
    ) {
        $html = $proceed($fileName);
        if ($this->helperRental->isPaymentResponse()) {
            return $html;
        }
        $domHtml = html5qp('<div>'.$html.'</div>');
        $isChanged = false;
        $originalHtml = $html;
        $originalHtml5 = $domHtml->html();
        $originalHtml5 = substr($originalHtml5, 0, strlen($originalHtml5) - 8);

        /*For removing html is important to use append. Using prepend won't work, needs a better solution of course*/
        if ($subject->getNameInLayout() === 'additional.product.info' && $this->_helperDamage->isFrontendCheckoutCart() && $this->helperRental->isRentalType($subject->getItem()->getProductId())) {
            $quoteItem = $subject->getItem();
            $this->_addDamageWaiver($domHtml, $quoteItem, $isChanged);
        }

        if ($isChanged) {
            $htmlString = $domHtml->html();
            $htmlString = substr($htmlString, 0, strlen($htmlString) - 8);

            $htmlString = str_replace($originalHtml5, '', $htmlString);

            return $originalHtml.$this->removeHtmlTags($htmlString);
        } else {
            return $html;
        }
    }

    /**
     * @param $dom
     * @param $quoteItem
     * @param $isChanged
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _addDamageWaiver(&$dom, $quoteItem, &$isChanged)
    {
        /*
         * The whole xml structure could be done right here but
         * for this exercise I will let it call lock->getJsLayout just to see why can be better.
         */
        //todo move the damage waiver field and global settings into extension so it doesn't need the rental extension. Can be used separate

        $insurancePrice = $this->helperCalendar->getDamageWaiverAmount($quoteItem->getProduct()->getId(), $quoteItem->getPrice());
        if ($insurancePrice > 0) {
            $insurancePriceFormatted = $this->helperCalendar->getDamageWaiverAmount($quoteItem->getProduct()->getId(), $quoteItem->getPrice(), true);
            $initialChecked = false;
            if ($quoteItem->getBuyRequest()->getHasInsurance()) {
                $initialChecked = true;
            }

            /** @var \SalesIgniter\DamageWaiver\Block\Checkout\Cart\Item\Damagewaiver $block */
            $block = $this->layout->createBlock(
                '\SalesIgniter\DamageWaiver\Block\Checkout\Cart\Item\Damagewaiver', '', ['data' => [
                    //'arguments' => [
                    'jsLayout' => [
                        'components' => [
                            'block-insurance' => [
                                'component' => 'SalesIgniter_DamageWaiver/js/view/cart/damagewaiver',
                                'config' => [
                                    'template' => 'SalesIgniter_DamageWaiver/cart/damagewaiveritem',
                                ],
                                'children' => [
                                    'insurance-field' => [
                                        'component' => 'SalesIgniter_DamageWaiver/js/view/cart/elements/damagewaiverswitcher',
                                        'config' => [
                                            'prefer' => 'toggle',
                                            'quoteItemId' => (int) $quoteItem->getId(),
                                            'insurance' => $insurancePrice,
                                            'checked' => $initialChecked,
                                            'toggleLabels' => [
                                                'on' => __('Yes').' (+'.strip_tags($insurancePriceFormatted).')',
                                                'off' => __('No'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    //],
                ],
                ]
            );
            $isChanged = true;
            $dom->append($block->toHtml());
        }
    }
}
