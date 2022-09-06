<?php

namespace Tests\Feature;

use Tests\TestCase;

class HelloApiTest extends TestCase {

    public function test_hello() {
        $response = $this->get('/api');
        $response->assertStatus(200);
        $this->assertEquals('Hello World com Laravel', $response->content()); 
    }
}
