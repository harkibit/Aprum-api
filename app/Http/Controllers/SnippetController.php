<?php

namespace App\Http\Controllers;

use App\Models\Snippet;
use App\Models\Version;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SnippetController extends Controller
{
    //
    private $client;
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
        $this->client = new Client();
    }

    public function index()
    {
        return Snippet::with(['user', 'version', 'version.language'])->where('public', true)->orderByDesc('id')->paginate(6);
    }

    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'string|required',
            'body' => 'string|required',
            'public' => 'required|boolean',
            'version_id' => 'required|integer',
            'description' => 'string|nullable'
        ]);
        $version = Version::findOrFail($request->input('version_id'));

        $snippet = new Snippet;
        $snippet->title = $request->input('title');
        $snippet->body = $request->input('body');
        $snippet->public = $request->input('public');
        $snippet->slug = Str::slug($request->input('title')) . '-' . uniqid();
        $snippet->description = $request->input('description');

        $snippet->user()->associate(auth()->user());
        $snippet->version()->associate($version);

        $snippet->save();

        return response()->json([
            'message' => 'Snippet created successfully',
            'snippet' => $snippet
        ]);

    }

    public function update(): JsonResponse
    {
        return 0;
    }

    public function destroy(Request $request, $slug): JsonResponse
    {
        auth()->user()->snippets()->where('slug', $slug)->firstOrFail()->delete();

        return response()->json([
            'message' => 'Snippet deleted successfully'
        ]);
    }

    public function execute(Request $request)
    {
        $this->validate($request, [
           'body' => 'string|required',
           'language_code' => 'required',
           'version_index' => 'required'
        ]);

        $params = [
            'script' => $request->input('body'),
            'language' => $request->input('language_code'),
            'stdin' => $request->input('stdin'),
            'versionIndex' => $request->input('version_index'),
            'clientId' => env('JDOODLE_CLIENT_ID', ''),
            'clientSecret' => env('JDOODLE_CLIENT_SECRET', '')
        ];

        try{
            $response = $this->client->post('https://api.jdoodle.com/v1/execute', ['json' => $params ]);
            $response = json_decode($response->getBody());
            return response()->json([
                'output' => $response->output,
                'memory' => $response->memory,
                'cpuTime' => $response->cpuTime
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }

    }

}
