<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Tests\Unit;

use Anisotton\Pagarme\Support\PaymentHelper;
use PHPUnit\Framework\TestCase;

class PaymentHelperTest extends TestCase
{
    public function test_cents_to_currency_conversion(): void
    {
        $this->assertEquals(10.0, PaymentHelper::centsToCurrency(1000));
        $this->assertEquals(1.50, PaymentHelper::centsToCurrency(150));
        $this->assertEquals(0.01, PaymentHelper::centsToCurrency(1));
    }

    public function test_currency_to_cents_conversion(): void
    {
        $this->assertEquals(1000, PaymentHelper::currencyToCents(10.0));
        $this->assertEquals(150, PaymentHelper::currencyToCents(1.50));
        $this->assertEquals(1, PaymentHelper::currencyToCents(0.01));
    }

    public function test_format_document(): void
    {
        $this->assertEquals('12345678901', PaymentHelper::formatDocument('123.456.789-01'));
        $this->assertEquals('12345678000199', PaymentHelper::formatDocument('12.345.678/0001-99'));
        $this->assertEquals('12345678901', PaymentHelper::formatDocument('12345678901'));
    }

    public function test_valid_cpf(): void
    {
        // CPF válido (fictício para teste)
        $this->assertTrue(PaymentHelper::isValidCpf('11144477735'));

        // CPF inválido
        $this->assertFalse(PaymentHelper::isValidCpf('12345678901'));
        $this->assertFalse(PaymentHelper::isValidCpf('11111111111'));
        $this->assertFalse(PaymentHelper::isValidCpf('123456789'));
    }

    public function test_format_phone(): void
    {
        $expected = [
            'country_code' => '55',
            'area_code' => '11',
            'number' => '987654321',
        ];

        $this->assertEquals($expected, PaymentHelper::formatPhone('11987654321'));
        $this->assertEquals($expected, PaymentHelper::formatPhone('(11) 98765-4321'));
    }

    public function test_invalid_phone_format(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PaymentHelper::formatPhone('123456');
    }

    public function test_credit_card_validation(): void
    {
        // Números de cartão válidos para teste (Luhn algorithm)
        $this->assertTrue(PaymentHelper::isValidCreditCard('4111111111111111')); // Visa
        $this->assertTrue(PaymentHelper::isValidCreditCard('5555555555554444')); // Mastercard

        // Números inválidos
        $this->assertFalse(PaymentHelper::isValidCreditCard('1234567890123456'));
        $this->assertFalse(PaymentHelper::isValidCreditCard('123'));
    }

    public function test_credit_card_brand_detection(): void
    {
        $this->assertEquals('visa', PaymentHelper::getCreditCardBrand('4111111111111111'));
        $this->assertEquals('mastercard', PaymentHelper::getCreditCardBrand('5555555555554444'));
        $this->assertEquals('amex', PaymentHelper::getCreditCardBrand('378282246310005'));
        $this->assertEquals('unknown', PaymentHelper::getCreditCardBrand('1234567890123456'));
    }

    public function test_installments_generation(): void
    {
        $installments = PaymentHelper::generateInstallments(1000, 3, 0.0);

        $this->assertCount(3, $installments);
        $this->assertEquals(1, $installments[0]['number']);
        $this->assertEquals(1000, $installments[0]['amount']);
        $this->assertEquals(1000, $installments[0]['total']);

        $this->assertEquals(2, $installments[1]['number']);
        $this->assertEquals(500, $installments[1]['amount']);
        $this->assertEquals(1000, $installments[1]['total']);
    }

    public function test_installments_with_interest(): void
    {
        $amount = PaymentHelper::calculateInstallmentWithInterest(1000, 2, 5.0);
        $this->assertGreaterThan(500, $amount); // Com juros deve ser maior que sem juros
    }
}
