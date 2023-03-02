<?php
try {

	if ( FALSE == @include 'init.php' ) {
		die( 'init.php missing - copy or rename the init.example.php to init.php and configure it with your account details' );
	}

	// Retrieve status of transaction.
	$sOrderFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $_GET['reference'];
	if (
		! file_exists( $sOrderFile )
		|| FALSE == ( $sOrderData = file_get_contents( $sOrderFile ) )
		|| FALSE == ( $aOrderData = json_decode( $sOrderData, TRUE ) )
		|| ! isset( $aOrderData['status'] )
	) {
		die( 'invalid transaction' );
	}

	// Print status.
	echo "transaction has status: <strong>{$aOrderData['status']}</strong><br><a href=\"1-payment.php\">new transaction</a>";

} catch ( cardgate\api\Exception $oException_ ) {
	echo htmlspecialchars( $oException_->getMessage() );
}
