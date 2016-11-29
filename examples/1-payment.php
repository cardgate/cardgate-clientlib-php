<?php
try {

	include 'init.php';

	$oTransaction = $oCardGate->transactions()->create( $iSiteId, 660596, 'EUR' );

	// Configure payment option.
	if ( ! empty( $_POST['option'] ) ) {
		$oTransaction->setPaymentMethod( $oCardGate->methods()->get( $_POST['option'] ) );

		// Configure
		if ( 'ideal' == $_POST['option'] ) {
			if ( empty( $_POST['issuer'] ) ) {
				header( 'Location: 2-payment-ideal.php' );
				exit;
			} else {
				$oTransaction->setIssuer( $_POST['issuer'] );
			}
		}
	} else {
		$oTransaction->setPaymentMethod( cardgate\api\Method::IDEAL );
	}

	// Configure customer.
	$oCustomer = $oTransaction->getCustomer();
	$oCustomer->address()->setFirstName( 'John' );
	$oCustomer->address()->setInitials( 'J.A.N.' );
	$oCustomer->address()->setLastName( 'Doe' );
	$oCustomer->address()->setAddress( 'Test Avenue 33' );
	$oCustomer->address()->setZipCode( '34342' );
	$oCustomer->address()->setCity( 'Minneapolis' );
	$oCustomer->address()->setCountry( 'US' );
	$oCustomer->shippingAddress()->setFirstName( 'Judy' );
	$oCustomer->shippingAddress()->setInitials( 'J.' );
	$oCustomer->shippingAddress()->setLastName( 'Doe' );
	$oCustomer->shippingAddress()->setAddress( 'Trialstreet 1334' );
	$oCustomer->shippingAddress()->setZipCode( '77377' );
	$oCustomer->shippingAddress()->setCity( 'Chicago' );
	$oCustomer->shippingAddress()->setCountry( 'US' );

	// Configure cart.
	$oCart = $oTransaction->getCart();
	$oItem = $oCart->addItem( \cardgate\api\Item::TYPE_PRODUCT, 'AA21484', 'iMac 27"', 3, 219999, 'http://www.apple.com/imac/' );
	$oItem = $oCart->addItem( \cardgate\api\Item::TYPE_SHIPPING, 'SHIPPING', 'Shipping by UPS', 1, 599 );

	// Create unique order id with corresponding database file.
	$sOrderFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cardgate_order_' . time();
	if ( ! is_writable( dirname( $sOrderFile ) ) ) {
		die( 'unable to create order file' );
	}
	$sOrderId = basename( $sOrderFile );

	// Configure communication endpoint locations.
	$sProtocol = isset( $_SERVER['HTTPS'] ) && strcasecmp( 'off', $_SERVER['HTTPS'] ) !== 0 ? 'https' : 'http';
	$sHostname = $_SERVER['HTTP_HOST'];
	$sPath = dirname( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'] );
	$oTransaction->setCallbackUrl( "{$sProtocol}://{$sHostname}{$sPath}/4-callback.php" );
	$oTransaction->setRedirectUrl( "{$sProtocol}://{$sHostname}{$sPath}/5-return.php" );

	$oTransaction->setReference( $sOrderId );
	$oTransaction->setDescription( 'test order ' . $sOrderId );

	$oTransaction->register();

	file_put_contents( $sOrderFile, json_encode( [
		'status'			=> 'pending', // callback need to update this status
		'transaction_id'	=> $oTransaction->getId()
	] ) );

	$sActionUrl = $oTransaction->getActionUrl();
	if ( NULL !== $sActionUrl ) {
		// Redirect the customer to the CardGate payment gateway.
		header( 'Location: ' . $sActionUrl );
	} else {
		// Transaction was successfull without need for customer interaction.
		echo 'OK';
	}

} catch ( cardgate\api\Exception $oException_ ) {
	echo htmlspecialchars( $oException_->getMessage() );
}
