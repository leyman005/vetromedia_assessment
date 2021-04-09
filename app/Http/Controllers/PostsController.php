<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\USER;
use Session;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['index','updateRate']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('updated_at', 'DESC')->get();

        return view('posts/index')->with('posts',$posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loggedInUserId = Auth::user()->id;

        $this->validate($request, array(
            'title'       => 'required|max:255',
            'description' => 'required'
        ));
        
        $post = new Post();
        $post->title = $request->title;
        $post->body = $request->description;
        $post->user_id = $loggedInUserId;
        $post->save();

        Session::flash('success', 'The blog post was successfully created!');

        return redirect()->route('posts.index',$post->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        return view('posts.show')->with('post',$post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id)->first();
        return view('posts.edit')->with('post',$post);
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
        $this->validate($request, array(
            'title'       => 'required|max:255',
            'description' => 'required'
        ));
        
        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->body = $request->input('description');
        $post->save();

        Session::flash('success', 'The blog post was successfully updated!');

        return redirect()->route('posts.show',$post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();

        Session::flash('success', 'The blog post was successfully deleted!');

        return redirect()->route('posts.index');
    }

    public function updateRate()
    {
        $rate = $_POST['rate'];
        $id = $_POST['id'];
        $post = Post::find($id);
        $post->rate = $rate;
        $post->save();

        Session::flash('success', 'The blog post was successfully updated!');

        return redirect()->route('posts.show',$post->id);
    }

    public function getRate()
    {
        $posts = Post::get();
        echo json_encode($posts);
    }

}
