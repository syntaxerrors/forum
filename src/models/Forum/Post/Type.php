<?php
namespace Syntax\Core;

class Forum_Post_Type extends Forum
{
	/********************************************************************
	 * Declarations
	 *******************************************************************/
	protected $table = 'forum_post_types';

	/********************************************************************
	 * Aware validation rules
	 *******************************************************************/

	/********************************************************************
	 * Scopes
	 *******************************************************************/

	/********************************************************************
	 * Relationships
	 *******************************************************************/
	public static $relationsData = array(
		'posts' => array('hasMany', 'Forum_Post', 'foreignKey' => 'forum_post_type_id'),
	);

	/********************************************************************
	 * Model Events
	 *******************************************************************/

	/********************************************************************
	 * Getter and Setter methods
	 *******************************************************************/

	/********************************************************************
	 * Extra Methods
	 *******************************************************************/

}