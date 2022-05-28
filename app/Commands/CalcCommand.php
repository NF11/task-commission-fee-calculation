<?php

namespace App\Commands;

use App\Helper\FeeCalculator;
use App\Models\Transaction;
use App\Services\CsvFileReader;
use App\Services\CsvFileWriter;
use Exception;
use LaravelZero\Framework\Commands\Command;

class CalcCommand extends Command
{
    protected $signature = 'run {path : the path of input csv file (required)}';

    protected $description = 'handles operations provided in CSV format and calculates a commission fee based on defined rules';

    public function handle(CsvFileReader $csvFileReader, CsvFileWriter $writer, FeeCalculator $calculator)
    {
        $inputPath = $this->argument('path');
        try {
            $transactionsInput = $csvFileReader->read($inputPath);
            $index = 0;
            $transactionCollection = collect();
            $dataResult = collect();
            foreach ($transactionsInput as $transactionInput) {
                $transaction = new Transaction();
                $transaction
                    ->setDate($transactionInput[0])
                    ->setUserId($transactionInput[1])
                    ->setClientType($transactionInput[2])
                    ->setOperationType($transactionInput[3])
                    ->setCurrencyCode($transactionInput[5])
                    ->setAmount($transactionInput[4])
                    ->setIndex($index);
                $transactionCollection->add($transaction->toArray());
                $index++;
            }
            // all deposit
            $transactionCollectionDeposit = $transactionCollection
                ->where(Transaction::OPERATION_TYPE, config('params.operationTypeDeposit'));

            foreach ($transactionCollectionDeposit as $transactions) {
                $dataResult->add(
                    [
                        Transaction::INDEX => $transactions[Transaction::INDEX],
                        'commission_fee' => $calculator
                            ->depositRuleFee($transactions[Transaction::AMOUNT], $transactions[Transaction::CURRENCY_CODE])
                    ]
                );
            }
            // all Withdraw Business
            $transactionCollectionWithdraw = $transactionCollection
                ->where(Transaction::OPERATION_TYPE, config('params.operationTypeWithdraw'))
                ->where(Transaction::CLIENT_TYPE, config('params.clientTypeBusiness'));
            foreach ($transactionCollectionWithdraw as $transactions) {
                $dataResult->add(
                    [
                        Transaction::INDEX => $transactions[Transaction::INDEX],
                        'commission_fee' => $calculator
                            ->withdrawnBusinessRuleFee($transactions[Transaction::AMOUNT], $transactions[Transaction::CURRENCY_CODE])
                    ]
                );
            }

            // all Withdraw private
            $transactionCollectionPrivate = $transactionCollection
                ->where(Transaction::OPERATION_TYPE, config('params.operationTypeWithdraw'))
                ->where(Transaction::CLIENT_TYPE, config('params.clientTypePrivate'))
                ->groupBy(Transaction::USER_ID);
            foreach ($transactionCollectionPrivate as $userTransactions) {
                foreach ($userTransactions as $transactions) {
                    $dataResult->add(
                        [
                            Transaction::INDEX => $transactions[Transaction::INDEX],
                            'commission_fee' => $calculator
                                ->withdrawnPrivateRuleFee($transactions, $userTransactions)
                        ]
                    );
                }
            }
            $dataResult = $dataResult->transform(function ($item) {

                return [
                    'commission_fee' => round($item['commission_fee'], 1),
                    'index' => $item['index']
                ];
            });
            $dataResult = $dataResult->sortBy('index');
            $writer->write($dataResult->sortBy('index'));

            foreach ($dataResult as $display) {
                $this->line($display['commission_fee']);
            }


        } catch (Exception $e) {
            $this->error($e->getMessage() . ' thrown in ' . $e->getFile() . ' on line ' . $e->getLine());
        }
    }


}
