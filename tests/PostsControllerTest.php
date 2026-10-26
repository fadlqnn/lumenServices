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

    // ─── Task 2: Tantangan 1 — store() ────────────────────────────────────────

    public function test_store_without_accept_returns_406(): void
    {
        $this->call('POST', '/posts', [], [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'],
            '{"title":"T","content":"B","user_id":1}'
        );
        $this->assertEquals(406, $this->response->getStatusCode());
        $this->assertEquals('Not Acceptable!', $this->response->getContent());
    }

    public function test_store_with_accept_no_content_type_returns_415(): void
    {
        $this->call('POST', '/posts', [], [], [],
            ['HTTP_ACCEPT' => 'application/json'],
            '{"title":"T","content":"B","user_id":1}'
        );
        $this->assertEquals(415, $this->response->getStatusCode());
        $this->assertEquals('Unsupported Media Type', $this->response->getContent());
    }

    public function test_store_valid_headers_returns_json_201(): void
    {
        $payload = json_encode(['title' => 'Test Post', 'content' => 'Body text', 'user_id' => 1]);
        $this->call('POST', '/posts', [], [], [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'],
            $payload
        );
        $this->assertEquals(201, $this->response->getStatusCode());
        $this->assertStringContainsString('application/json', $this->response->headers->get('Content-Type'));
        $data = json_decode($this->response->getContent(), true);
        $this->assertEquals('Post created', $data['message']);
        $this->assertEquals('Test Post', $data['result']['title']);
    }

    public function test_store_accept_xml_returns_xml_201(): void
    {
        $payload = json_encode(['title' => 'XML Post', 'content' => 'Body text', 'user_id' => 1]);
        $this->call('POST', '/posts', [], [], [],
            ['HTTP_ACCEPT' => 'application/xml', 'CONTENT_TYPE' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'],
            $payload
        );
        $this->assertEquals(201, $this->response->getStatusCode());
        $this->assertEquals('application/xml', $this->response->headers->get('Content-Type'));
        $this->assertStringContainsString('<message>Post created</message>', $this->response->getContent());
        $this->assertStringContainsString('<title>XML Post</title>', $this->response->getContent());
    }
}
