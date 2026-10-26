<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    private function resolveResponse(Request $request, array $data, int $status = 200)
    {
        if ($request->header('Accept') === 'application/xml') {
            $xml = new \SimpleXMLElement('<response/>');
            $this->arrayToXml($data, $xml);
            return response($xml->asXML(), $status, ['Content-Type' => 'application/xml']);
        }

        return response()->json($data, $status);
    }

    private function arrayToXml(array $data, \SimpleXMLElement &$xml)
    {
        foreach ($data as $key => $value) {
            $keyName = is_numeric($key) ? 'item' : $key;
            if (is_array($value)) {
                $subnode = $xml->addChild($keyName);
                $this->arrayToXml($value, $subnode);
            } else {
                $xml->addChild($keyName, htmlspecialchars((string) $value));
            }
        }
    }

    public function index(Request $request = null)
    {
        $request = $request ?: request();
        $accept = $request->header('Accept');
        if (!in_array($accept, ['application/json', 'application/xml'])) {
            return response('Not Acceptable!', 406);
        }

        $posts = Post::orderBy('id', 'desc')->paginate(10);
        return $this->resolveResponse($request, [
            'message' => 'posts',
            'results' => $posts->toArray()
        ]);
    }

    public function show(Request $request, $id)
    {
        $accept = $request->header('Accept');
        if (!in_array($accept, ['application/json', 'application/xml'])) {
            return response('Not Acceptable!', 406);
        }

        $post = Post::findOrFail($id);
        return $this->resolveResponse($request, [
            'message' => 'post',
            'result'  => $post->toArray()
        ]);
    }

    public function store(Request $request)
    {
        $accept = $request->header('Accept');
        if (!in_array($accept, ['application/json', 'application/xml'])) {
            return response('Not Acceptable!', 406);
        }

        $contentType = $request->header('Content-Type');
        if ($contentType !== 'application/json' && !str_starts_with((string)$contentType, 'application/json')) {
            return response('Unsupported Media Type', 415);
        }

        $this->validate($request, [
            'title'   => 'required|string',
            'content' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        $post = Post::create($request->only(['title', 'content', 'user_id', 'status']));
        return $this->resolveResponse($request, [
            'message' => 'Post created',
            'result'  => $post->toArray()
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $accept = $request->header('Accept');
        if (!in_array($accept, ['application/json', 'application/xml'])) {
            return response('Not Acceptable!', 406);
        }

        $contentType = $request->header('Content-Type');
        if ($contentType !== 'application/json' && !str_starts_with((string)$contentType, 'application/json')) {
            return response('Unsupported Media Type', 415);
        }

        $post = Post::findOrFail($id);
        $post->update($request->only(['title', 'content', 'status', 'user_id']));

        return $this->resolveResponse($request, [
            'message' => 'Post updated',
            'result'  => $post->fresh()->toArray()
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $accept = $request->header('Accept');
        if (!in_array($accept, ['application/json', 'application/xml'])) {
            return response('Not Acceptable!', 406);
        }

        $post = Post::findOrFail($id);
        $post->delete();
        return $this->resolveResponse($request, [
            'message' => 'Post deleted'
        ]);
    }
}
