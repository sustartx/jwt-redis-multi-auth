<?php

namespace SuStartX\JWTRedisMultiAuth\Response;

class JWTRedisMultiAuthSuccessResponse
{
    private bool $status = true;
    public int $status_code;
    protected array $data;

    public function __construct(int $status_code, array $data)
    {
        $this->status_code = $status_code;
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    /**
     * @param int $status_code
     */
    public function setStatusCode(int $status_code): void
    {
        $this->status_code = $status_code;
    }

    /**
     * @return array
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'status_code' => $this->status_code,
            'data' => $this->data,
        ];
    }
}
