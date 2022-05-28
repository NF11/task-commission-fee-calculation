<?php

return [
    'currencyApi' => 'https://developers.paysera.com/tasks/api/',
    'clientTypePrivate' => 'private',
    'clientTypeBusiness' => 'business',
    'operationTypeWithdraw' => 'withdraw',
    'operationTypeDeposit' => 'deposit',
    'baseCurrency' => 'EUR',
    'depositFee' => 0.03,
    'withdrawBusinessFee' => 0.5,
    'withdrawPrivateFee' => 0.3,
    'currencyDefaultScale' => 2,
    'withdrawPrivateLimitAmount' => '1000.00',
    'withdrawTransactionLimit' => 3,
    'outputCsv' => base_path('ect'),


];
