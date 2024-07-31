<?php

namespace SwooleApp\SwooleAppMongoConnection\Pool;

class ConnectionPool
{
    protected array $pool = [];

    protected int $countResource;
    private bool $mutexes_pull = false;
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
            if (!$this->coroutineRuntime()) {
                usleep(10000);
            } else {
                \co::sleep(0.01);
            }
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
            if (!$this->coroutineRuntime()) {
                usleep(10);
            } else {
                \co::sleep(0.01);
            }
            $key = array_search(true, $this->freePull);
        }
        $this->freePull[$key] = false;
        $this->giveAwayMutexes();
        return $key;
    }

    protected function coroutineRuntime(): bool
    {
        return (\Swoole\Coroutine::getCid() !== -1);
    }

}
