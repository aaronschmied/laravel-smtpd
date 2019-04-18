<?php

namespace Smtpd\Network;

abstract class AbstractSocket
{
    /**
     * @var \resource
     */
    private $handle;

    /**
     * @param \resource $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return \resource
     */
    public function getHandle()
    {
        return $this->handle;
    }

    #abstract public function create();

    abstract public function bind(string $ip, int $port): bool;

    abstract public function listen(): bool;

    abstract public function connect(string $ip, int $port);

    abstract public function accept();

    abstract public function select(array &$readHandles, array &$writeHandles, array &$exceptHandles): int;

    abstract public function getPeerName(string &$ip, int &$port);

    abstract public function lastError(): int;

    abstract public function strError(): string;

    abstract public function clearError();

    abstract public function read(): string;

    abstract public function write(string $data): int;

    abstract public function shutdown();

    abstract public function close();
}