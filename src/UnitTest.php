<?php
namespace Nathejk\Sms;

class UnitTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $app = new Application(['debug' => true]);
        $app->boot();
        $this->app = $app;
    }

    public function test_recieve_message()
    {
        $message1 = [
            "body" => "hello",
            "recipient" => "12345678",
            "sender" => "test",
        ];
        $this->app['time'] = 987654;

        // expext 1 sms to be send
        $this->app['sms'] = $this->getMockBuilder(SmsService::class)->disableOriginalConstructor()->getMock();
        $this->app['sms']->expects($this->once())->method('send')->willReturn('ok');
        //$this->app['sms.dsn'] = 'cpsms://UN:PW@localhost/sms';

        // expect message to be saved to database
        $this->app['message.repo'] = $this->getMockBuilder(Repository::class)->disableOriginalConstructor()->getMock();
        $this->app['message.repo']->expects($this->once())->method('save')->with((object)($message1 + ["status"=>'ok', "uts"=>987654]));

        // catch output to console
        $this->app['console.output'] = $this->getMockBuilder(get_class($this->app['console.output']))->getMock();
        $this->app['console.output']->method('writeln');

        // listen for messages
        $this->app['mq.client'] = new MessageQueueConnectionMock([json_encode($message1)]);
        $this->app['mq']->listen();
    }
}
