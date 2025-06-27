<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\DataTransferObjects;

class CustomerDto extends BaseDto
{
    public function __construct(
        public string $type,
        public string $name,
        public string $email,
        public string $document_type,
        public string $document,
        public ?array $phones = null,
        public ?array $address = null,
        public ?array $metadata = null,
        public ?string $birthdate = null,
        public ?string $gender = null
    ) {}

    public static function individual(
        string $name,
        string $email,
        string $document,
        ?array $phones = null,
        ?array $address = null,
        ?array $metadata = null
    ): self {
        return new self(
            type: 'individual',
            name: $name,
            email: $email,
            document_type: 'CPF',
            document: $document,
            phones: $phones,
            address: $address,
            metadata: $metadata
        );
    }

    public static function company(
        string $name,
        string $email,
        string $document,
        ?array $phones = null,
        ?array $address = null,
        ?array $metadata = null
    ): self {
        return new self(
            type: 'company',
            name: $name,
            email: $email,
            document_type: 'CNPJ',
            document: $document,
            phones: $phones,
            address: $address,
            metadata: $metadata
        );
    }
}
