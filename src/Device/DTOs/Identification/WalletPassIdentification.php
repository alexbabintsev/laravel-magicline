<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Identification;

class WalletPassIdentification extends BaseIdentification
{
    protected function __construct(array $data)
    {
        $this->type = 'WALLET_PASS';
        $this->value = $data['value'];
    }

    /**
     * Create wallet pass identification
     */
    public static function create(string $walletPassId): self
    {
        return self::from(['value' => $walletPassId]);
    }

    /**
     * Check if wallet pass ID is a valid UUID
     */
    public function isValidUuid(): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $this->value) === 1;
    }

    /**
     * Get UUID version (if valid UUID)
     */
    public function getUuidVersion(): ?int
    {
        if (! $this->isValidUuid()) {
            return null;
        }

        $parts = explode('-', $this->value);

        return (int) $parts[2][0];
    }
}
