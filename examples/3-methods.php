<?php
try {

	include 'init.php';

	$aMethods = $oCardGate->methods()->all( $iSiteId );

	echo '<form method="post" action="1-payment.php">';
	echo 'Select payment option: <select name="option">';

	foreach( $aMethods as $oMethod ) {
		echo '<option value="' . $oMethod->getId() . '">' . $oMethod->getId() . '</option>';
	}

	echo '<option value="">or select later</option></select>';
	echo '<button>Submit</button>';
	echo '</form>';

} catch ( cardgate\api\Exception $oException_ ) {
	echo htmlspecialchars( $oException_->getMessage() );
}
