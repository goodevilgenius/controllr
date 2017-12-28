<?php namespace Tests;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

        $this->assertEquals(
            env('APP_NAME'), $this->response->getContent()
        );
    }
}
