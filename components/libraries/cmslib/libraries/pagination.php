<?php  
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 * 
 * @modified	Azrul Rahim
 * @purpose 	Change class naming and remove CI dependecies  
 */

// ------------------------------------------------------------------------

/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/pagination.html
 */
class CMSPagination {

	var $base_url			= ''; // The page we are linking to
	var $total_rows  		= ''; // Total number of items (database results)
	var $per_page	 		= 4; // Max number of items you want shown per page
	var $num_links			=  4; // Number of "digit" links to show before/after the currently viewed page
	var $cur_page	 		=  0; // The current page being viewed
	var $first_link   		= '&lsaquo; First';
	var $next_link			= '&gt;';
	var $prev_link			= '&lt;';
	var $last_link			= 'Last &rsaquo;';
	var $full_tag_open		= '';
	var $full_tag_close		= '';
	var $first_tag_open		= '';
	var $first_tag_close	= '&nbsp;';
	var $last_tag_open		= '&nbsp;';
	var $last_tag_close		= '';
	var $cur_tag_open		= '&nbsp;<b>';
	var $cur_tag_close		= '</b>';
	var $next_tag_open		= '&nbsp;';
	var $next_tag_close		= '&nbsp;';
	var $prev_tag_open		= '&nbsp;';
	var $prev_tag_close		= '';
	var $num_tag_open		= '&nbsp;';
	var $num_tag_close		= '';
	
	var $item_id			= "1";
	var $num_pages			= 0;
	var $last_page			= "";

	var $cms                = null;
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
	function CMSPagination($params = array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);		
		}
		//log_message('debug', "Pagination Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
	function initialize($params = array())
	{
		$this->cms =& cmsInstance('CMSCore');
		$this->cms->load('helper','url');
		
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}		
		}
		
		// Need to clean up any previous &cpage
		$this->base_url = preg_replace('/\&cpage=[0-9]*/', '', $this->base_url);
		$this->base_url = preg_replace('/\&amp;cpage=[0-9]*/', '', $this->base_url);
		
		// Remove the url and set it to start with index.php if SEF ext exist
		if(function_exists('function_exists'))
			$this->base_url = "index.php" . substr($this->base_url, strpos($this->base_url, '?'));
		

	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Return the current limist start value
	 *
	 * @access	public
	 * @return	number, current limitstart
	 */	
	function get_page()
	{
		// For build-in SEF ext for com_content, we need to handle it in a special way
		if($p = strpos($_SERVER['REQUEST_URI'], 'content/view/cpage,'))
		{
			return substr($_SERVER['REQUEST_URI'], $p+19);
		} else
			if(isset($_GET['cpage']))
				return $_GET['cpage'];
			else
				return 0;
	}	
	
	// --------------------------------------------------------------------
	
	/**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @return	string
	 */	
	function create_links()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
		   return '';
		}

		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);
		$this->num_pages = $num_pages;

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
		{
			return '';
		}

		// Determine the current page number.		
		// $CI =& get_instance();	
		if (isset($_GET['cpage']))
		{
			$this->cur_page = intval($_GET['cpage']);
			
			// Prep the current page - no funny business!
			$this->cur_page = (int) $this->cur_page;
		}
				
		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = 0;
		}
		
		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}
		
		$uri_page_number = $this->cur_page;
		$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		// Add a trailing slash to the base URL if needed
		$this->base_url = rtrim($this->base_url); //rtrim($this->base_url, '/') .'/';

  		// And here we go...
		$output = '';

		// Render the "First" link
		if  ($this->cur_page > $this->num_links)
		{
			$output .= $this->first_tag_open.'<a href="'.$this->base_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
		}

		// Render the "previous" link
		if  (($this->cur_page - $this->num_links) >= 0)
		{
			$i = $uri_page_number - $this->per_page;
			if ($i == 0) $i = '';
			
			$tl = $this->base_url.'&cpage='.$i;
			if(function_exists('function_exists'))
				$tl = cmsSefAmpReplace($tl);
			
			if(strpos($tl, 'content/view'))
			{
				$tl .= "cpage,$i";
			}	
			$output .= $this->prev_tag_open.'<a href="'.$tl.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		}

		// Write the digit links
		for ($loop = $start -1; $loop <= $end; $loop++)
		{
			$i = ($loop * $this->per_page) - $this->per_page;
					
			if ($i >= 0)
			{
				if ($this->cur_page == $loop)
				{
					$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
				}
				else
				{
					//$n = ($i == 0) ? '' : "&amp;cpage=$i";
					$n = "&cpage=$i";					
					$tl = $this->base_url.$n;
					
					$tl = cmsSefAmpReplace($tl);
					
					// For com_content view, we need to specifically add the cpage,xxx
					// since it doesn't gets added by Joomla default SEF ext
					if(strpos($tl, 'content/view'))
					{
						$tl .= "cpage,$i";
					}
						
					$output .= $this->num_tag_open.'<a href="'.$tl.'">'.$loop.'</a>'.$this->num_tag_close;
				}
			}
		}

		// Render the "next" link
		if ($this->cur_page < $num_pages)
		{
			$i = ($this->cur_page * $this->per_page);
			$tl = $this->base_url.'&cpage='.$i;

			$tl = cmsSefAmpReplace($tl);
			
			// For com_content view, we need to specifically add the cpage,xxx
			// since it doesn't gets added by Joomla default SEF ext
			if(strpos($tl, 'content/view'))
			{
				$tl .= "cpage,$i";
			}
			
			//$output .= $this->next_tag_open.'<a href="'.$this->base_url.'&amp;cpage='.($this->cur_page * $this->per_page).'">'.$this->next_link.'</a>'.$this->next_tag_close;
			$output .= $this->next_tag_open.'<a href="'.$tl.'">'.$this->next_link.'</a>'.$this->next_tag_close;
		}

		// Render the "Last" link
		if (($this->cur_page + $this->num_links) < $num_pages)
		{
			$i = (($num_pages * $this->per_page) - $this->per_page);
			
			$tl = $this->base_url.'&cpage='.$i;
			$tl = cmsSefAmpReplace($tl);
			
			// For com_content view, we need to specifically add the cpage,xxx
			// since it doesn't gets added by Joomla default SEF ext
			if(strpos($tl, 'content/view'))
			{
				$tl .= "cpage,$i";
			}
			
			$this->last_page = $tl;
			
			$output .= $this->last_tag_open.'<a href="'.$tl.'">'.$this->last_link.'</a>'.$this->last_tag_close;
		}

		// Kill double slashes.  Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;
		
		return $output;		
	}
	
	function last_link()
	{
		if (($this->num_pages > 1))
		{
			$i = (($this->num_pages * $this->per_page) - $this->per_page);
			
			$tl = $this->base_url.'&cpage='.$i;

            $tl = cmsSefAmpReplace($tl);
			
			// For com_content view, we need to specifically add the cpage,xxx
			// since it doesn't gets added by Joomla default SEF ext
			if(strpos($tl, 'content/view'))
			{
				$tl .= "cpage,$i";
			}
			
			return $tl;
		}

	}
}
// END Pagination Class
?>
