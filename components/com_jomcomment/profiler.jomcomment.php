<?php

/**
 * Jom Comment 
 * @package JomComment
 * @copyright (C) 2006 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/

# Don't allow direct linking
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

if(cmsVersion() == _CMS_JOOMLA10){
	/**
	 * Our very own profiler class
	 */
	class JomProfiler extends mosProfiler
	{
		var $_html = "";
		var $prefix; 	# we need this for 1.5
		var $start;		# we need this for 1.5

		/**
		 * Put a marker at current execution point
		 */
		function mark($label)
		{
			$this->_html .= sprintf("\n<div class=\"profiler\">$this->prefix %.3f $label</div>", $this->getmicrotime() - $this->start);
		}

		/**
		 * Attach any notes, if necessary
		 */
		function addDebugNote($note)
		{
			$this->_html .= sprintf("\n<div class=\"profiler\">%s</div>", $note);
		}

		/**
		 * Return the profiler output, along with number of queries
		 */
		function getHTML()
		{
			return $this->_html;
		}
	}
}else if(cmsVersion() == _CMS_JOOMLA15){
	jimport('joomla.utilities.profiler');
	/**
	 * Our very own profiler class
	 */
	class JomProfiler extends JProfiler
	{
		var $_html = "";
		var $prefix; 	# we need this for 1.5
		var $start;		# we need this for 1.5

		/**
		 * Put a marker at current execution point
		 */
		function mark($label)
		{
			$this->_html .= sprintf("\n<div class=\"profiler\">$this->prefix %.3f $label</div>", $this->getmicrotime() - $this->start);
		}

		/**
		 * Attach any notes, if necessary
		 */
		function addDebugNote($note)
		{
			$this->_html .= sprintf("\n<div class=\"profiler\">%s</div>", $note);
		}

		/**
		 * Return the profiler output, along with number of queries
		 */
		function getHTML()
		{
			return $this->_html;
		}
	}
}
