<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is for Joomla to get the SEF'ed URLs for Jom Comment.
 **/

function JomCommentBuildRoute(& $query){
	// Return values
	$segments   = array();
	$oldTask = array('mycomments', 'mysubscriptions', 'myfavorites','rss');
	$newTask = array('comments', 'subscriptions', 'favorites','feed');
	
	if(isset($query['task'])){
		$query['task'] = str_replace($oldTask, $newTask, $query['task']);

		$segments[] = $query['task'];
		unset($query['task']);		
	}

	// RSS stuffs
	if(isset($query['contentid'])){
	    $segments[]	= $query['contentid'];
		unset($query['contentid']);
	}

	// Trackbacks id
	if(isset($query['id'])){
		$segments[] = $query['id'];
		unset($query['id']);
	}
	
	// RSS & Trackbacks uses this
	if(isset($query['opt'])){
	    $segments[] = $query['opt'];
	    unset($query['opt']);
	}

	if(isset($query['sid'])){
		$segments[]	= $query['sid'];
		unset($query['sid']);
	}
	
	if(isset($query['do'])){
		$segments[] = $query['do'];
		unset($query['do']);
	}


	
	if(isset($query['amp;Itemid'])){
	    $query['Itemid']    = $query['amp;Itemid'];
        unset($query['amp;Itemid']);
	}

    return $segments;
}

function JomCommentParseRoute($segments){
	$vars   = array();

	if(isset($segments[0])){
	    if($segments[0] == 'comments' && !isset($segments[1]))
	        $vars['task']   = 'mycomments';
		else if($segments[0] == 'subscriptions' && !isset($segments[1]))
		    $vars['task']   = 'mysubscriptions';
		else if($segments[0] == 'favorites' && !isset($segments[1]))
		    $vars['task']   = 'myfavorites';
		else if($segments[0] == 'jomadmin' && isset($segments[1]) && isset($segments[2])){
			$vars['task']	= 'jomadmin';
			$vars['sid']	= $segments[1];
			$vars['do']		= $segments[2];
		} else if($segments[0] == 'trackback' && isset($segments[1]) && isset($segments[2])){
		    $vars['task']   = $segments[0];
		    $vars['id']     = $segments[1];
		    $vars['opt']    = $segments[2];
		} else if($segments[0] == 'feed'){
			$vars['task']   	= 'rss';
			// If we only have /feed , it is a global, system wide comment feed
			if(count($segments) > 2){ 
				$vars['contentid']  = $segments[1];
				$vars['opt']        = $segments[2];
			}
		}else if($segments[0] == 'userinfo'){
			$vars['task']   	= 'userinfo';
		}
	}
	return $vars;
}

?>
