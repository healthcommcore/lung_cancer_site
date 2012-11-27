<?php

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

  /*
   ========================================================
   Plugin TruncHTML
   --------------------------------------------------------
   Author: Oliver Heine
   http://gadgets.silenz.org/
   --------------------------------------------------------
   You may use this Plugin for free as long as this
   header remains intact.
   ========================================================
   File: pi.trunchtml.php
   --------------------------------------------------------
   Purpose: Truncates HTML to the specified length without
   without leaving open tags.
   ========================================================
   
   */
  
//  $plugin_info = array('pi_name' => 'TruncHTML', 'pi_version' => '1.1.2', 'pi_author' => 'Oliver Heine', 'pi_author_url' => 'http://gadgets.silenz.org/index.php/gadgets/category/Plugins/', 'pi_description' => 'Truncates HTML/Text to the specified number of characters. Does not count characters in HTML-tags, does not cut-off in the middle of tags, closes all open tags.', 'pi_usage' => trunchtml::usage());
  
	class CMSTrunchtml
	{
		var $return_data;

		function CMSTrunchtml(){}
      
		function trunchtml($text = "", $chars = 100, $config = 0)
		{
			//$chars = '100'; //(!$TMPL->fetch_param('chars')) ? '500' : $TMPL->fetch_param('chars');
			$ending = ''; //(!$TMPL->fetch_param('ending')) ? '' : $TMPL->fetch_param('ending');
			$exact = 'no'; //(!$TMPL->fetch_param('exact')) ? 'no' : $TMPL->fetch_param('exact');

			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $chars) {
			  $this->return_data = $text;
			}
          
          preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
          $total_length = 0;
          $open_tags = array();
          $truncate = '';
          foreach ($lines as $line_matchings) {
              if (!empty($line_matchings[1])) {
                  if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                  } elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                      $pos = array_search($tag_matchings[1], $open_tags);
                      if ($pos !== false) {
                          unset($open_tags[$pos]);
                      }
                  } elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                      array_unshift($open_tags, strtolower($tag_matchings[1]));
                  }
                  $truncate .= $line_matchings[1];
              }
              $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
              if ($total_length + $content_length > $chars) {
                  $left = $chars - $total_length;
                  $entities_length = 0;
                  if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                      foreach ($entities[0] as $entity) {
                          if ($entity[1] + 1 - $entities_length <= $left) {
                              $left--;
                              $entities_length += strlen($entity[0]);
                          } else {
                              break;
                          }
                      }
                  }
                  $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                  break;
              } else {
                  $truncate .= $line_matchings[2];
                  $total_length += $content_length;
              }
              if ($total_length >= $chars) {
                  break;
              }
          }
          if ($exact != "yes") {
              $spacepos = strrpos($truncate, ' ');
              if ( $spacepos !== FALSE ) {
                  $truncate = substr($truncate, 0, $spacepos);
              }
          }
          
          foreach ($open_tags as $tag) {
              $truncate .= '</' . $tag . '>';
          }
          $truncate .= " " . $ending;
          $this->return_data = $truncate;
          
          return $this->return_data;
      }

      // ----------------------------------------
      //  Plugin Usage
      // ----------------------------------------
      // This function describes how the plugin is used.
      //  Make sure and use output buffering
      function usage()
      {
          ob_start();
?>
Example:
----------------
{exp:trunchtml chars="300" ending="<a href='{path=site/comments}'>read on</a>"}
{body}
{/exp:trunchtml}

Parameters:
----------------
chars=""
Defaults to 500. Number of characters that are to be returned. 

ending=""
Optional. String to be added after the truncated text.

exact="yes"
If this is set, text will be truncated after exactly the specified number of chars. 
Otherwise text will be cut after a space to prevent cutting words in the middle.


----------------
CHANGELOG:

1.1.2
* small correction suggested by D. Jones

1.1.1
* updated usage instructions

1.1
* added parameter exact="yes"

1.0 
* initial release

<?php
          $buffer = ob_get_contents();
          ob_end_clean();
          return $buffer;
      }
      /* END */
      
  }
  // END CLASS
?>
