<?php

namespace App\Helper;

use AmrShawky\Currency;
use App\Models\Transaction;
use Carbon\Carbon;
use Cknow\Money\Money;
use Exception;
use Illuminate\Support\Collection;

class FeeCalculator
{
    public function depositRuleFee(string $amount, string $currency): string
    {
        return Money::parseByIntlLocalizedDecimal($amount, $currency)
            ->multiply(config('params.depositFee'))
            ->divide(100)
            ->formatByDecimal();
    }

    public function withdrawnBusinessRuleFee(string $amount, string $currency): string
    {
        return Money::parseByIntlLocalizedDecimal($amount, $currency)
            ->multiply(config('params.withdrawBusinessFee'))
            ->divide(100)
            ->formatByDecimal();
    }


    /**
     * @throws Exception
     */
    public function withdrawnPrivateRuleFee(array $transaction, Collection $transactions): string
    {
        $transaction['temp'] = $this->checkIfNeedToConvertBaseCurrency($transaction);
        if ($transaction['temp'] > config('params.withdrawPrivateLimitAmount')) {
            $amount = $this->subtractExceeded($transaction['temp']);
            if ($transaction[Transaction::CURRENCY_CODE] !== config('params.baseCurrency'))
                $amount = $this->getConversionToOriginConfig($amount, $transaction[Transaction::CURRENCY_CODE]);
            return $amount;
        }
        $lastTransactions = $transactions
            ->whereBetween(Transaction::DATE,
                [
                    Carbon::createFromDate($transaction[Transaction::DATE])->startOfWeek()->format('Y-m-d'),
                    Carbon::createFromDate($transaction[Transaction::DATE])->format('Y-m-d'),

                ]
            );
        $lastTransactions = $lastTransactions->reject(function ($item) use ($transaction) {
            return $item[Transaction::INDEX] > $transaction[Transaction::INDEX];
        });
        $sumAmount = Money::parseByIntlLocalizedDecimal('0.00', config('params.baseCurrency'));
        foreach ($lastTransactions as $amount) {
            $amount['temp'] = $this->checkIfNeedToConvertBaseCurrency($amount);
            $sumAmount = Money::parseByIntlLocalizedDecimal($amount['temp'], config('params.baseCurrency'))
                ->add($sumAmount);
        }
        if ($sumAmount->greaterThan(Money::parseByIntlLocalizedDecimal(config('params.withdrawPrivateLimitAmount'), config('params.baseCurrency')))
            ||
            $lastTransactions->count() > config('params.withdrawTransactionLimit')
        ) {
            $exceeded = Money::parseByIntlLocalizedDecimal($transaction['temp'], config('params.baseCurrency'))
                ->add(Money::parseByIntlLocalizedDecimal('1000.00', config('params.baseCurrency')))
                ->formatByDecimal();

            $amount = $this->subtractExceeded($exceeded);

            if ($transaction[Transaction::CURRENCY_CODE] !== config('params.baseCurrency'))
                return $this->getConversionToOriginConfig($amount, $transaction[Transaction::CURRENCY_CODE]);
            return $amount;
        }


        return 0;
    }

    /**
     * @param array $transaction
     * @return string
     * @throws Exception
     */
    private function checkIfNeedToConvertBaseCurrency(array $transaction): string
    {
        if ($transaction[Transaction::CURRENCY_CODE] !== config('params.baseCurrency')) {
            $amount = $this->getConversionFromBaseConfig($transaction[Transaction::AMOUNT], $transaction[Transaction::CURRENCY_CODE]);
        } else {

            $amount = Money::parse($transaction[Transaction::AMOUNT], $transaction[Transaction::CURRENCY_CODE])->formatByDecimal();
        }
        return $amount;
    }

    /**
     * @throws Exception
     */
    private function getConversionFromBaseConfig($amount, $from): string
    {
        $convert = Currency::convert()
            ->from($from)
            ->to(config('params.baseCurrency'))
            ->amount($amount)->get();
        return Money::parseByIntlLocalizedDecimal($convert, config('params.baseCurrency'))->formatByDecimal();

    }

    private function subtractExceeded(string $amount): string
    {
        return Money::parseByIntlLocalizedDecimal($amount, config('params.baseCurrency'))
            ->subtract(Money::parseByIntlLocalizedDecimal(config('params.withdrawPrivateLimitAmount'), config('params.baseCurrency')))
            ->multiply(config('params.withdrawPrivateFee'))
            ->divide(100)
            ->formatByDecimal();


    }

    /**
     * @throws Exception
     */
    private function getConversionToOriginConfig($amount, $to): string
    {
        $convert = Currency::convert()
            ->from(config('params.baseCurrency'))
            ->to($to)
            ->amount($amount)->get();
        return Money::parseByIntlLocalizedDecimal($convert, config('params.baseCurrency'))->formatByDecimal();

    }

}
