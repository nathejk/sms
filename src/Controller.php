<?php
namespace Nathejk\Sms;

use Symfony\Component\HttpFoundation\Request;

class Controller
{
    public function indexAction(Application $app, Request $request)
    {
        $text = file_get_contents(__DIR__ . '/../README.md');
        return \Michelf\MarkdownExtra::defaultTransform($text);
    }

    public function smsMockAction(Application $app, Request $request)
    {
        return "<succes>SMS succesfully sent to 1 recipient(s)</succes>";
    }
}
