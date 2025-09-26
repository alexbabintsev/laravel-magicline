<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class ImageUpload extends BaseConnectResource
{
    /**
     * Get pre-signed URL for image upload
     */
    public function getUploadUrl(): array
    {
        return $this->client->get('/v1/prospectimage/uploadurl');
    }
}
