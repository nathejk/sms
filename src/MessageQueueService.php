<?php
namespace Nathejk\Sms;

class MessageQueueService
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function listen()
    {
        $app = $this->app;
        $this->app['mq.client']->subscribe("sms", function (string $payload) use ($app) {
            $message = json_decode($payload);
            try {
                $this->app['jsonschema']['message']->validate($message);
            } catch (\JsonSchemaValidation\ValidationException $e) {
                $app['console.output']->writeln($e->getViolations());
                return false;
            } catch (\Exception $e) {
                $app['console.output']->writeln($e->getMessage());
                return false;
            }
            $message->status = $app['sms']->send($message);
            $message->uts = $app['time'];
            $app['message.repo']->save($message);
            $app['console.output']->writeln(json_encode($message));
        });
        $this->app['mq.client']->wait();
    }
}
