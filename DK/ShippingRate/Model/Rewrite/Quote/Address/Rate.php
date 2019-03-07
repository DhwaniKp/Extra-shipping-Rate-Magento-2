<?php

namespace DK\ShippingRate\Model\Rewrite\Quote\Address;

use Magento\Framework\Model\AbstractModel;

class Rate extends \Magento\Quote\Model\Quote\Address\Rate
{
	protected $_checkoutSession;

	public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_checkoutSession = $checkoutSession;
    }
    
    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

	public function importShippingRate(\Magento\Quote\Model\Quote\Address\RateResult\AbstractResult $rate)
    {
    	if ($rate instanceof \Magento\Quote\Model\Quote\Address\RateResult\Error) {
            $this->setCode(
                $rate->getCarrier() . '_error'
            )->setCarrier(
                $rate->getCarrier()
            )->setCarrierTitle(
                $rate->getCarrierTitle()
            )->setErrorMessage(
                $rate->getErrorMessage()
            );
        } elseif ($rate instanceof \Magento\Quote\Model\Quote\Address\RateResult\Method) {

	     $quote = $this->getCheckoutSession()->getQuote();
	     $items = $quote->getAllItems();

	     $finalQty = 0;
	     foreach($items as $item) {
		$finalQty = $finalQty + $item->getQty();
	     }
	     $finalQty = $finalQty - 1;

	     if($finalQty > 0) {
		$finalPrice = $rate->getPrice() + ($finalQty * 5); // Add custom shipping price here 
	     } else {
		$finalPrice = $rate->getPrice();
	     }

	    $this->setCode(
		$rate->getCarrier() . '_' . $rate->getMethod()
	    )->setCarrier(
		$rate->getCarrier()
	    )->setCarrierTitle(
		$rate->getCarrierTitle()
	    )->setMethod(
		$rate->getMethod()
	    )->setMethodTitle(
		$rate->getMethodTitle()
	    )->setMethodDescription(
		$rate->getMethodDescription()
	    )->setPrice(
		$finalPrice
	    );
        }
        return $this;
    }
}
