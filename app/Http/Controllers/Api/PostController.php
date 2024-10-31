<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return response()->json(['data' => $posts], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $validateUser = Validator::make($request->all(), 
        [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image',
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }
    
        $imagePath = $request->file('image')->store('images', 'public');
    
        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => 'storage/'.$imagePath,
            'created_by' => auth('sanctum')->user()->id,
        ]);
    
        return response()->json(['data' => $post, 'message' => 'Post created successfully'], 201);
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json(['data' => $post], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
        $validateUser = Validator::make($request->all(), 
        [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'sometimes|image',
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }
    
        try {

            $post = Post::FindOrFail($id);
            $post->title = $request->title;
            $post->description = $request->description;
            $post->created_by =  auth('sanctum')->user()->id;
           
            
            $data = $request->only(['title', 'description', 'created_by']);

            // dd($request->all());
        
            if ($request->hasFile('image')) {
                $post->image = 'storage/'.$request->file('image')->store('images', 'public');
            }
            
        
            $post->update();
        
            return response()->json(['data' => $post, 'message' => 'Post updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::FindOrFail($id);
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
