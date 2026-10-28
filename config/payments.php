<?php

return [
    'methods' => [
        'bank' => 'Transfer Bank',
        'ewallet' => 'E-Wallet',
        'cod' => 'Bayar di Tempat (COD)',
    ],

    'providers' => [
        'bank' => ['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI'],
        'ewallet' => ['DANA', 'OVO', 'GoPay', 'ShopeePay', 'LinkAja'],
    ],
];
