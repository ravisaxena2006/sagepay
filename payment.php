<?php 
session_start();
//http://www.sagepay.co.uk/support/12/36/test-card-details-for-your-test-transactions
include("lib/SagePay.class.php");
$sagepay = new SagePay('haris-ven', 'simulator');

if (SagePay::is3dResponse()) {
	$sagepay = SagePay::recover3d();
	$sagepay->complete3d();
	// recover persistent data - see below
	$basket = $sagepay->basket;
}
elseif (true) {
	
	$sagepay->VendorTxCode = time();
	$sagepay->Amount       = 10;
	$sagepay->Currency     = 'GBP';
	$sagepay->Description  = 'Basket Contents';

	$sagepay->CardHolder   = 'Mr John Doe';
	$sagepay->CardNumber   = '4917300000000008';
	$sagepay->ExpiryDate = '0115';
	$sagepay->CV2 = '123';
	$sagepay->CardType = 'VISA';
	$sagepay->BillingSurname = 'Richard';
	$sagepay->BillingFirstnames = 'Richard';
	$sagepay->BillingAddress1 = 'test address';
	$sagepay->BillingCity = 'new york';
	$sagepay->BillingState = 'NY';
	$sagepay->BillingPostCode = 'NYC';
	$sagepay->BillingCountry = 'US';
	
	$sagepay->DeliverySurname = 'Richard';
	$sagepay->DeliveryFirstnames = 'Richard';
	$sagepay->DeliveryAddress1 = 'test address';
	$sagepay->DeliveryCity = 'new york';
	$sagepay->DeliveryPostCode = 'NYC';
	$sagepay->DeliveryState = 'NY';
	$sagepay->DeliveryCountry = 'US';
	/*foreach ($basket_contents as $line_item) {
		$sagepay->addLine($line_item['name'], $line_item['quantity'], $line_item['value'], $line_item['tax']);
	}*/
	
	$sagepay->addLine("test", 1, 10, 0);
	
	$sagepay->register();
	
}
	
if ($sagepay->status() == 'OK') {
	// The order has been completed.
	echo "Payment complete. See details:\n";
	print_r($sagepay->result);
}

elseif ($sagepay->status() == '3DAUTH') {
	// Any data that needs to persist through 3DAUTH can be added to the object
	$sagepay->basket = array("test", 1, 10, 0);
	
	// POST to ACSURL - usually by outputting a auto-submitting form
	?>
	<form action="<?=$sagepay->result['ACSURL']?>" method="post">
		<input type="hidden" name="PaReq" value="<?=$sagepay->result['PAReq']?>" />
		<input type="hidden" name="MD" value="<?=$sagepay->result['MD']?>" />
		<input type="hidden" name="TermUrl" value="http://yourdomain.com/sagepay/payment.php" />
		<input type="submit" value="Continue to Card Verification" />
	</form>
	<?php
}

else {
	echo "Errors:\n";
	print_r($sagepay->result);
}