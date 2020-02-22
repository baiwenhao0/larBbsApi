<?php

namespace App\Http\Controllers\Api;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Queries\TopicQuery;
use App\Http\Resources\TopicResource;
use App\Http\Requests\Api\TopicRequest;
//use Spatie\QueryBuilder\QueryBuilder;
//use Spatie\QueryBuilder\AllowedFilter;
//use App\Http\Controllers\Controller;

class TopicsController extends Controller
{

    public function index(Request $request, TopicQuery $query)
    {
        $topics = $query->paginate();

        return TopicResource::collection($topics);
    }
    //
    public function store(TopicRequest $request, Topic $topic)
    {
//        return $this->errorResponse(403, '您还没有通过认证', 1003);
        $topic->fill($request->all());
        $topic->user_id = $request->user()->id;
        $topic->save();

        return new TopicResource($topic);
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);

        $topic->update($request->all());
        return new TopicResource($topic);
    }
    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);

        $topic->delete();

        return response(null, 204);
    }

    //获取用户发布的话题，按创建时间倒叙返回该用户所有的话题。
    public function userIndex(Request $request, User $user, TopicQuery $query)
    {
        $topics = $query->where('user_id', $user->id)->paginate();

        return TopicResource::collection($topics);
    }

    public function show($topicId, TopicQuery $query)
    {

        $topic = $query->findOrFail($topicId);
        return new TopicResource($topic);

        //必须使用 QueryBuilder 查询数据才可以利用 include 参数，而这里的 $topic 已经通过路由模型绑定查询出来了
        $topic = QueryBuilder::for(Topic::class)
            ->allowedIncludes('user', 'category')
            ->findOrFail($topicId);

        return new TopicResource($topic);
    }
//    public function show(Topic $topic)
//    {
//        return new TopicResource($topic);
//    }
}
