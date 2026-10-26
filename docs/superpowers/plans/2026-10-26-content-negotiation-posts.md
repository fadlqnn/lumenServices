# Content Negotiation — PostsController Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implementasikan Content Negotiation lengkap pada `PostsController` — validasi `Accept` dan `Content-Type` header, serta response format JSON/XML — untuk semua endpoint Posts sesuai modul PDF "PHP Lumen Response API".

**Architecture:** Semua logika content negotiation tinggal di `PostsController`; helper `resolveResponse()` private menghandle pemilihan format JSON vs XML agar tidak duplikat antar method. Routes baru ditambahkan di `routes/web.php`. Satu test file `PostsControllerTest.php` mencakup semua tantangan.

**Tech Stack:** PHP 8.1, Lumen 10, PHPUnit 10, `SimpleXMLElement` (built-in PHP), Lumen Testing (`$this->call()` dengan custom headers).

**Spec:** `D:\vsc\lumen\ServiceApp\docs\PHP Lumen Response API.pdf` — halaman 1–11 (khususnya halaman 11: Tantangan 1–3).

---

## Global Constraints

- PHP ≥ 8.1; Lumen ≥ 10.0 — tidak boleh install dependency baru.
- Hanya `SimpleXMLElement` untuk XML (sudah built-in).
- Accept Header yang valid: `application/json` ATAU `application/xml`.
- Content-Type Header yang valid (untuk POST/PUT): `application/json` saja.
- Jika Accept Header tidak valid/tidak ada → HTTP `406 Not Acceptable`.
- Jika Content-Type Header tidak valid/tidak ada pada POST/PUT → HTTP `415 Unsupported Media Type`.
- Routes mengikuti pola yang ditetapkan PDF: `/posts` (collection) dan `/post/{postId}` (resource).
- Test menggunakan `Tests\TestCase` (`Laravel\Lumen\Testing\TestCase`) yang sudah ada — tidak membuat base class baru.
- Semua test menggunakan `DatabaseTransactions` agar DB bersih tiap test.

---

## Review Focus

1. **Accept Header kosong vs. tidak ada** — keduanya harus menghasilkan `406`; Lumen mungkin meneruskan header kosong sebagai `""`.
2. **Accept Header `*/*`** — tidak disebutkan di PDF, jangan diterima kecuali diperintah (defaultnya 406).
3. **Body POST non-JSON saat Content-Type: application/json** — validasi header cukup, bukan parse body; tes harus konfirmasi tidak crash.
4. **Route `/post/{postId}` vs `/posts`** — pastikan plural/singular sesuai PDF dan tidak tabrakan.
5. **XML escaping** — data yang mengandung `<`, `>`, `&` dalam respons XML harus di-escape `SimpleXMLElement` secara otomatis; tambahkan satu tes string dengan karakter tersebut.

---

## Dasar Teori

### Content Negotiation

**Content Negotiation** adalah mekanisme HTTP di mana client dan server bernegosiasi tentang format data yang dipertukarkan melalui dua header:

| Header | Arah | Arti |
|---|---|---|
| `Accept` | Client → Server | Format **response** yang diinginkan client |
| `Content-Type` | Client → Server | Format **request body** yang dikirim client |

**Alur validasi:**
```
Request masuk
  ├─ Periksa Accept Header
  │    ├─ Tidak ada / tidak valid → 406 Not Acceptable
  │    └─ Valid (json/xml) → lanjut
  ├─ [Untuk POST/PUT] Periksa Content-Type Header
  │    ├─ Tidak ada / tidak valid → 415 Unsupported Media Type
  │    └─ Valid (json) → lanjut
  └─ Proses request → format response sesuai Accept
```

**`SimpleXMLElement`** adalah kelas PHP built-in untuk membuat/parse XML:
```php
$xml = new SimpleXMLElement('<response/>');
$xml->addChild('message', 'Post created');
echo $xml->asXML(); // <?xml version="1.0"?><response><message>Post created</message></response>
```

---

## File Structure

| Aksi | File | Tanggung Jawab |
|------|------|----------------|
| **Modify** | `app/Http/Controllers/PostsController.php` | Semua logika CRUD + content negotiation |
| **Modify** | `routes/web.php` | Tambah routes `show`, `store`, `update`, `destroy` untuk posts |
| **Create** | `tests/PostsControllerTest.php` | Semua test untuk 3 tantangan |

---

## Task 1: Routes + Helper `resolveResponse()`

**Dasar Teori:** Sebelum method apapun bisa diuji, routes harus terdaftar dan helper private untuk format response harus ada agar tidak duplikasi di setiap method.

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/PostsController.php`
- Create: `tests/PostsControllerTest.php`

**Interfaces:**
- Produces: `PostsController::resolveResponse(Request $request, array $data, int $status): \Illuminate\Http\Response`

---

- [ ] **Step 1: Tulis failing test — routes terdaftar**

  ```php
  // tests/PostsControllerTest.php
  <?php
  namespace Tests;

  use Laravel\Lumen\Testing\DatabaseTransactions;

  class PostsControllerTest extends TestCase
  {
      use DatabaseTransactions;

      public function test_routes_exist_for_posts_crud(): void
      {
          // GET /posts — index
          $this->call('GET', '/posts', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
          $this->assertNotEquals(404, $this->response->getStatusCode());

          // GET /post/1 — show
          $this->call('GET', '/post/1', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
          $this->assertNotEquals(404, $this->response->getStatusCode());

          // POST /posts — store
          $this->call('POST', '/posts', [], [], [], ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json']);
          $this->assertNotEquals(404, $this->response->getStatusCode());

          // PUT /post/1 — update
          $this->call('PUT', '/post/1', [], [], [], ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json']);
          $this->assertNotEquals(404, $this->response->getStatusCode());

          // DELETE /post/1 — destroy
          $this->call('DELETE', '/post/1', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
          $this->assertNotEquals(404, $this->response->getStatusCode());
      }
  }
  ```

- [ ] **Step 2: Jalankan test — pastikan FAIL**

  ```bash
  cd D:\vsc\lumen\ServiceApp
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter test_routes_exist_for_posts_crud -v
  ```
  Expected: FAIL — `404` karena routes belum ada.

- [ ] **Step 3: Tambah routes di `routes/web.php`**

  Tambahkan setelah baris `$router->get('/posts', ...)` yang sudah ada:

  ```php
  $router->get('/post/{id}',    'PostsController@show');
  $router->post('/posts',       'PostsController@store');
  $router->put('/post/{id}',    'PostsController@update');
  $router->delete('/post/{id}', 'PostsController@destroy');
  ```

  Tambahkan stub kosong di `PostsController.php` (belum ada logika):

  ```php
  use Illuminate\Http\Request;

  // method stubs:
  public function show(Request $request, $id) { return response('', 200); }
  public function store(Request $request) { return response('', 200); }
  public function update(Request $request, $id) { return response('', 200); }
  public function destroy(Request $request, $id) { return response('', 200); }
  ```

- [ ] **Step 4: Jalankan test — pastikan PASS**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter test_routes_exist_for_posts_crud -v
  ```

- [ ] **Step 5: Tulis failing test — `resolveResponse()` via `index`**

  ```php
  public function test_index_with_accept_json_returns_json_content_type(): void
  {
      $this->call('GET', '/posts', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
      $this->assertEquals(200, $this->response->getStatusCode());
      $this->assertStringContainsString('application/json', $this->response->headers->get('Content-Type'));
  }

  public function test_index_without_accept_header_returns_406(): void
  {
      $this->call('GET', '/posts');
      $this->assertEquals(406, $this->response->getStatusCode());
      $this->assertEquals('Not Acceptable!', $this->response->getContent());
  }

  public function test_index_with_accept_xml_returns_xml_content_type(): void
  {
      $this->call('GET', '/posts', [], [], [], ['HTTP_ACCEPT' => 'application/xml']);
      $this->assertEquals(200, $this->response->getStatusCode());
      $this->assertEquals('application/xml', $this->response->headers->get('Content-Type'));
  }
  ```

- [ ] **Step 6: Jalankan test — pastikan FAIL**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter "test_index" -v
  ```

- [ ] **Step 7: Implementasi `resolveResponse()` dan update `index()` di `PostsController.php`**

  ```php
  private function resolveResponse(Request $request, array $data, int $status = 200): \Illuminate\Http\Response
  {
      if ($request->header('Accept') === 'application/xml') {
          $xml = new \SimpleXMLElement('<response/>');
          array_walk_recursive($data, fn($v, $k) => $xml->addChild($k, htmlspecialchars((string) $v)));
          return response($xml->asXML(), $status, ['Content-Type' => 'application/xml']);
      }
      return response()->json($data, $status);
  }

  public function index(Request $request)
  {
      $accept = $request->header('Accept');
      if (!in_array($accept, ['application/json', 'application/xml'])) {
          return response('Not Acceptable!', 406);
      }
      $posts = Post::orderBy('id', 'desc')->paginate(10);
      return $this->resolveResponse($request, ['message' => 'posts', 'results' => $posts->items()]);
  }
  ```

- [ ] **Step 8: Jalankan test — pastikan PASS**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter "test_index" -v
  ```

- [ ] **Step 9: Commit**

  ```bash
  git add routes/web.php app/Http/Controllers/PostsController.php tests/PostsControllerTest.php
  git commit -m "feat: add posts routes, resolveResponse helper, update index with content negotiation"
  ```

---

## Task 2: Tantangan 1 — `store()` response format JSON/XML

**Dasar Teori:** Function `store` (POST) harus memvalidasi **dua header** (Accept + Content-Type) DAN menghasilkan response dalam format yang sesuai Accept Header (JSON atau XML). Ini adalah Tantangan 1 dari PDF halaman 11.

**Files:**
- Modify: `app/Http/Controllers/PostsController.php` (method `store`)
- Modify: `tests/PostsControllerTest.php`

**Interfaces:**
- Consumes: `resolveResponse()` dari Task 1
- Consumes: `POST /posts` route dari Task 1

---

- [ ] **Step 1: Tulis failing tests — `store` header validations**

  ```php
  public function test_store_without_accept_header_returns_406(): void
  {
      $this->call('POST', '/posts', [], [], [],
          ['CONTENT_TYPE' => 'application/json'],
          '{"title":"T","body":"B","user_id":1}'
      );
      $this->assertEquals(406, $this->response->getStatusCode());
      $this->assertEquals('Not Acceptable!', $this->response->getContent());
  }

  public function test_store_with_accept_but_without_content_type_returns_415(): void
  {
      $this->call('POST', '/posts', [], [], [],
          ['HTTP_ACCEPT' => 'application/json'],
          '{"title":"T","body":"B","user_id":1}'
      );
      $this->assertEquals(415, $this->response->getStatusCode());
      $this->assertEquals('Unsupported Media Type', $this->response->getContent());
  }

  public function test_store_with_valid_headers_returns_json_201(): void
  {
      $payload = json_encode(['title' => 'Test Post', 'body' => 'Body', 'user_id' => 1]);
      $this->call('POST', '/posts', [], [], [],
          ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
          $payload
      );
      $this->assertEquals(201, $this->response->getStatusCode());
      $data = json_decode($this->response->getContent(), true);
      $this->assertEquals('Post created', $data['message']);
  }

  public function test_store_with_accept_xml_returns_xml_201(): void
  {
      $payload = json_encode(['title' => 'XML Post', 'body' => 'Body', 'user_id' => 1]);
      $this->call('POST', '/posts', [], [], [],
          ['HTTP_ACCEPT' => 'application/xml', 'CONTENT_TYPE' => 'application/json'],
          $payload
      );
      $this->assertEquals(201, $this->response->getStatusCode());
      $this->assertEquals('application/xml', $this->response->headers->get('Content-Type'));
      $this->assertStringContainsString('<message>Post created</message>', $this->response->getContent());
  }
  ```

- [ ] **Step 2: Jalankan test — pastikan FAIL**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter "test_store" -v
  ```

- [ ] **Step 3: Implementasi `store()` di `PostsController.php`**

  ```php
  public function store(Request $request)
  {
      $accept = $request->header('Accept');
      if (!in_array($accept, ['application/json', 'application/xml'])) {
          return response('Not Acceptable!', 406);
      }
      if ($request->header('Content-Type') !== 'application/json') {
          return response('Unsupported Media Type', 415);
      }

      $this->validate($request, [
          'title'   => 'required|string',
          'body'    => 'required|string',
          'user_id' => 'required|integer',
      ]);

      $post = Post::create($request->only(['title', 'body', 'user_id']));
      return $this->resolveResponse($request, ['message' => 'Post created', 'result' => $post->toArray()], 201);
  }
  ```

  Pastikan di `Post` model: `protected $fillable = ['title', 'body', 'user_id'];`

- [ ] **Step 4: Jalankan test — pastikan PASS**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter "test_store" -v
  ```

- [ ] **Step 5: Commit**

  ```bash
  git add app/Http/Controllers/PostsController.php app/Models/Post.php tests/PostsControllerTest.php
  git commit -m "feat(tantangan-1): store() validates Accept+ContentType, returns JSON or XML"
  ```

---

## Task 3: Tantangan 2 — Accept Header pada `show()` dan `destroy()`

**Dasar Teori:** `show` (GET resource tunggal) dan `destroy` (DELETE) harus memvalidasi Accept Header. Response dikembalikan dalam format JSON atau XML sesuai Accept. Tantangan 2 dari PDF halaman 11.

**Files:**
- Modify: `app/Http/Controllers/PostsController.php` (methods `show`, `destroy`)
- Modify: `tests/PostsControllerTest.php`

**Interfaces:**
- Consumes: `resolveResponse()` dari Task 1

---

- [ ] **Step 1: Tulis failing tests — `show()`**

  ```php
  public function test_show_without_accept_header_returns_406(): void
  {
      $this->call('GET', '/post/1');
      $this->assertEquals(406, $this->response->getStatusCode());
  }

  public function test_show_with_accept_json_returns_post_as_json(): void
  {
      $post = \App\Models\Post::factory()->create();
      $this->call('GET', "/post/{$post->id}", [], [], [], ['HTTP_ACCEPT' => 'application/json']);
      $this->assertEquals(200, $this->response->getStatusCode());
      $data = json_decode($this->response->getContent(), true);
      $this->assertEquals($post->id, $data['result']['id']);
  }

  public function test_show_with_accept_xml_returns_xml(): void
  {
      $post = \App\Models\Post::factory()->create();
      $this->call('GET', "/post/{$post->id}", [], [], [], ['HTTP_ACCEPT' => 'application/xml']);
      $this->assertEquals(200, $this->response->getStatusCode());
      $this->assertEquals('application/xml', $this->response->headers->get('Content-Type'));
  }
  ```

- [ ] **Step 2: Jalankan test — pastikan FAIL**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter "test_show" -v
  ```

- [ ] **Step 3: Implementasi `show()` di `PostsController.php`**

  ```php
  public function show(Request $request, $id)
  {
      $accept = $request->header('Accept');
      if (!in_array($accept, ['application/json', 'application/xml'])) {
          return response('Not Acceptable!', 406);
      }
      $post = Post::findOrFail($id);
      return $this->resolveResponse($request, ['message' => 'post', 'result' => $post->toArray()]);
  }
  ```

- [ ] **Step 4: Jalankan test — pastikan PASS**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter "test_show" -v
  ```

- [ ] **Step 5: Tulis failing tests — `destroy()`**

  ```php
  public function test_destroy_without_accept_header_returns_406(): void
  {
      $this->call('DELETE', '/post/1');
      $this->assertEquals(406, $this->response->getStatusCode());
  }

  public function test_destroy_with_accept_json_deletes_and_returns_json(): void
  {
      $post = \App\Models\Post::factory()->create();
      $this->call('DELETE', "/post/{$post->id}", [], [], [], ['HTTP_ACCEPT' => 'application/json']);
      $this->assertEquals(200, $this->response->getStatusCode());
      $data = json_decode($this->response->getContent(), true);
      $this->assertEquals('Post deleted', $data['message']);
  }
  ```

- [ ] **Step 6: Jalankan test — pastikan FAIL**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter "test_destroy" -v
  ```

- [ ] **Step 7: Implementasi `destroy()` di `PostsController.php`**

  ```php
  public function destroy(Request $request, $id)
  {
      $accept = $request->header('Accept');
      if (!in_array($accept, ['application/json', 'application/xml'])) {
          return response('Not Acceptable!', 406);
      }
      Post::findOrFail($id)->delete();
      return $this->resolveResponse($request, ['message' => 'Post deleted']);
  }
  ```

- [ ] **Step 8: Jalankan test — pastikan PASS**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter "test_destroy" -v
  ```

- [ ] **Step 9: Commit**

  ```bash
  git add app/Http/Controllers/PostsController.php tests/PostsControllerTest.php
  git commit -m "feat(tantangan-2): show() and destroy() validate Accept header, return JSON or XML"
  ```

---

## Task 4: Tantangan 2+3 — `update()` Accept + Content-Type

**Dasar Teori:** `update()` menggabungkan dua tantangan: validasi Accept Header (Tantangan 2) dan validasi Content-Type Header (Tantangan 3). Sama seperti `store()` tapi untuk update resource yang ada. Tantangan 2+3 dari PDF halaman 11.

**Files:**
- Modify: `app/Http/Controllers/PostsController.php` (method `update`)
- Modify: `tests/PostsControllerTest.php`

**Interfaces:**
- Consumes: `resolveResponse()` dari Task 1
- Consumes: route `PUT /post/{id}` dari Task 1

---

- [ ] **Step 1: Tulis failing tests — `update()`**

  ```php
  public function test_update_without_accept_header_returns_406(): void
  {
      $this->call('PUT', '/post/1', [], [], [], ['CONTENT_TYPE' => 'application/json'], '{}');
      $this->assertEquals(406, $this->response->getStatusCode());
  }

  public function test_update_with_accept_but_no_content_type_returns_415(): void
  {
      $this->call('PUT', '/post/1', [], [], [], ['HTTP_ACCEPT' => 'application/json'], '{}');
      $this->assertEquals(415, $this->response->getStatusCode());
  }

  public function test_update_with_valid_headers_returns_updated_post_as_json(): void
  {
      $post = \App\Models\Post::factory()->create(['title' => 'Old Title']);
      $payload = json_encode(['title' => 'New Title']);
      $this->call('PUT', "/post/{$post->id}", [], [], [],
          ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
          $payload
      );
      $this->assertEquals(200, $this->response->getStatusCode());
      $data = json_decode($this->response->getContent(), true);
      $this->assertEquals('Post updated', $data['message']);
      $this->assertEquals('New Title', $data['result']['title']);
  }

  public function test_update_with_accept_xml_returns_xml_response(): void
  {
      $post = \App\Models\Post::factory()->create();
      $this->call('PUT', "/post/{$post->id}", [], [], [],
          ['HTTP_ACCEPT' => 'application/xml', 'CONTENT_TYPE' => 'application/json'],
          json_encode(['title' => 'Updated XML'])
      );
      $this->assertEquals(200, $this->response->getStatusCode());
      $this->assertEquals('application/xml', $this->response->headers->get('Content-Type'));
      $this->assertStringContainsString('<message>Post updated</message>', $this->response->getContent());
  }
  ```

- [ ] **Step 2: Jalankan test — pastikan FAIL**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter "test_update" -v
  ```

- [ ] **Step 3: Implementasi `update()` di `PostsController.php`**

  ```php
  public function update(Request $request, $id)
  {
      $accept = $request->header('Accept');
      if (!in_array($accept, ['application/json', 'application/xml'])) {
          return response('Not Acceptable!', 406);
      }
      if ($request->header('Content-Type') !== 'application/json') {
          return response('Unsupported Media Type', 415);
      }

      $post = Post::findOrFail($id);
      $post->update($request->only(['title', 'body']));
      return $this->resolveResponse($request, ['message' => 'Post updated', 'result' => $post->fresh()->toArray()]);
  }
  ```

- [ ] **Step 4: Jalankan test — pastikan PASS**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/PostsControllerTest.php --filter "test_update" -v
  ```

- [ ] **Step 5: Jalankan full suite — semua harus hijau**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/ -v
  ```

- [ ] **Step 6: Commit final**

  ```bash
  git add app/Http/Controllers/PostsController.php tests/PostsControllerTest.php
  git commit -m "feat(tantangan-3): update() validates Accept + Content-Type, returns JSON or XML"
  ```

---

## Task 5: Verifikasi Final

- [ ] **Step 1: Jalankan full test suite**

  ```bash
  php vendor/phpunit/phpunit/phpunit tests/ -v
  ```
  Expected: Semua test PASS, tidak ada warning.

- [ ] **Step 2: Manual verification di Postman**

  | Request | Expected Status | Expected Body |
  |---------|----------------|---------------|
  | GET `/posts` (no header) | 406 | `Not Acceptable!` |
  | GET `/posts` Accept: json | 200 | JSON |
  | GET `/posts` Accept: xml | 200 | XML |
  | POST `/posts` (no header) | 406 | `Not Acceptable!` |
  | POST `/posts` Accept: json (no Content-Type) | 415 | `Unsupported Media Type` |
  | POST `/posts` Accept: json, Content-Type: json | 201 | JSON |
  | POST `/posts` Accept: xml, Content-Type: json | 201 | XML |
  | PUT `/post/{id}` (no header) | 406 | `Not Acceptable!` |
  | PUT `/post/{id}` Accept: json (no Content-Type) | 415 | `Unsupported Media Type` |
  | DELETE `/post/{id}` (no header) | 406 | `Not Acceptable!` |
  | DELETE `/post/{id}` Accept: xml | 200 | XML |

- [ ] **Step 3: Final commit**

  ```bash
  git add .
  git commit -m "chore: content negotiation complete — all 3 tantangan implemented and tested"
  ```
