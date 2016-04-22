<?php
namespace Nathejk\Sms;

class SmsService
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function send(\stdClass $message)
    {
        $dsn = parse_url($this->app['sms.dsn']);
        $query = http_build_query([
            'message' => $message->body,
            'recipient' => (strlen($message->recipient) == 8 ? '45' : '') . $message->recipient,
            'from' => $message->sender,
            'username' => $dsn['user'],
            'password' => $dsn['pass'],
        ]);
        $port = isset($dsn['port']) ? ":{$dsn['port']}" : '';
        $path = isset($dsn['path']) ? $dsn['path'] : '/';
        $url = "http://{$dsn['host']}$port$path?$query";
        // $The url is opened
        $reply = file_get_contents($url);
        if ($reply == "<succes>SMS succesfully sent to 1 recipient(s)</succes>") {
            return 'ok';
        }
        $this->app['console.output']->writeln("$reply ($url)");
        return 'unknown';
    }
}
