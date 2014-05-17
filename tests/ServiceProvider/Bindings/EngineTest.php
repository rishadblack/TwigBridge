<?php

namespace TwigBridge\Tests\ServiceProvider\Bindings;

use TwigBridge\Tests\Base;
use TwigBridge\ServiceProvider;
use Mockery as m;

class EngineTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testEngineExtension()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        $this->assertInstanceOf('TwigBridge\Engine\Twig', $app['view']->getEngineResolver()->resolve('twig'));
    }

    public function testSetLexer()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        $lexer = m::mock('Twig_LexerInterface');
        $lexer->shouldReceive('fooBar')->andReturn('buttonMoon');
        $app['twig.lexer'] = $lexer;

        $app['view']->getEngineResolver()->resolve('twig');
        $lexer = $app['twig.bridge']->getLexer();

        $this->assertEquals('buttonMoon', $lexer->fooBar());
    }
}