<?php

namespace Jdsantos\Laradocker\Tests;

use PHPUnit\Framework\Attributes\Test;

class LaradockerAboutCommandTest extends TestCase
{
    #[Test]
    public function it_shows_laradocker_on_about()
    {
        $this->artisan('about')->expectsOutputToContain('Laradocker');
    }
}
