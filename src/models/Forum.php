<?php
namespace Syntax\Core;

class Forum extends \BaseModel
{
	/********************************************************************
	 * Declarations
	 *******************************************************************/

	/********************************************************************
	 * Aware validation rules
	 *******************************************************************/

	/********************************************************************
	 * Relationships
	 *******************************************************************/

	/********************************************************************
	 * Getter and Setter methods
	 *******************************************************************/

	/********************************************************************
	 * Extra Methods
	 *******************************************************************/

	public function getForumTypes()
	{
		$class = get_called_class();

		switch ($class) {
			case 'Forum_Post':
			case 'Syntax\Core\Forum_Post':
				return Forum_Post_Type::orderByNameAsc()->get();
			break;
			case 'Forum_Reply':
			case 'Syntax\Core\Forum_Reply':
				return Forum_Reply_Type::orderByNameAsc()->get();
			break;
		}
	}

	public function setModeration($reason)
	{
		if ($this instanceof Forum_Post || $this instanceof Forum_Reply) {
			// Set this as locked for moderation
			$this->moderatorLockedFlag = 1;
			$this->save();

			// Create the moderation record
			$report                = new Forum_Moderation;
			$report->resource_type = get_called_class();
			$report->resource_id   = $this->id;
			$report->user_id       = Auth::user()->id;
			$report->reason        = $reason;

			$report->save();
		}
	}

	public function unsetModeration($moderationId)
	{
		if ($this instanceof Forum_Post || $this instanceof Forum_Reply) {
			$this->moderatorLockedFlag = 0;
			$this->save();

			// Create the moderation log
			$moderationLog                      = new Forum_Moderation_Log;
			$moderationLog->forum_moderation_id = $moderationId;
			$moderationLog->user_id             = Auth::user()->id;
			$moderationLog->action              = Forum_Moderation::REMOVE_REPORT;

			$moderationLog->save();
		}
	}

	public function setAdminReview($moderationId)
	{
		if ($this instanceof Forum_Post || $this instanceof Forum_Reply) {
			// Set the object for admin review
			$this->adminReviewFlag = 1;
			$this->save();

			// Create the moderation log
			$moderationLog                      = new Forum_Moderation_Log;
			$moderationLog->forum_moderation_id = $moderationId;
			$moderationLog->user_id             = Auth::user()->id;
			$moderationLog->action              = Forum_Moderation::ADMIN_REVIEW;

			$moderationLog->save();
		}
	}

	public function adminDeletePost($moderationId)
	{
		if ($this instanceof Forum_Post || $this instanceof Forum_Reply) {
			// Delete the object
			$this->delete();

			// Create the moderation log
			$moderationLog                      = new Forum_Moderation_Log;
			$moderationLog->forum_moderation_id = $moderationId;
			$moderationLog->user_id             = Auth::user()->id;
			$moderationLog->action              = Forum_Moderation::DELETE_POST;

			$moderationLog->save();
		}
	}

	/**
	 * Get all users that have a forum role
	 *
	 * @return array
	 */
	public function users()
	{
		// Get all forum roles
		return Role::where('group', 'Forum')->orderBy('priority', 'asc')->get()->users();
	}

	/**
	 * Get the recent non-support posts
	 *
	 * @return array
	 */
	public function recentPosts()
	{
		// Get all non-support categories
		return Forum_Category::with('boards.posts')
			->where('forum_category_type_id', '!=', Forum_Category::TYPE_SUPPORT)
			->get()
			->boards
			->posts
			->take(5);
	}

	/**
	 * Get the recent posts for a category
	 *
	 * @return array
	 */
	public function recentCategoryPosts($categoryId)
	{
		// Get all non-support categories
		return Forum_Category::with('boards.posts')
			->where('uniqueId', $categoryId)
			->boards
			->posts
			->take(10);
	}

	/**
	 * Get the recent support posts
	 *
	 * @return array
	 */
	public function recentSupportPosts()
	{
		// Get all non-support categories
		return Forum_Category::with('boards.posts')
			->where('forum_category_type_id', Forum_Category::TYPE_SUPPORT)
			->get()
			->boards
			->posts
			->take(3);
	}

	/**
	 * Get the unread posts for user
	 *
	 * @return Forum_Post[]
	 */
	public function unreadPostsByUser($userId)
	{
		// Get all viewed posts
		$viewedPostIds = Forum_Post_View::where('user_id', $userId)->get()->id->toArray();

		$posts = Forum_Post::whereNotIn('uniqueId', $viewedPostIds)->get();

		return $posts;
	}

	/**
	 * Set all posts read for a user
	 *
	 * @return boolean
	 */
	public function markAllReadByUser($userId)
	{
		$posts = $this->unreadPostsByUser($userId);

		if (count($posts) > 0) {
			foreach ($posts as $post) {
				$post->userViewed($userId);
			}
		}

		return true;
	}
}