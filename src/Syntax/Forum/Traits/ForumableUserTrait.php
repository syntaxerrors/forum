<?php namespace Syntax\Core;

class ForumableUserTrait {

    public function posts()
    {
        return $this->hasMany('Forum_Post', user_id);
    }

    public function replies()
    {
        return $this->hasMany('Forum_Reply', user_id);
    }

	/**
	 * See if there are unread posts in a certain forum board
	 *
	 * @param  int $boardId A forum board Id
	 *
	 * @return boolean
	 */
	public function checkUnreadBoard($boardId)
	{
		// Future version
		// return Forum_Board::where('id', '=', $boardId)->or_where('parent_id', '=', $boardId)->get()->unreadFlagForUser($this->id);

		// Get all parent and child boards matching the id
		$boardIds   = Forum_Board::where('uniqueId', $boardId)->orWhere('parent_id', '=', $boardId)->get()->id->toArray();

		// Get any posts within those boards
		$posts    = Forum_Post::whereIn('forum_board_id', $boardIds)->get();
		$postIds  = $posts->id->toArray();

		// Make sure there are posts
		if (count($postIds) > 0) {

			// See which of these posts the user has already viewed
			$viewedPosts = Forum_Post_View::where('user_id', '=', $this->id)->whereIn('forum_post_id', $postIds)->get();

			// If the posts are greater than the viewed, there are new posts
			if (count($posts) > count($viewedPosts)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get the number of unread posts
	 *
	 * @return int
	 */
	public function unreadPostCount()
	{
		// Get the id of all posts
		$posts      = Forum_Post::all();
		$postsCount = $posts->count();

		if ($postsCount > 0) {
			foreach ($posts as $key => $post) {
				if ($post->board->forum_board_type_id == Forum_Board::TYPE_GM && !$this->checkPermission('GAME_MASTER')) {
					unset($posts[$key]);
				}
			}
			$postIds = $posts->id->toArray();

			// See which of these the user has viewed
			$viewedPostCount = Forum_Post_View::where('user_id', $this->id)->whereIn('forum_post_id', $postIds)->count();

			// If there are more posts than viewed posts, return the remainder
			if ($postsCount > $viewedPostCount) {
				return $postsCount - $viewedPostCount;
			}
		}
		return 0;
	}
}