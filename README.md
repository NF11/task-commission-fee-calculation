# Commission fee calculator app

## About the task

Some bank allows private and business clients to `deposit` and `withdraw` funds to and from accounts in multiple
currencies. Clients may be charged a commission fee.

You have to create an application that handles operations provided in CSV format and calculates a commission fee based
on defined rules.

## Commission fee calculation

- Commission fee is always calculated in the currency of the operation. For example, if you `withdraw` or `deposit` in
  US dollars then commission fee is also in US dollars.
- Commission fees are rounded up to currency's decimal places. For example, `0.023 EUR` should be rounded up
  to `0.03 EUR`.

### Deposit rule

All deposits are charged 0.03% of deposit amount.

### Withdraw rules

There are different calculation rules for `withdraw` of `private` and `business` clients.

**Private Clients**

- Commission fee - 0.3% from withdrawn amount.
- 1000.00 EUR for a week (from Monday to Sunday) is free of charge. Only for the first 3 withdraw operations per a week.
  4th and the following operations are calculated by using the rule above (0.3%). If total free of charge amount is
  exceeded them commission is calculated only for the exceeded amount (i.e. up to 1000.00 EUR no commission fee is
  applied).

## Installation

this application is based on laravel zero

to install all dependency

```bash
composer install 
```

I name the project cli_calc so to see available command

```bash
php cli_calc 
```

To lunch the main app

```bash
php cli_calc run 'path_of_your_file.csv'
```

### Config

you find in the config folder the file ```prams.php``` where you could change the configuration of the rates

### Result

You will find the result in a csv file in the ect folder with the name result.csv

## Expected result

Output of calculated commission fees for each operation.

In each output line only final calculated commission fee for a specific operation must be provided without currency.

# My Result

```
➜  cat input.csv 
2014-12-31,4,private,withdraw,1200.00,EUR
2015-01-01,4,private,withdraw,1000.00,EUR
2016-01-05,4,private,withdraw,1000.00,EUR
2016-01-05,1,private,deposit,200.00,EUR
2016-01-06,2,business,withdraw,300.00,EUR
2016-01-06,1,private,withdraw,30000,JPY
2016-01-07,1,private,withdraw,1000.00,EUR
2016-01-07,1,private,withdraw,100.00,USD
2016-01-10,1,private,withdraw,100.00,EUR
2016-01-10,2,business,deposit,10000.00,EUR
2016-01-10,3,private,withdraw,1000.00,EUR
2016-02-15,1,private,withdraw,300.00,EUR
2016-02-19,5,private,withdraw,3000000,JPY

➜  php script.php input.csv
0.6
3
0
0.1
1.5
0
3
0.3
0.3
3
0
0
8590.9
```

Note: the last transaction output is not like the given example.
