<?php
namespace Nathejk\Sms;

class MessageQueueConnectionMock
{
    protected $closure;
    protected $payloads;

    public function __construct(array $payloads)
    {
        $this->payloads = $payloads;
    }

    public function subscribe($queueName, \Closure $closure)
    {
        $this->closure = $closure;
    }

    public function wait()
    {
        #var_dump($this->closure);
        $closure = $this->closure;
        foreach ($this->payloads as $payload) {
            $closure($payload);
        }
    }

}
