<?php
namespace Nathejk\Sms;

class Repository
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function save(\stdClass $message)
    {
        $fields = implode(', ', array_keys((array)$message));
        $values = ':' . implode(', :', array_keys((array)$message));
        $query = $this->app['db']->prepare("INSERT INTO message ($fields) VALUES ($values)");
        return $query->execute((array)$message);
    }
}
