<?php
try {

	if ( FALSE == @include 'init.php' ) {
		die( 'init.php missing - copy or rename the init.example.php to init.php and configure it with your account details' );
	}

	// Make sure callback parameters are from the CardGate gateway.
	if ( FALSE == $oCardGate->transactions()->verifyCallback( $_GET, $sSiteKey ) ) {
		die( 'invalid callback' );
	}

	// Overwrite order database file with updated status.
	$sOrderFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $_GET['reference'];
	if ( ! file_exists( $sOrderFile ) ) {
		die( 'invalid transaction' );
	}
	file_put_contents( $sOrderFile, json_encode( [
		'status'			=> $_GET['status'],
		'transaction_id'	=> $_GET['transaction']
	] ) );

	// The gateway expects a formatted response.
	die( "{$_GET['transaction']}.{$_GET['code']}" );

} catch ( cardgate\api\Exception $oException_ ) {
	echo htmlspecialchars( $oException_->getMessage() );
}
