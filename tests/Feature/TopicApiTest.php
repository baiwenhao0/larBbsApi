<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Topic;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TopicApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    /**
     * A basic feature test example.
     *
     * @return void
     */
//    public function testExample()
//    {
//        $response = $this->get('/');
//
//        $response->assertStatus(200);
//    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    public function testStoreTopic()
    {
        $data = ['category_id' => 1, 'body' => 'test body', 'title' => 'test title'];

//        $token = auth('api')->fromUser($this->user);
//        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])
//            ->json('POST', '/api/v1/topics', $data);
        $response = $this->JWTActingAs($this->user)
            ->json('POST', '/api/v1/topics', $data);

        $assertData = [
            'category_id' => 1,
            'user_id' => $this->user->id,
            'title' => 'test title',
            'body' => clean('test body', 'user_topic_body'),
        ];

        $response->assertStatus(201)
            ->assertJsonFragment($assertData);
    }
}
