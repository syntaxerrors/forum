<?php
namespace Syntax\Core;

class Forum_Reply_Edit extends Forum
{
	/********************************************************************
	 * Declarations
	 *******************************************************************/
	protected $table = 'forum_reply_edits';

	/********************************************************************
	 * Aware validation rules
	 *******************************************************************/
	public static $rules = array(
		'user_id'             => 'required|exists:users,uniqueId',
		'forum_reply_id'      => 'required|exists:forum_replies,uniqueId',
	);

	/********************************************************************
	 * Scopes
	 *******************************************************************/

	/********************************************************************
	 * Relationships
	 *******************************************************************/
	public static $relationsData = array(
		'reply' => array('belongsTo', 'Forum_Reply',	'foreignKey' => 'forum_reply_id'),
		'user'  => array('belongsTo', 'User',			'foreignKey' => 'user_id'),
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