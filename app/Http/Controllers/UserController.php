<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Get user data from specific username.
     *
     * @param  string $username
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        return DB::table('users')
            ->where('username', $username)
            ->get();
    }

    /**
     * Get all feeds from specific user
     * @param string $username
     */
    public function showFeed($username)
    {
        return DB::table('users')
            ->join('user_feeds', 'users.id', '=', 'user_feeds.user_id')
            ->join('feeds', 'user_feeds.feed_id', '=', 'feeds.id')
            ->where('users.username', $username)
            ->select('feeds.id', 'feeds.name', 'feeds.thumbnail_30')
            ->get();
    }

    /**
     * Get episodes from feedId
     * @param string $username
     * @param integer $feedId
     */
    public function showEpisodes($username, $feedId)
    {
        return DB::table('users')
            ->join('user_feeds', function($join) use ($feedId) {
                $join->on('users.id', '=', 'user_feeds.user_id')
                    ->where('user_feeds.feed_id', '=', $feedId);
            })
            ->join('feeds', 'user_feeds.feed_id', '=', 'feeds.id')
            ->join('user_episodes', 'user_feeds.id','=', 'user_episodes.user_feed_id')
            ->join('episodes', 'episodes.id', '=', 'user_episodes.id')
            ->where('users.username', $username)
            ->select(
                'feeds.name as feed_name',
                'episodes.title as episode_title',
                'user_episodes.heard',
                'user_episodes.hide',
                'user_episodes.downloaded',
                'user_episodes.paused_at',
                'episodes.media_url',
                'episodes.media_type',
                'episodes.published_date',
                'episodes.content'
            )
            ->get();
    }
}
