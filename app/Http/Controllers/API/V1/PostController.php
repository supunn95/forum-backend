<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostStatusRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

    public function getAllPosts()
    {
        $user = Auth::user();
        $posts_query = Post::with('user');

        if ($user->hasRole('user')) {

            $posts_query->where('status', '=', 'approved');

        }
        $posts = $posts_query->orderBy('id', 'DESC')->get();

        return PostResource::collection($posts);
    }

    public function getApprovedPosts()
    {

        $posts = Post::with('user')->where('status', '=', 'approved')->get();
        return PostResource::collection($posts);

    }

    public function changePostStatus(UpdatePostStatusRequest $request)
    {

        try {

            $user = Auth::user();

            DB::beginTransaction();

            if ($user->hasRole('admin')) {
                $post = Post::where('id', '=', $request->id)->first();
                $post->status = $request->status;
                $post->save();
            } else {
                return response()->json('Unauthorized', 401);
            }

            DB::commit();

            return response()->json('Successfully updated!', 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 409);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 409);

        }

    }

    public function createPost(StorePostRequest $request)
    {

        try {

            DB::beginTransaction();

            $user = Auth::user()->load('roles');
            $input_data = $request->all();
            $status = 'pending';

            if ($user->hasRole('admin')) {

                $status = 'approved';
            } else {

                $status = 'pending';
            }

            $input_arr = array_merge($input_data, ['status' => $status, 'user_id' => $user->id]);

            $post = Post::create($input_arr);

            DB::commit();

            return response()->json('Successfully created!', 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 409);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 409);

        }

    }

    public function deletePost($id)
    {

        try {

            DB::beginTransaction();

            $post = Post::where('id', '=', $id)->first();

            $post->delete();

            DB::commit();

            return response()->json('Successfully deleted!', 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 409);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 409);

        }

    }

    public function searchPost(Request $request)
    {

        $user = Auth::user();
        $posts_query = Post::with('user');
        $search_string = $request->keyword;

        if ($user->hasRole('user')) {

            $posts_query->where('status', '=', 'approved');

        }

        if (!is_null($search_string)) {

            $posts_query->where(function ($query) use ($search_string) {
                $query->where('description', 'LIKE', '%' . $search_string . '%');

                $query->orWhereHas('user', function ($q) use ($search_string) {
                    $q->where(function ($q) use ($search_string) {
                        $q->orWhere('name', 'LIKE', '%' . $search_string . '%');
                    });
                });

            });

        }

        $posts = $posts_query->orderBy('id', 'DESC')->get();

        return PostResource::collection($posts);

    }

}
