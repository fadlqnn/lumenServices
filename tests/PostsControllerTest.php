<?php

namespace Tests;

use App\Models\Post;
use Laravel\Lumen\Testing\DatabaseMigrations;

class PostsControllerTest extends TestCase
{
    use DatabaseMigrations;

    // ─── Task 1 ───────────────────────────────────────────────────────────────

    public function test_routes_exist_for_posts_crud(): void
    {
        $this->call('GET', '/post/1', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $this->assertNotEquals(404, $this->response->getStatusCode());

        $this->call('POST', '/posts', [], [], [], ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json']);
        $this->assertNotEquals(404, $this->response->getStatusCode());

        $this->call('PUT', '/post/1', [], [], [], ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json']);
        $this->assertNotEquals(404, $this->response->getStatusCode());

        $this->call('DELETE', '/post/1', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $this->assertNotEquals(404, $this->response->getStatusCode());
    }

    public function test_index_without_accept_returns_406(): void
    {
        $this->call('GET', '/posts');
        $this->assertEquals(406, $this->response->getStatusCode());
        $this->assertEquals('Not Acceptable!', $this->response->getContent());
    }

    public function test_index_with_accept_json_returns_json(): void
    {
        $this->call('GET', '/posts', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertStringContainsString('application/json', $this->response->headers->get('Content-Type'));
    }

    public function test_index_with_accept_xml_returns_xml(): void
    {
        $this->call('GET', '/posts', [], [], [], ['HTTP_ACCEPT' => 'application/xml']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('application/xml', $this->response->headers->get('Content-Type'));
    }
}
