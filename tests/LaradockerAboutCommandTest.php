<?php

namespace Jdsantos\Laradocker\Tests;

class LaradockerAboutCommandTest extends TestCase
{
    public function testAboutCommand()
    {
        $this->artisan('about')->expectsOutputToContain('Laradocker');
    }
}
