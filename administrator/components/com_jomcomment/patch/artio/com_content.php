<?php
/**
 * SEF module for Joomla!
 *
 * @author      $Author: michal $
 * @copyright   ARTIO s.r.o., http://www.artio.cz
 * @package     JoomSEF
 * @version     $Name$, ($Revision: 4994 $, $Date: 2005-11-03 20:50:05 +0100 (??t, 03 XI 2005) $)
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_VALID_MOS')) die('Direct Access to this location is not allowed.');

class sefext_com_content extends sef_404
{
    function create($string, &$vars) {
        global $sefConfig;

        $params = SEFTools::GetExtParams('com_content');
        
        extract($vars);

        // Set title.
        $title = array();

        //Limitstart, limit nog toevoegen
        switch (@$task) {
            case 'new': {
                /*
                $title[] = getMenuTitle($option, $task, $Itemid, $string);
                $title[] = 'new' . $sefConfig->suffix;
                */
                break;
            }
            case 'archivecategory':
            case 'archivesection': {
                if (eregi($task.".*id=".$id, $_SERVER['REQUEST_URI'])) break;
            }
            default: {
                if( isset($do_pdf) && ($do_pdf == 1) ) {
                    // Create PDF
                    $title = sef_404::getContentTitles('view', $id);
                    if (count($title) === 0) $title[] = getMenuTitle(@$option, @$task, @$Itemid);
                    
                    $title[] = _CMN_PDF;
                } else {
                    //$title = array_merge($title, sef_404::getContentTitles($task,$id));
                    $title = sef_404::getContentTitles($task, $id);
                    if (count($title) === 0) $title[] = getMenuTitle(@$option, @$task, @$Itemid);
                    //		if ((@$task == "view") && isset($sefConfig->suffix)) {
                    //			// throw the suffix on the last item
                    //			if ($sefConfig->suffix == "/") {
                    //				$title[] = "/";
                    //			}else{
                    //				$title[count($title)-1] .= $title[count($title)-1].$sefConfig->suffix;
                    //			}
                    //		}
                    
                    // Add content ID if set to
                    #BEGIN_JC
                    if(isset($cpage))
						$title[count($title) - 1] .= "cpage-". $cpage . $sefConfig->suffix;
					else
						$title[count($title) - 1] .= $sefConfig->suffix;
					#END_JC
					
                    if( $params->get('titleid', '0') != '0' ) {
                        $i = count($title) - 1;
                        $title[$i] = $id . '-' . $title[$i];
                    }
                    
                    if ((@$task == 'view') && isset($sefConfig->suffix)) {
                        $title[count($title) - 1] .= $sefConfig->suffix;
                    }
                    else {
                        $title[] = '/';
                    }

                    if( isset($pop) && ($pop == 1) ) {
                        // Print article
                        $title[] = _CMN_PRINT. (isset($page) ? '-'.($page+1) : '');
                    }
                }
            }
        }
        if (count($title) > 0) {
            $string = sef_404::sefGetLocation($string, $title, null, @$limit, @$limitstart, @$lang);
        }

        return $string;
    }
}
?>
