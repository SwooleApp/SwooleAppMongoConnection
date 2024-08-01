<?php

namespace SwooleApp\SwooleAppMongoConnection\Pool;

class ConnectionPool
{
    /**
     * @var array<int, mixed>
     */
    protected array $pool = [];
    /**
     * @var int
     */
    protected int $countResource;
    /**
     * @var bool
     */
    private bool $mutexes_pull = false;
    /**
     * @var array<int, bool>
     */
    protected array $freePull = [];

    /**
     * @param int $countResource
     */
    public function __construct(int $countResource)
    {
        $this->countResource = $countResource;
    }

    public function init(callable $initFUnction): void
    {
        for ($i = 0; $i < $this->countResource; $i++) {
            $this->pool[$i] = $initFUnction();
            $this->freePull[$i] = true;
        }
    }


    protected function takeMutexesPull(): void
    {
        while ($this->mutexes_pull) {
            $this->wait();
        }
        $this->mutexes_pull = true;
    }

    protected function giveAwayMutexes(): void
    {
        $this->mutexes_pull = false;
    }

    protected function searchFreeResource(): int
    {
        $this->takeMutexesPull();
        $key = array_search(true, $this->freePull);
        while ($key === false) {
            $this->wait();
            $key = array_search(true, $this->freePull);
        }
        $this->freePull[$key] = false;
        $this->giveAwayMutexes();
        return (int)$key;
    }

    protected function coroutineRuntime(): bool
    {
        return (\Swoole\Coroutine::getCid() !== -1);
    }

    /**
     * @return void
     */
    protected function wait(): void
    {
        if (!$this->coroutineRuntime()) {
            usleep(10);
        } else {
            // @phpstan-ignore-next-line
            \co::sleep(0.01);
        }
    }

}
