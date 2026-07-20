<?php

return [
    'cart' => [
        'already_purchased' => 'The file has already been purchased.',
        'already_in_cart' => 'The file is already in the cart.',
        'pending_payment_exists' => 'The file already has a pending payment and cannot be added.',
        'added' => 'Item was added to the cart.',
        'removed' => 'Item was removed from the cart.',
    ],

    'payment' => [
        'terms_not_accepted' => 'You must accept the payment terms.',
        'cart_empty' => 'Your cart is empty.',
        'pending_exists' => 'There is a pending payment for one or more selected files.',
        'already_purchased' => 'You have already purchased this file.',

        'no_external_id' => 'Missing external payment identifier.',
        'cannot_suspend' => 'This payment cannot be canceled.',
        'egov_invalid_response' => 'Invalid response received from the payment provider.',
        'suspended_successfully' => 'Payment was successfully canceled.',
        'confirm_suspend' => 'Are you sure you want to cancel this payment?',
    ],
];
