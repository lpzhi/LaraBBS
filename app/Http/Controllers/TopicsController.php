<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function uploadImage(ImageUploadHandler $uploadHandler,Request $request)
    {
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];

        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploadHandler->save($request->upload_file, 'topics', \Auth::id(), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }

    public function index(Request $request)
    {
        $topics = Topic::with('user', 'category')->withOrder($request->order)->paginate(20);
        return view('topics.index', compact('topics'));
    }

    public function show(Topic $topic)
    {

        return view('topics.show', compact('topic'));
    }

	public function create(Request $request,Topic $topic)
	{
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(TopicRequest $request,Topic $topic)
	{
        $topic->fill($request->all());
	    $topic->user_id = Auth::id();
	    $topic->save();

		return redirect()->route('topics.show', $topic->id)->with('message', '新建话题成功');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->route('topics.show', $topic->id)->with('message', 'Updated successfully.');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', 'Deleted successfully.');
	}
}