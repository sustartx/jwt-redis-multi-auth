<?php

namespace SuStartX\JWTRedisMultiAuth\Response;

class JWTRedisMultiAuthErrorResponse
{
    public bool $status = false;
    public int $status_code;
    protected string $title;
    protected string $message;
    protected int $code;

    public function __construct(int $status_code, string $title, string $message, int $code)
    {
        $this->status_code = $status_code;
        $this->title = $title;
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * @param bool $status
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'status_code' => $this->status_code,
            'title' => $this->title,
            'message' => $this->message,
            'code' => $this->code,
        ];
    }
}
