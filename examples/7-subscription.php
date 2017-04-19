<?php
try {

	if ( FALSE == @include 'init.php' ) {
		die( 'init.php missing - copy or rename the init.example.php to init.php and configure it with your account details' );
	}

	if (
		! empty( $_POST['period_amount'] )
		&& isset( $_POST['period'] )
		&& isset( $_POST['period_type'] )
	) {

		$oSubscription = $oCardGate->subscriptions()->create( $iSiteId, 660596, 'EUR' );
		$oSubscription->setPaymentMethod( cardgate\api\Method::BANKTRANSFER );








	} else {

		echo '<form method="post" action="7-subscription.php">';
		echo 'Amount (cents): <input type="number" name="period_amount" value=""> ';
		echo 'Every: ';
		echo '<input type="number" name="period" value="1" style="width:40px;"> ';
		echo '<select name="period_type">';

		foreach( [
			[ 'id' => 'week',  'name' => 'Weeks' ],
			[ 'id' => 'month', 'name' => 'Months', 'selected' => TRUE ],
			[ 'id' => 'year',  'name' => 'Years' ],
		] as $aPeriod ) {
			echo '<option value="' . $aPeriod['id'] . '"';
			if ( isset( $aPeriod['selected'] ) ) {
				echo ' selected="selected"';
			}
			echo '>' . $aPeriod['name'] . '</option>';
		}

		echo '</select> ';
		echo '<button>Submit</button>';
		echo '</form>';
	}

} catch ( cardgate\api\Exception $oException_ ) {
	echo htmlspecialchars( $oException_->getMessage() );
}
