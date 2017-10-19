<?php
try {

	if ( FALSE == @include 'init.php' ) {
		die( 'init.php missing - copy or rename the init.example.php to init.php and configure it with your account details' );
	}

	$aDetails = [];
	$oTransaction = $oCardGate->transactions()->get( 'T16A21948968', $aDetails );
	$oTransaction->recur( 50 );

} catch ( cardgate\api\Exception $oException_ ) {
	echo htmlspecialchars( $oException_->getMessage() );
}
