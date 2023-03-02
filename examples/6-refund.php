<?php
try {

	if ( FALSE == @include 'init.php' ) {
		die( 'init.php missing - copy or rename the init.example.php to init.php and configure it with your account details' );
	}

	$aDetails = [];
	$oTransaction = $oCardGate->transactions()->get( 'T16A21948968', $aDetails );

	if ( $oTransaction->canRefund() ) {
		$oTransaction->refund( 50 );
		echo '€ 0,50 of transaction ' . $oTransaction->getId() . ' refunded.';

		$iRemainder_ = 0;
		if (
			$oTransaction->canRefund( $iRemainder_ )
			&& $iRemainder_ > 0
		) {
			echo ' € ' . number_format( $iRemainder_ / 100, 2, ',', '.' ) . ' remaining.';
		}
	} else {
		echo 'Transaction ' . $oTransaction->getId() . ' can not be refunded.';
	}

} catch ( cardgate\api\Exception $oException_ ) {
	echo htmlspecialchars( $oException_->getMessage() );
}
