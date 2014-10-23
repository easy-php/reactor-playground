<?php

require 'vendor/autoload.php';

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

$i = 0;

$output = new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG);
$server = $argc > 2 ? $argv[2] : 'Gucci';

$progress = new ProgressBar($output);

$app = function ($request, $response) use (&$i, $server, $progress) {
    $i++;

    $progress->advance();

    $text = "This is request number $i.\n";
    $headers = array('Content-Type' => 'text/plain', 'Server' => $server, 'X-Powered-By' => 'muscle');
                                                            usleep(5000);
    $response->writeHead(200, $headers);
    $response->end($text);
};

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket);

$http->on('request', $app);
$port = $argc > 1 ? $argv[1] : 1337;
echo "Server running at http://127.0.0.1:{$port}\n";
$progress->start();

$socket->listen($port);
$loop->run();
