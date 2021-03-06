<?php
namespace Syntax\Core;

class Forum_Board_Type extends Forum
{
	/********************************************************************
	 * Declarations
	 *******************************************************************/
	protected $table = 'forum_board_types';
	
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
		'boards' => array('hasMany', 'Forum_Board', 'foreignKey' => 'forum_board_type_id'),
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