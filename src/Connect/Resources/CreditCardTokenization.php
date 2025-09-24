<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class CreditCardTokenization extends BaseConnectResource
{
    /**
     * Get payment methods for Adyen WebComponent
     */
    public function getPaymentMethods(): array
    {
        return $this->client->get('/v2/creditcard/tokenization/payment-methods');
    }

    /**
     * Initiate credit card tokenization
     *
     * @param array $data Tokenization initiation data
     */
    public function initiate(array $data): array
    {
        $this->validateRequired($data, ['paymentMethod', 'browserInfo', 'studioId', 'returnUrl', 'origin']);

        $data = $this->filterEmptyValues($data);

        return $this->client->post('/v2/creditcard/tokenization/initiate', $data);
    }

    /**
     * Complete credit card tokenization
     *
     * @param string $tokenizationReference Tokenization reference UUID
     * @param array $data Completion data (threeDSResult or redirectResult)
     */
    public function complete(string $tokenizationReference, array $data): array
    {
        if (empty($tokenizationReference)) {
            throw new \InvalidArgumentException('Tokenization reference is required');
        }

        $data = $this->filterEmptyValues($data);

        return $this->client->post("/v2/creditcard/tokenization/{$tokenizationReference}/complete", $data);
    }

    /**
     * Get tokenization state
     *
     * @param string $tokenizationReference Tokenization reference UUID
     */
    public function getState(string $tokenizationReference): array
    {
        if (empty($tokenizationReference)) {
            throw new \InvalidArgumentException('Tokenization reference is required');
        }

        return $this->client->get("/v2/creditcard/tokenization/{$tokenizationReference}/state");
    }
}