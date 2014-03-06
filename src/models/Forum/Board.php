<?php
namespace Syntax\Core;

use HTML;
use Auth;

class Forum_Board extends Forum
{
	/********************************************************************
	 * Declarations
	 *******************************************************************/

	/**
	 * Table declaration
	 *
	 * @var string $table The table this model uses
	 */
	protected $table      = 'forum_boards';
	protected $primaryKey = 'uniqueId';
	public $incrementing  = false;

	const TYPE_APPLICATION = 3;
	const TYPE_CHILD       = 2;
	const TYPE_STANDARD    = 1;
	const TYPE_ROLEPLAYING = 4;
	const TYPE_GM          = 5;

	/**
	 * Soft Delete users instead of completely removing them
	 *
	 * @var bool $softDelete Whether to delete or soft delete
	 */
	protected $softDelete = true;


	/********************************************************************
	 * Aware validation rules
	 *******************************************************************/

    /**
     * Validation rules
     *
     * @static
     * @var array $rules All rules this model must follow
     */
	public static $rules = array(
		'name'                => 'required|max:200',
		'keyName'             => 'required|max:200',
		'forum_category_id'   => 'required|exists:forum_categories,uniqueId',
	);

	/********************************************************************
	 * Scopes
	 *******************************************************************/

	/********************************************************************
	 * Relationships
	 *******************************************************************/
	public static $relationsData = array(
		'category' => array('belongsTo',	'Forum_Category',	'foreignKey' => 'forum_category_id'),
		'parent'   => array('belongsTo',	'Forum_Board',		'foreignKey' => 'parent_id'),
		'type'     => array('belongsTo',	'Forum_Board_Type',	'foreignKey' => 'forum_board_type_id'),
		'children' => array('hasMany',		'Forum_Board',		'foreignKey' => 'parent_id'),
		'posts'    => array('hasMany',		'Forum_Post',		'foreignKey' => 'forum_board_id',
							'orderBy' => array('modified_at', 'desc'), 'with' => array('author', 'status')),
	);

	/********************************************************************
	 * Model events
	 *******************************************************************/

	/********************************************************************
	 * Getter and Setter methods
	 *******************************************************************/

    /**
     * Get count of posts in this board
     *
     * @return int
     */
	public function getPostsCountAttribute()
	{
		return $this->posts()->count();
	}

    /**
     * Get count of replies in this board
     *
     * @return int
     */
	public function getRepliesCountAttribute()
	{
		$replies = $this->posts()->with('replies')->get()->replies->count();
		return $replies;
	}

    /**
     * Get the last actual post from this board
     *
     * @return int
     */
	public function getLastPostAttribute()
	{
		$posts         = $this->posts()->get();
		$childrenPosts = $this->children->posts;

		$newestPost = $posts->sortBy(function ($post) {
			return $post->modified_at;
		})->reverse()->first();

		$newestChildPost = $childrenPosts->sortBy(function ($post) {
			return $post->modified_at;
		})->reverse()->first();

		if ($newestPost != null && $newestChildPost == null) {
			return $newestPost;
		} elseif ($newestChildPost != null && $newestPost == null) {
			return $newestChildPost;
		} elseif ($newestChildPost != null && $newestPost != null) {
			if ($newestPost->modified_at > $newestChildPost->modified_at) {
				return $newestPost;
			} else {
				return $newestChildPost;
			}
		}

		return false;
	}

    /**
     * Get last update in this board
     *
     * @return Forum_Post|Forum_Reply
     */
	public function getLastUpdateAttribute()
	{
		$lastPost = $this->getLastPostAttribute();

		if ($lastPost != false) {
			return $lastPost->lastUpdate;
		}

		return false;
	}

    /**
     * Get the pagination page number for the last reply of the last post
     *
     * @return int
     */
	public function getLastUpdatePageAttribute()
	{
		$lastPost = $this->getLastPostAttribute();

		if ($lastPost instanceof Forum_Post) {
			$replies    = $lastPost->replies;
			$lastUpdate = $lastPost->lastUpdate;

			foreach ($replies as $key => $reply) {
				if ($reply->id == $lastUpdate->id) {
					return round($key/30) + 1;
				}
			}

		}
		return 1;
	}

    /**
     * Get child board links and format them as needed
     *
     * @return int
     */
	public function getChildLinksAttribute()
	{
		$children = $this->children()->with('posts')->orderBy('position', 'asc')->get();

		if (count($children) > 0) {
			$links = array();

			foreach ($children as $child) {
				$posts = $child->posts;
				$posts = $posts->filter(function ($post) {
					if ($post->checkUserViewed(Auth::user()->id) == false) {
						return true;
					}
				});

				if (count($posts) > 0) {
					$links[] = '<strong>' . HTML::link('forum/board/view/'. $child->id, $child->name) . '</strong>';
				} else {
					$links[] = HTML::link('forum/board/view/'. $child->id, $child->name, array('style' => 'font-weight: normal;', 'class' => 'text-disabled'));
				}
			}
			return implode(', ', $links);
		}

		return false;
	}

	/********************************************************************
	 * Extra Methods
	 *******************************************************************/
}