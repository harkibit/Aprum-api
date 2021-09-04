<?php

namespace App\Http\Controllers;

use App\Models\Snippet;
use App\Models\Version;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SnippetController extends Controller
{
    //
    private $client;
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->client = new Client();
    }

    public function index()
    {
        return Snippet::with(['user', 'version', 'version.language'])->where('public', true)->orderByDesc('id')->paginate(6);
    }

    public function show(Request $request, $slug)
    {
        $snippet = Snippet::with(['user', 'version', 'version.language'])->where('slug', $slug)->firstOrFail();
        if(!$snippet->exists()){
            return response()->json([
                'message' => 'Snippet not found',
            ], 404);
        }elseif (!$snippet->public && ($snippet->user->id !== auth()->id())){
            return response()->json([
                'message' => 'You\'re not allowed to fetch this snippet'
            ], 403);
        }
        return response()->json([
            'owner' => $snippet->user->id === auth()->id(),
            'snippet' => $snippet
        ]);

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
        $snippet->slug = uniqid();
        $snippet->description = $request->input('description');

        $snippet->user()->associate(auth()->user());
        $snippet->version()->associate($version);

        $snippet->save();

        return response()->json([
            'message' => 'Snippet created successfully',
            'snippet' => $snippet
        ]);

    }

    public function update(Request $request, $slug): JsonResponse
    {
        $this->validate($request, [
            'title' => 'string|required',
            'body' => 'string|required',
            'public' => 'required|boolean',
            'version_id' => 'required|integer',
            'description' => 'string|nullable'
        ]);

        auth()->user()->snippets()->where('slug', $slug)->update([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'public' => $request->input('public'),
            'version_id' => $request->input('version_id'),
            'description' => $request->input('description')
        ]);

        return response()->json([
            'message' => 'Snippet updated successfully'
        ]);
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
