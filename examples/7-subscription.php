<?php

try {
    if (false == @include 'init.php') {
        die('init.php missing - copy or rename the init.example.php to init.php and configure it with your account details');
    }

    if (
        ! empty($_POST['period_amount'])
        && isset($_POST['period'])
        && isset($_POST['period_type'])
    ) {
        $oSubscription = $oCardGate->subscriptions()->create($siteId, (int) $_POST['period'], $_POST['period_type'], (int) $_POST['period_amount'], 'EUR');
        $oSubscription->setPaymentMethod(cardgate\api\Method::BANKTRANSFER);

        // Configure consumer.
        $oConsumer = $oSubscription->getConsumer();

        $oConsumer->setEmail('john@doe.com');
        $oConsumer->setPhone('0123456789');

        $oConsumer->address()->setFirstName('John');
        $oConsumer->address()->setInitials('J.A.N.');
        $oConsumer->address()->setLastName('Doe');
        $oConsumer->address()->setAddress('Test Avenue 33');
        $oConsumer->address()->setZipCode('34342');
        $oConsumer->address()->setCity('Minneapolis');
        $oConsumer->address()->setCountry('US');

        $oConsumer->shippingAddress()->setFirstName('Judy');
        $oConsumer->shippingAddress()->setInitials('J.');
        $oConsumer->shippingAddress()->setLastName('Doe');
        $oConsumer->shippingAddress()->setAddress('Trialstreet 1334');
        $oConsumer->shippingAddress()->setZipCode('77377');
        $oConsumer->shippingAddress()->setCity('Chicago');
        $oConsumer->shippingAddress()->setCountry('US');

        // Configure cart.
        $oCart = $oSubscription->getCart();
        $oItem = $oCart->addItem(\cardgate\api\Item::TYPE_PRODUCT, 'AA21484', 'iMac 27"', 3, 219999, 'http://www.apple.com/imac/');
        $oItem = $oCart->addItem(\cardgate\api\Item::TYPE_SHIPPING, 'SHIPPING', 'Shipping by UPS', 1, 599);


        // Create unique order id with corresponding database file.

        $orderFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cardgate_order_' . time();
        if (! is_writable(dirname($orderFile))) {
            die('unable to create order file');
        }
        $orderId = basename($orderFile);

        // Fill with test data
        file_put_contents($orderFile, json_encode([
            'status'            => 'pending',
            'subscription_id'    => 'test'
        ]));

        // Configure communication endpoint locations.
        $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? 'https' : 'http';
        $hostname = $_SERVER['HTTP_HOST'];
        $path = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);
        $oSubscription->setCallbackUrl("$protocol://$hostname$path/4-callback.php");
        $oSubscription->setRedirectUrl("$protocol://$hostname$path/5-return.php");

        $oSubscription->setReference($orderId);
        $oSubscription->setDescription('test order ' . $orderId);

        $oSubscription->register();

        $actionUrl = $oSubscription->getActionUrl();
        if (null !== $actionUrl) {
            // Redirect the consumer to the CardGate payment gateway.
            header('Location: ' . $actionUrl);
        } else {
            // Transaction was successfull without need for consumer interaction.
            echo 'OK';
        }
    } else {
        echo '<h5>New subscription</h5>';
        echo '<form method="post" action="7-subscription.php">';
        echo 'Amount (cents): <input type="number" name="period_amount" value="660596"> ';
        echo 'Every: <input type="number" name="period" value="1" style="width:40px;"> ';
        echo '<select name="period_type">';

        foreach (
            [
            [ 'id' => 'day', 'name' => 'Days' ],
            [ 'id' => 'week', 'name' => 'Weeks' ],
            [ 'id' => 'month', 'name' => 'Months', 'selected' => true ],
            [ 'id' => 'year', 'name' => 'Years' ],
            ] as $period
        ) {
            echo '<option value="' . $period['id'] . '"';
            if (isset($period['selected'])) {
                echo ' selected="selected"';
            }
            echo '>' . $period['name'] . '</option>';
        }

        echo '</select> ';
        echo '<button>Submit</button>';
        echo '</form>';
    }
} catch (cardgate\api\Exception $oException_) {
    echo htmlspecialchars($oException_->getMessage());
}
