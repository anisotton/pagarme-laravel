<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Support;

class PaymentHelper
{
    /**
     * Convert cents to real currency format
     */
    public static function centsToCurrency(int $cents): float
    {
        return $cents / 100;
    }

    /**
     * Convert currency to cents
     */
    public static function currencyToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Format CPF/CNPJ document
     */
    public static function formatDocument(string $document): string
    {
        return preg_replace('/\D/', '', $document);
    }

    /**
     * Validate CPF
     */
    public static function isValidCpf(string $cpf): bool
    {
        $cpf = self::formatDocument($cpf);

        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($i = 9; $i < 11; $i++) {
            $sum = 0;
            for ($j = 0; $j < $i; $j++) {
                $sum += $cpf[$j] * (($i + 1) - $j);
            }
            $checkDigit = ((10 * $sum) % 11) % 10;
            if ($cpf[$i] != $checkDigit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate CNPJ
     */
    public static function isValidCnpj(string $cnpj): bool
    {
        $cnpj = self::formatDocument($cnpj);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights1[$i];
        }
        $checkDigit1 = ($sum % 11) < 2 ? 0 : 11 - ($sum % 11);

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights2[$i];
        }
        $checkDigit2 = ($sum % 11) < 2 ? 0 : 11 - ($sum % 11);

        return $cnpj[12] == $checkDigit1 && $cnpj[13] == $checkDigit2;
    }

    /**
     * Format phone number
     */
    public static function formatPhone(string $phone): array
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) === 11) {
            return [
                'country_code' => '55',
                'area_code' => substr($phone, 0, 2),
                'number' => substr($phone, 2),
            ];
        }

        if (strlen($phone) === 10) {
            return [
                'country_code' => '55',
                'area_code' => substr($phone, 0, 2),
                'number' => substr($phone, 2),
            ];
        }

        throw new \InvalidArgumentException('Invalid phone number format');
    }

    /**
     * Generate installments options
     */
    public static function generateInstallments(int $amount, int $maxInstallments = 12, float $interestRate = 0.0): array
    {
        $installments = [];

        for ($i = 1; $i <= $maxInstallments; $i++) {
            $installmentAmount = $i === 1 ? $amount : self::calculateInstallmentWithInterest($amount, $i, $interestRate);

            $installments[] = [
                'number' => $i,
                'amount' => $installmentAmount,
                'total' => $installmentAmount * $i,
            ];
        }

        return $installments;
    }

    /**
     * Calculate installment amount with interest
     */
    public static function calculateInstallmentWithInterest(int $amount, int $installments, float $interestRate): int
    {
        if ($interestRate === 0.0) {
            return (int) round($amount / $installments);
        }

        $monthlyRate = $interestRate / 100;
        $coefficient = pow(1 + $monthlyRate, $installments);
        $installmentValue = $amount * ($coefficient * $monthlyRate) / ($coefficient - 1);

        return (int) round($installmentValue);
    }

    /**
     * Validate credit card number using Luhn algorithm
     */
    public static function isValidCreditCard(string $cardNumber): bool
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        $length = strlen($cardNumber);

        if ($length < 13 || $length > 19) {
            return false;
        }

        $sum = 0;
        $alternate = false;

        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int) $cardNumber[$i];

            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $alternate = !$alternate;
        }

        return $sum % 10 === 0;
    }

    /**
     * Get credit card brand
     */
    public static function getCreditCardBrand(string $cardNumber): string
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        $patterns = [
            'visa' => '/^4/',
            'mastercard' => '/^5[1-5]/',
            'amex' => '/^3[47]/',
            'diners' => '/^3[0689]/',
            'discover' => '/^6(?:011|5)/',
            'elo' => '/^(4011|4312|4389|4514|4573|5041|5066|5090|6277|6362|6363|6504|6505|6516)/',
            'hipercard' => '/^(606282|637095|637568|637599|637609|637612)/',
        ];

        foreach ($patterns as $brand => $pattern) {
            if (preg_match($pattern, $cardNumber)) {
                return $brand;
            }
        }

        return 'unknown';
    }
}
