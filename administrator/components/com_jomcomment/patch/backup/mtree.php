<?php
/**
* Mosets Tree 
*
* @package Mosets Tree 1.50
* @copyright (C) 2005 Mosets Consulting
* @url http://www.Mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$cms    =& cmsInstance('CMSCore');
# Main Mosets Tree file
require( $cms->get_path('root') .'/administrator/components/com_mtree/config.mtree.php');
require_once( $cms->get_path('root').'/administrator/components/com_mtree/admin.mtree.class.php');
require_once( $cms->get_path('root').'/components/com_mtree/mtree.class.php');
require_once( $cms->get_path('root').'/components/com_mtree/mtree.tools.php');

if ( file_exists( $cms->get_path('root') ."/components/com_mtree/css/".$mainframe->getTemplate().".css" ) ) {
	$mainframe->addCustomHeadTag( "<link href=\"".$mosConfig_live_site."/components/com_mtree/css/".$mainframe->getTemplate().".css\" rel=\"stylesheet\" type=\"text/css\"/>" );
} else {
	$mainframe->addCustomHeadTag( "<link href=\"".$mosConfig_live_site."/components/com_mtree/css/mtree.css\" rel=\"stylesheet\" type=\"text/css\"/>" );
}

# Include the language file. Default is English
include_once( 'components/com_mtree/language/'.$mt_language.'.php');
$_MT_LANG =& new mtLanguage();

# Main mtree class
$mtree = new mtree();

# Categories name cache
$cache_cat_names = array();

# Savant Class
require_once( $cms->get_path('root').'/components/com_mtree/Savant2.php');

$task = trim( mosGetParam( $_REQUEST, 'task', '' ) );
$link_id = intval( mosGetParam( $_REQUEST, 'link_id', 0 ) );
$cat_id = intval( mosGetParam( $_REQUEST, 'cat_id', 0 ) );
$user_id = intval( mosGetParam( $_REQUEST, 'user_id', 0 ) );
$start = substr(trim( mosGetParam( $_REQUEST, 'start', '' ) ), 0, 1);
$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

$now = date( 'Y-m-d H:i:s', time() + $mosConfig_offset * 60 * 60 );

$savantConf = array (
		'template_path' => $cms->get_path('root')."/components/com_mtree/templates/$mt_template/",
		'plugin_path' => $cms->get_path('root').'/components/com_mtree/Savant2/',
		'filter_path' => $cms->get_path('root').'/components/com_mtree/Savant2/'
);

# cache activation
$cache =& mosCache::getCache( 'com_mtree' );

mtAppendPathWay( $option, $task, $cat_id, $link_id );

switch ($task) {
	
	case "viewlink":
		viewlink( $link_id, $limitstart, $option );
		break;

	case "print":
		$cache->call( 'printlink', $link_id, $option );
		break;

	/* Visit a URL */
	case "visit":
		visit( $link_id );
		break;

	/* Reviews */
	case "writereview":
		writereview( $link_id, $option );
		break;
	case "addreview":
		mosCache::cleanCache( 'com_mtree' );
		addreview( $link_id, $option );
		break;

	/* Ratings */
	case "rate":
		rate( $link_id, $option );
		break;
	case "addrating":
		mosCache::cleanCache( 'com_mtree' );
		addrating( $link_id, $option );
		break;

	/* Recommend to Friend */
	case "recommend":
		recommend( $link_id, $option );
		break;
	case "send_recommend":
		send_recommend( $link_id, $option );
		break;

	/* Contact Owner */
	case "contact":
		contact( $link_id, $option );
		break;
	case "send_contact":
		send_contact( $link_id, $option );
		break;

	/* Report Listing */
	case "report":
		report( $link_id, $option );
		break;
	case "send_report":
		send_report( $link_id, $option );
		break;

	/* Claim Listing */
	case "claim":
		claim( $link_id, $option );
		break;
	case "send_claim":
		send_claim( $link_id, $option );
		break;

	/* Add Listing */
	case "addlisting":
		editlisting( 0, $option );
		break;
	case "editlisting":
		editlisting( $link_id, $option );
		break;
	case "savelisting":
		savelisting( $option );
		break;

	/* Add Category */
	case "addcategory":
		addcategory( $option );
		break;
	case "addcategory2":
		addcategory2( $option );
		break;

	/* Delete Listing */
	case "deletelisting":
		deletelisting( $link_id, $option );
		break;
	case "confirmdelete":
		mosCache::cleanCache( 'com_mtree' );
		confirmdelete( $link_id, $option );
		break;

	/* My Listing */
	case "mylisting":
		mylisting( $limitstart, $option );
		break;

	/* All listing from this owner */
	case "viewowner":
		viewowner( $user_id, $limitstart, $option );
		break;

	/* List Alphabetically */
	case "listalpha":
		listalpha( $cat_id, $start, $limitstart, $option );
		break;
	
	/* Popular Listing */
	case "listpopular":
		listpopular( $cat_id, $option );
		break;

	/* Most Rated Listing*/
	case "listmostrated":
		listmostrated( $cat_id, $option );
		break;

	/* Top Rated Listing*/
	case "listtoprated":
		listtoprated( $cat_id, $option );
		break;

	/* Most Reviewed Listing*/
	case "listmostreview":
		listmostreview( $cat_id, $option );
		break;

	/* New Listing */
	case "listnew":
		listnew( $cat_id, $limitstart, $option );
		break;

	/* Featured Listing */
	case "listfeatured":
		listfeatured( $cat_id, $limitstart, $option );
		break;

	/* Search */
	case "search":
		search( $option );
		break;
	case "advsearch":
		advsearch( $option );
		break;
	case "advsearch2":
		advsearch2( $option );
		break;

	/* Default Main Index */
	case "listcats":
	default:
		showTree( $cat_id, $limitstart, $option );
		break;
}

function showTree( $cat_id, $limitstart, $option ) {
	global $database, $mainframe, $cache, $mosConfig_MetaTitle, $_MT_LANG;
	global $mt_allow_listings_submission_in_root;

	$database->setQuery( "SELECT cat_id, cat_name, cat_desc, cat_template, cat_image, cat_allow_submission, metakey, metadesc, cat_published, cat_usemainindex, cat_show_listings FROM #__mt_cats WHERE cat_id='".$cat_id."' AND cat_published='1' LIMIT 1" );
	$database->loadObject( $cat );

	if ( $cat ) {
		# Set Page Title
		if ( $cat_id == 0 ) {
			$mainframe->setPageTitle( $_MT_LANG->ROOT );
			$cat->cat_allow_submission = $mt_allow_listings_submission_in_root;
		} else {
			$mainframe->setPageTitle( $cat->cat_name );
		}

		# Add META tags
		if ($mosConfig_MetaTitle=='1') {
			$mainframe->addMetaTag( 'title' , $cat->cat_name );
		}

		if ($cat->metadesc <> '') $mainframe->prependMetaTag( 'description', $cat->metadesc );
		if ($cat->metakey <> '') $mainframe->prependMetaTag( 'keywords', $cat->metakey );
	}

	//showTree_cache( $cat, $limitstart, $option );
	$cache->call( 'showTree_cache', $cat, $limitstart, $option );
}
function showTree_cache( $cat, $limitstart, $option ) {
	global $database, $_MT_LANG, $mosConfig_offset, $Itemid, $mosConfig_live_site;
	$cms    =& cmsInstance('CMSCore');
	global $savantConf, $mt_listing_image_dir, $mt_cat_image_dir, $mt_fe_num_of_subcats, $mt_fe_num_of_links, $mt_display_empty_cat, $mt_first_listing_order1, $mt_first_listing_order2, $mt_second_listing_order1, $mt_second_listing_order2, $mt_first_cat_order1, $mt_first_cat_order2, $mt_second_cat_order1, $mt_second_cat_order2, $mt_show_email, $mt_display_alpha_index, $mt_display_listing_count_in_root, $mt_display_cat_count_in_root, $mt_display_cat_count_in_subcat, $mt_display_listing_count_in_subcat, $mt_display_listings_in_root, $mt_min_votes_to_show_rating, $mt_user_addlisting, $mt_allow_listings_submission_in_root, $mt_fe_num_of_chars;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
	if ( empty($cat->cat_id) ) {
		$cat_id = 0;
	} else {
		$cat_id = $cat->cat_id;
	}

	if ( is_object($cat) && $cat->cat_published == 0 && $cat_id > 0 ) {
		
		echo _NOT_EXIST;

	} else {

		# Page Navigation
		$database->setQuery("SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE l.link_published='1' AND l.link_approved='1' && cl.cat_id ='".$cat_id."' "
			. "\n AND ( l.publish_up = '0000-00-00 00:00:00' OR l.publish_up <= '$now'  ) "
			. "\n AND ( l.publish_down = '0000-00-00 00:00:00' OR l.publish_down >= '$now' ) "
			.	"\n AND cl.link_id = l.link_id "
		);
		$total_links = $database->loadResult();

		require_once($cms->get_path('root')."/includes/pageNavigation.php");
		$pageNav = new mosPageNav( $total_links, $limitstart, $mt_fe_num_of_links );

		# Retrieve categories
		$sql = "SELECT * FROM #__mt_cats WHERE cat_published=1 && cat_approved=1 && cat_parent='".$cat_id."' ";
		if ( !$mt_display_empty_cat ) { $sql .= " && ( cat_cats > 0 || cat_links > 0 ) ";	}
		$sql .= "ORDER BY $mt_first_cat_order1 $mt_first_cat_order2, $mt_second_cat_order1 $mt_second_cat_order2";

		$database->setQuery( $sql );
		$cats = $database->loadObjectList("cat_id");

		$cat_desc = '';
		$related_categories = null;

		# Only shows sub-cat if this is a root category
		if ( $cat_id == 0 || $cat->cat_usemainindex == 1 ) {
			foreach ( $cats AS $c ) {

				# Get all sub-cats
				$sql = "SELECT cat_id, cat_name, cat_cats FROM #__mt_cats WHERE cat_parent='".$c->cat_id."' && cat_published='1' && cat_approved='1' ";
				if ( !$mt_display_empty_cat ) { $sql .= " && ( cat_cats > 0 || cat_links > 0 ) ";	}

				$sql .= "\nORDER BY cat_featured DESC, $mt_first_cat_order1 $mt_first_cat_order2, $mt_second_cat_order1 $mt_second_cat_order2 ";
				$sql .= "\nLIMIT ". (($mt_fe_num_of_subcats >= 0) ? $mt_fe_num_of_subcats : 0);

				$database->setQuery( $sql );
				$sub_cats[$c->cat_id] = $database->loadObjectList();

				$total_sub_cats = $cats[$c->cat_id]->cat_cats;
				$sub_cats_total[$c->cat_id] = (($total_sub_cats) ? $total_sub_cats : 0 );

			}
		} else {

			# Get related categories
			$database->setQuery( "SELECT r.rel_id FROM #__mt_relcats AS r "
				.	"LEFT JOIN #__mt_cats AS c ON c.cat_id = r.rel_id "
				.	"WHERE r.cat_id='".$cat_id."' AND c.cat_published = '1'" );
			$related_categories = $database->loadResultArray();

		}

		# Retrieve Links
		$database->setQuery( "SELECT l.*, cl.*, u.name AS owner FROM #__mt_links AS l"
			.	"\n LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id "
			.	"\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			.	"\n WHERE link_published='1' && link_approved='1' && cl.cat_id='".$cat_id."' "
			.	"\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
			.	"\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
			.	"ORDER BY $mt_first_listing_order1 $mt_first_listing_order2, $mt_second_listing_order1 $mt_second_listing_order2 "
			.	"LIMIT $limitstart, $mt_fe_num_of_links" );

		$links = $database->loadObjectList();

		# Load custom fields' caption
		$database->setQuery( "SELECT name, value AS caption FROM #__mt_config WHERE name LIKE 'cust_%'" );
		$custom_fields = $database->loadObjectList( "name" );

		# Mambots
		applyMambots( $links );
		
		# Strip Tags
		stripTags( $links );

		# Pathway
		$pathWay = new mtPathWay( $cat_id );

		if ( isset($cat->cat_template) && $cat->cat_template <> '' ) {

			// Make sure the directory exists, otherwise fallback to default template
			$templateDir = $cms->get_path('root') . '/components/com_mtree/templates/' . $cat->cat_template;
			if ( is_dir( $templateDir ) ) {
				$savantConf["template_path"] = $templateDir;
			}

		}

		# Savant Template
		$savant = new Savant2($savantConf);
		
		$savant->_MT_LANG =& $_MT_LANG;
		$savant->assignRef('pageNav', $pageNav);
		$savant->assignRef('pathway', $pathWay);
		$savant->assign('option', $option);
		$savant->assign('Itemid', $Itemid);
		$savant->assign('num_of_subcats_to_show', $mt_fe_num_of_subcats);
		$savant->assign('mt_show_email', $mt_show_email);
		$savant->assign('display_cat_count_in_subcat', $mt_display_cat_count_in_subcat);
		$savant->assign('display_listing_count_in_subcat', $mt_display_listing_count_in_subcat);
		$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);
		$savant->assign('user_addlisting', $mt_user_addlisting);

		$savant->assign('listing_image_dir', $mosConfig_live_site.$mt_listing_image_dir);
		$savant->assign('cat_image_dir', $mosConfig_live_site.$mt_cat_image_dir);
		$savant->assign('custom_fields', $custom_fields);
		$savant->assign('reviews', getReviews($links));
		
		if (isset($cat->cat_allow_submission)) {
			$savant->assign('cat_allow_submission',$cat->cat_allow_submission);
		} else {
			$savant->assign('cat_allow_submission',0);
		}

		if (isset($cat->cat_show_listings)) {
			$savant->assign('cat_show_listings',$cat->cat_show_listings);
		} else {
			$savant->assign('cat_show_listings',0);
		}

		$savant->assign('cat_id', $cat_id);
		$savant->assign('categories', $cats);
		if (isset($sub_cats)) $savant->assign('sub_cats', $sub_cats);
		if (isset($sub_cats_total)) $savant->assign('sub_cats_total', $sub_cats_total);
		$savant->assign('related_categories', $related_categories);
		$savant->assignRef('links', $links);
		if (isset($cat->cat_desc)) $savant->assign('cat_desc', $cat->cat_desc);
		if (isset($cat->cat_image)) $savant->assign('cat_image', $cat->cat_image);
		if (isset($cat->cat_name)) $savant->assign('cat_name', $cat->cat_name);
		
		$savant->assign('total_listing', $total_links);
		$savant->assign('max_chars', $mt_fe_num_of_chars);

		if ( $cat_id == 0 || $cat->cat_usemainindex == 1 ) {
			$savant->assign('display_alpha_index', $mt_display_alpha_index);
			$savant->assign('display_cat_count_in_root', $mt_display_cat_count_in_root);
			$savant->assign('display_listing_count_in_root', $mt_display_listing_count_in_root);
			$savant->assign('display_listings_in_root', $mt_display_listings_in_root);
			
			$savant->display( 'page_index.tpl.php' );
		} else {
			$savant->display( 'page_subCatIndex.tpl.php' );
		}

	}

}

/***
* Simple Search
*/
function search( $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $mt_fe_num_of_searchresults, $mosConfig_offset, $mosConfig_sef, $custom404;
	global $mt_search_link_name, $mt_search_link_desc, $mt_search_address, $mt_search_city, $mt_search_postcode, $mt_search_state, $mt_search_country, $mt_search_email, $mt_search_website, $mt_search_telephone, $mt_search_fax, $mt_search_metakey, $mt_search_metadesc, $mt_first_search_order1, $mt_first_search_order2, $mt_second_search_order1, $mt_second_search_order2, $mt_fulltext_search, $mt_show_email, $mt_fe_num_of_chars, $mt_min_votes_to_show_rating;

	$cms    =& cmsInstance('CMSCore');
	# Search word
	$searchword = mosGetParam( $_REQUEST, 'searchword', '');
	//$searchword = html_entity_decode(htmlentities($searchword, ENT_COMPAT, 'UTF-8'));
	
	# Using Built in SEF feature in Mambo
	if ( !isset($custom404) && $mosConfig_sef ) {
		$searchword = urldecode($searchword);
	}

	# Search Category
	$search_cat = mosGetParam( $_REQUEST, 'cat_id', 0 );
	
	$only_subcats_sql = '';
	if ( $search_cat > 0 ) {
		$mtCats = new mtCats( $database );
		$subcats = $mtCats->getSubCats_Recursive( $search_cat, true );
		$subcats[] = $search_cat;
		if ( count($subcats) > 0 ) {
			$only_subcats_sql = "\n AND cat_id IN (" . implode( ", ", $subcats ) . ")";
		}
	}

	# Page Navigation
	$limitstart = trim( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
	
	$cats = array(0);
	
	# Construct WHERE
	$link_fields = array('link_name', 'link_desc', 'address', 'city', 'postcode', 'state', 'country', 'email', 'website', 'telephone', 'fax', 'metakey', 'metadesc', 'cust_1', 'cust_2', 'cust_3', 'cust_4', 'cust_5', 'cust_6', 'cust_7', 'cust_8', 'cust_9', 'cust_10', 'cust_11', 'cust_12', 'cust_13', 'cust_14', 'cust_15', 'cust_16', 'cust_17', 'cust_18', 'cust_19', 'cust_20', 'cust_21', 'cust_22', 'cust_23', 'cust_24', 'cust_25', 'cust_26', 'cust_27', 'cust_28', 'cust_29', 'cust_30' );

	$database->setQuery( "SELECT name, searchable FROM #__mt_config WHERE name LIKE 'cust_%'" );
	$cust = $database->loadObjectList( 'name' );

	# Full text Search
	if ( $mt_fulltext_search == 1 ) {

		# Retrieve categories
		if ( $limitstart == 0 ) {
			# Search Categories 
			$database->setQuery( "SELECT *, MATCH (cat_name,cat_desc) AGAINST ('$searchword' IN BOOLEAN MODE) AS score FROM #__mt_cats AS c" 
				.	"\n WHERE MATCH (cat_name,cat_desc) AGAINST ('$searchword' IN BOOLEAN MODE) "
				.	"\n AND cat_published='1' AND cat_approved='1' "
				.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' )
				. "\n ORDER BY score desc"
			);
			$cats = $database->loadObjectList();
		}

		$where = array();

		foreach( $link_fields AS $lf ) {
			if ( substr($lf, 0, 5) == "cust_" ) {
				if ($cust[$lf]->searchable == 1) {
					$searchable_fields[] = $lf;
				}
			} else {
				$lf_varname = 'mt_search_'.$lf;
				if ($$lf_varname == 1) {
					$searchable_fields[] = $lf;
				}
			}
		}

		$database->setQuery( "SELECT *, MATCH (".implode(", ",$searchable_fields).") AGAINST ('$searchword' IN BOOLEAN MODE) AS score FROM (#__mt_links AS l, #__mt_cl AS cl)"
			.	"\n	WHERE " 
			. "link_published='1' AND link_approved='1' AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' )"
			.	"\n AND cl.link_id = l.link_id "
			. "\n AND MATCH (".implode(", ",$searchable_fields).") AGAINST ('$searchword' IN BOOLEAN MODE)"
			.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' )
			. "\n ORDER BY score desc"
			.	"\n LIMIT $limitstart, $mt_fe_num_of_searchresults" );
		
		$links = $database->loadObjectList();

		$database->setQuery( "SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl)"
			.	"\n	WHERE " 
			. "link_published='1' AND link_approved='1' AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' )"
			.	"\n AND cl.link_id = l.link_id "
			. "\n AND MATCH (".implode(", ",$searchable_fields).") AGAINST ('$searchword' IN BOOLEAN MODE)"
			.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' ) );
		$total = $database->loadResult();

	# Simple Search
	} else {
		
		# Retrieve categories
		if ( $limitstart == 0 ) {
			# Search Categories 
			$database->setQuery( "SELECT * FROM #__mt_cats AS c" 
				.	"\n WHERE c.cat_name LIKE '%$searchword%' "
				.	"\n AND cat_published='1' AND cat_approved='1' "
				.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' )
			);
			$cats = $database->loadObjectList();
		}
		
		/*
		foreach( $link_fields AS $lf ) {
			if ( substr($lf, 0, 5) == "cust_" ) {
				if ($cust[$lf]->searchable == 1) {
					$where[] = "$lf LIKE '%$searchword%'";
				}
			} else {
				$lf_varname = 'mt_search_'.$lf;
				if ($$lf_varname == 1) {
					$where[] = "$lf LIKE '%$searchword%'";
				}
			}
		}
		*/
		
		$words = explode( ' ', $searchword );
		$wheres = array();
		foreach ($words as $word) {
			$wheres2	= array();
			
			foreach( $link_fields AS $lf ) {
				if ( substr($lf, 0, 5) == "cust_" ) {
					if ($cust[$lf]->searchable == 1) {
						$wheres2[] = "LOWER($lf) LIKE '%$word%'";
					}
				} else {
					$lf_varname = 'mt_search_'.$lf;
					if ($$lf_varname == 1) {
						$wheres2[] = "LOWER($lf) LIKE '%$word%'";
					}
				}
			}

			$wheres[] = implode( ' OR ', $wheres2 );
		}
		$where = '(' . implode( ') OR (', $wheres ) . ')';

		# Retrieve Links
		$database->setQuery( "SELECT * FROM (#__mt_links AS l, #__mt_cl AS cl)"
			.	"\n	WHERE " 
			. "link_published='1' AND link_approved='1' AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' )"
			.	"\n AND cl.link_id = l.link_id "
			.	"\n AND cl.main = 1 "
			//.	( (count($where) > 0 ) ? ' AND (' . implode(' OR ', $where) .')' : '' )
			.	"\n AND (".$where.")"
			.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' )
			.	"\n ORDER BY $mt_first_search_order1 $mt_first_search_order2, $mt_second_search_order1 $mt_second_search_order2"
			.	"\n LIMIT $limitstart, $mt_fe_num_of_searchresults" );

		$links = $database->loadObjectList();

		# Get total
		$database->setQuery( "SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) "
			.	"\n	WHERE " 
			. "link_published='1' AND link_approved='1' AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' )"
			.	"\n AND cl.link_id = l.link_id "
			.	"\n AND cl.main = 1 "
			//.	( (count($where) > 0 ) ? ' AND (' . implode(' OR ', $where) .')' : '' )
			.	"\n AND (".$where.")"
			.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' )
			);

		$total = $database->loadResult();

	}
	
	applyMambots( $links );
	stripTags( $links );

	require_once($cms->get_path('root')."/includes/pageNavigation.php");
	$pageNav = new mosPageNav( $total, $limitstart, $mt_fe_num_of_searchresults );

	# Pathway
	$pathWay = new mtPathWay();

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assignRef('pathway', $pathWay);
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('searchword', stripslashes($searchword));
	$savant->assign('mt_show_email', $mt_show_email);
	$savant->assign('cat_id', $search_cat);
	$savant->assign('reviews', getReviews($links));
	$savant->assignRef('pageNav', $pageNav);
	$savant->assignRef('links', $links);
	if ( $limitstart == 0 ) $savant->assign('cats', $cats);

	$savant->assign('max_chars', $mt_fe_num_of_chars);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->display( 'page_searchResults.tpl.php' );
}

/***
* Advanced Search
*/

function advsearch( $option ) {
	global $cache, $_MT_LANG, $mainframe;

	$mainframe->setPageTitle( $_MT_LANG->ADVANCED_SEARCH ); 

	$cache->call( 'advsearch_cache', $option );
}

function advsearch_cache( $option ) {
	global $savantConf, $_MT_LANG, $database, $Itemid;

	# Get custom field's caption
	$database->setQuery( "SELECT name, value FROM #__mt_config WHERE name LIKE 'cust_%'" );

	# Pathway
	$pathWay = new mtPathWay();

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assign('custom_fields', $database->loadObjectList('name'));
	$savant->assignRef('pathway', $pathWay);
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);

	$savant->display( 'page_advSearch.tpl.php' );

}

function advsearch2( $option ) {
	global $database, $mainframe, $mosConfig_live_site, $mosConfig_offset, $savantConf, $_MT_LANG, $Itemid;
	global $mt_fe_num_of_searchresults, $mt_fe_num_of_chars, $mt_min_votes_to_show_rating, $mt_show_email, $mt_first_listing_order1, $mt_first_listing_order2, $mt_second_listing_order1, $mt_second_listing_order2;

	$mainframe->setPageTitle( $_MT_LANG->ADVANCED_SEARCH_RESULTS ); 
	$cms    =& cmsInstance('CMSCore');
	# Load up search ID if available
	$search_id = intval( mosGetParam( $_REQUEST, 'search_id', 0 ) );
	
	if ($search_id > 0) {
		$database->setQuery("SELECT search_text FROM #__mt_searchlog WHERE search_id ='".$search_id."'");
		$database->query();
		$_POST = unserialize($database->loadResult());
	}

	# Construct WHERE
	$link_fields = array('link_name', 'link_desc', 'address', 'city', 'postcode', 'state', 'country', 'email', 'website', 'telephone', 'fax', 'cust_1', 'cust_2', 'cust_3', 'cust_4', 'cust_5', 'cust_6', 'cust_7', 'cust_8', 'cust_9', 'cust_10', 'cust_11', 'cust_12', 'cust_13', 'cust_14', 'cust_15', 'cust_16', 'cust_17', 'cust_18', 'cust_19', 'cust_20', 'cust_21', 'cust_22', 'cust_23', 'cust_24', 'cust_25', 'cust_26', 'cust_27', 'cust_28', 'cust_29', 'cust_30' );

	$where = array();

	foreach( $link_fields AS $lf ) {
		$$lf =		mosGetParam( $_POST, $lf, '' );
		if ($$lf) $where[] = "$lf LIKE '%".$$lf."%'";
	}

	$limit = mosGetParam( $_GET, 'limit', $mt_fe_num_of_searchresults );
	$limitstart = mosGetParam( $_GET, 'limitstart', 0 );
	$price =			mosGetParam( $_POST, 'price', '' );
	$opt_price =	mosGetParam( $_POST, 'opt_price', '' );
	$link_rating = mosGetParam( $_POST, 'link_rating', '' );
	$opt_rating = mosGetParam( $_POST, 'opt_rating', '' );
	$link_votes = mosGetParam( $_POST, 'link_votes', '' );
	$opt_votes =	mosGetParam( $_POST, 'opt_votes', '' );
	$link_hits =	mosGetParam( $_POST, 'link_hits', '' );
	$opt_hits =		mosGetParam( $_POST, 'opt_hits', '' );

	// price
	if ( is_numeric($price) && $price >= 0 ) {
		switch($opt_price) {
			case 1:
				$where[] = "price = '$price'";
				break;
			case 2:
				$where[] = "price > '$price'";
				break;
			case 3:
				$where[] = "price < '$price'";
				break;
		}
	}

	// rating
	if ( is_numeric($link_rating) && $link_rating >= 0 && $link_rating <= 5 ) {
		switch($opt_rating) {
			case 1:
				$where[] = "link_rating = '$link_rating'";
				break;
			case 2:
				$where[] = "link_rating > '$link_rating'";
				break;
			case 3:
				$where[] = "link_rating < '$link_rating'";
				break;
		}
	}
	
	// votes
	if ( is_numeric($link_votes) && $link_votes >= 0 ) {
		switch($opt_votes) {
			case 1:
				$where[] = "link_votes = '$link_votes'";
				break;
			case 2:
				$where[] = "link_votes > '$link_votes'";
				break;
			case 3:
				$where[] = "link_votes < '$link_votes'";
				break;
		}
	}
	
	// hits
	if ( is_numeric($link_hits) && $link_hits >= 0 ) {
		switch($opt_hits) {
			case 1:
				$where[] = "link_hits = '$link_hits'";
				break;
			case 2:
				$where[] = "link_hits > '$link_hits'";
				break;
			case 3:
				$where[] = "link_hits < '$link_hits'";
				break;
		}
	}

	if ( count( $where ) > 0 ) {

		// Total Results
		$database->setQuery( "SELECT * FROM #__mt_links"
			.	( (count($where)) ? "\nWHERE ".implode(" AND ", $where) : '') );

		$database->query();
		$total = $database->getNumRows();

		if ( $search_id <= 0 && $total > 0 ) {

			# Store search for later retrieval.
			if ( $search_id < 1 ) {
				$database->setQuery("INSERT INTO #__mt_searchlog (search_text) VALUES ('".serialize($_POST)."')");		
				if(!$database->query())
				{
					echo $database->getErrorMsg();
				}
			}

			# Get the above search ID
			$database->setQuery("SELECT search_id FROM #__mt_searchlog WHERE search_text ='".serialize($_POST)."'");		
			$database->query();
			$search_id = $database->loadResult();

			$mainframe->addCustomHeadTag('<meta http-equiv="Refresh" content="1; URL='.sefRelToAbs("index.php?option=com_mtree&task=advsearch2&search_id=$search_id&Itemid=$Itemid").'">');
			# Savant template
			$savant = new Savant2($savantConf);
			$savant->_MT_LANG =& $_MT_LANG;
			$savant->assign('redirect_url', sefRelToAbs($mosConfig_live_site."/index.php?option=com_mtree&task=advsearch2&search_id=$search_id&Itemid=$Itemid"));
			$savant->display( 'page_advSearchRedirect.tpl.php' );

		} else {

			// Publishing
			$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

			$where[] = "( (publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now')  AND "
			. "(publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now') AND "
			.	"link_published = '1' AND link_approved = '1' )";

			// Links
			$database->setQuery( "SELECT l.*, cl.cat_id FROM (#__mt_links AS l, #__mt_cl AS cl)"
				.	( (count($where)) ? "\nWHERE ".implode(" AND ", $where) : '')
				.	"\n AND l.link_id = cl.link_id"
				.	"\n AND cl.main = 1"
				//.	"\nORDER BY link_name ASC"
				.	"\nORDER BY $mt_first_listing_order1 $mt_first_listing_order2, $mt_second_listing_order1 $mt_second_listing_order2"
				.	"\nLIMIT $limitstart, $limit" );

			$links = $database->loadObjectList();

			# Mambots
			applyMambots( $links );

			# Strip Tags
			stripTags( $links );

			# Page Navigation
			require_once($cms->get_path('root') ."/includes/pageNavigation.php");
			$pageNav = new mosPageNav( $total, $limitstart, $limit );

			# Pathway
			$pathWay = new mtPathWay();
			
			# Savant template
			$savant = new Savant2($savantConf);

			$savant->_MT_LANG =& $_MT_LANG;
			$savant->assignRef('pathway', $pathWay);
			$savant->assignRef('links', $links);
			$savant->assign('reviews', getReviews($links));
			$savant->assignRef('pageNav', $pageNav);
			$savant->assign('search_id', $search_id);
			$savant->assign('option', $option);
			$savant->assign('Itemid', $Itemid);

			$savant->assign('mt_show_email', $mt_show_email);
			$savant->assign('max_chars', $mt_fe_num_of_chars);
			$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

			$savant->display( 'page_advSearchResults.tpl.php' );

		}
	
	} else {
		
		// Return the Advanced Search page if no condition are specified
		advsearch( $option );

	}

}

function listalpha( $cat_id, $start, $limitstart, $option ) {
	global $cache, $_MT_LANG, $mainframe, $database;
	
	$cms    =& cmsInstance('CMSCore');
	$database->setQuery( "SELECT cat_name FROM #__mt_cats WHERE cat_id = '".$cat_id."'" );
	$cat_name = $database->loadResult();

	$mainframe->setPageTitle( sprintf($_MT_LANG->LIST_ALPHA_BY_LISTINGS_AND_CATS, strtoupper($start), $cat_name) ); 

	$cache->call( 'listalpha_cache', $cat_id, $start, $limitstart, $option );
}

function listalpha_cache( $cat_id, $start, $limitstart, $option ) {
	global $database, $savantConf, $_MT_LANG, $Itemid, $mosConfig_offset, $mainframe;
	global $mt_fe_num_of_links, $mt_show_email, $mt_fe_num_of_chars, $mt_min_votes_to_show_rating;

	$cms    =& cmsInstance('CMSCore');
	# Number (0-9)
	if ( $start == '0' ) {
		for( $i=48; $i <= 57; $i++) {
			$cond_seq_link[] = "link_name LIKE '".chr($i)."%'";
			$cond_seq_cat[] = "cat_name LIKE '".chr($i)."%'";
		}
		$where[] = "(".implode(" OR ",$cond_seq_link).")";
		$where_cat[] = "(".implode(" OR ",$cond_seq_cat).")";

	# Alphabets (A-Z)
	} elseif ( eregi("[a-z0-9]{1}[0-9]*", $start) ) {
		$where[] = "link_name LIKE '".$start."%'";
		$where_cat[] = "cat_name LIKE '".$start."%'";
	}
	
	# SQL condition to display category specific results
	$subcats = implode(", ",getSubCats_Recursive($cat_id));

	if ($subcats) $where[] = "cl.cat_id IN (" . $subcats . ")";
	if ($subcats) $where_cat[] = "cat_parent IN (" . $subcats . ")";

	// Get Total results - Links
	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	$sql = "SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) ";
	$where[] = "l.link_id = cl.link_id";
	$where[] = "cl.main = '1'";
	$where[] = "link_approved = '1'";
	$where[] = "link_published = '1'";
	$where[] = "( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  )";
	$where[] = "( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' )";
	
	$sql .= (count( $where ) ? " WHERE " . implode( ' AND ', $where ) : "");

	$database->setQuery( $sql );
	$total = $database->loadResult();

	// Get Links
	$link_sql = "SELECT l.*, cl.cat_id AS cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) ";
	$link_sql .= (count( $where ) ? " WHERE " . implode( ' AND ', $where ) : "");
	$link_sql .= " AND l.link_id = cl.link_id AND cl.main = '1' ";
	$link_sql .= "ORDER BY link_featured DESC, link_name ASC ";
	$link_sql .= "LIMIT $limitstart, $mt_fe_num_of_links";

	# Shows categories if this is the first page. ie: $limitstart = 0
	$num_of_cats = 0;
		
	if ( $limitstart == 0 ) {
		// Get Categories - Normal
		$cat_sql = "SELECT cat_name AS text, cat_id AS value FROM #__mt_cats WHERE ";
		$cat_sql .= implode( ' AND ', $where_cat );
		$cat_sql .= "AND cat_approved = '1' ";
		$cat_sql .= "AND cat_published = '1' ";
		$cat_sql .= "AND cat_featured = '0' ";
		$cat_sql .= "ORDER BY cat_name ASC ";

		// Get Categories - Featured
		$cat_f_sql = "SELECT * FROM #__mt_cats WHERE ";
		$cat_f_sql .= implode( ' AND ', $where_cat );
		$cat_f_sql .= "AND cat_approved = '1' ";
		$cat_f_sql .= "AND cat_published = '1' ";
		$cat_f_sql .= "AND cat_featured = '1' ";
		$cat_f_sql .= "ORDER BY ordering ASC ";

		# SQL - Normal Categories
		$lists = array();
		$categories[] = mosHTML::makeOption( '-1', $_MT_LANG->SELECT_CATEGORY );
		$database->setQuery( $cat_sql );
		$normal_cat = $database->loadObjectList();
		$categories = array_merge( $categories, $normal_cat );
		$num_of_cats = count( $normal_cat );

		$lists['cat_id'] = mosHTML::selectList( $categories, 'cat_id', 'class="inputbox" size="1" onchange="document.adminForm.submit();"',	'value', 'text', '-1' );

		# SQL - Featured Categories
		$database->setQuery( $cat_f_sql );
		$featured_cats = $database->loadObjectList();
		$num_of_cats += count( $featured_cats );

	}

	# SQL - Links
	$database->setQuery( $link_sql );
	$links = $database->loadObjectList();
	

	# Mambots
	applyMambots( $links );

	# Strip Tags
	stripTags( $links );

	# Page Navigation
	require_once($cms->get_path('root')."/includes/pageNavigation.php");
	$pageNav = new mosPageNav( $total, $limitstart, $mt_fe_num_of_links );
	
	# Pathway
	$pathWay = new mtPathWay( 0 );

	# Load custom template
	loadCustomTemplate( $cat_id, $savantConf);

	# Savant Template
	$savant = new Savant2($savantConf);
	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assign('option', $option);
	$savant->assign('cat_id', $cat_id);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('start', $start);
	$savant->assign('num_of_cats', $num_of_cats);
	$savant->assign('mt_show_email', $mt_show_email);
	$savant->assignRef('pathway', $pathWay);
	$savant->assignRef('links', $links);
	$savant->assign('reviews', getReviews($links));
	$savant->assignRef('lists', $lists);
	$savant->assignRef('featured_cats', $featured_cats);
	$savant->assignRef('pageNav', $pageNav);

	$savant->assign('max_chars', $mt_fe_num_of_chars);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->display( 'page_listAlpha.tpl.php' );

}

function listpopular( $cat_id, $option ) {
	global $cache, $database, $mainframe, $_MT_LANG;

	$database->setQuery( "SELECT cat_name FROM #__mt_cats WHERE cat_id = '".$cat_id."' LIMIT 1" );
	$cat_name = $database->loadResult();

	$mainframe->setPageTitle( $_MT_LANG->POPULAR_LISTING2 . $cat_name ); 

	$cache->call( 'listpopular_cache', $cat_id, $option );
}

function listpopular_cache( $cat_id, $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $mosConfig_offset;
	global $mt_fe_num_of_popularlisting, $mt_fe_num_of_chars, $mt_min_votes_to_show_rating; 

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	# Retrieve Links
	$subcats = implode(", ",getSubCats_Recursive($cat_id));

	$database->setQuery( "SELECT * FROM (#__mt_links AS l, #__mt_cl AS cl) "
		. "WHERE link_published='1' && link_approved='1' "
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		. "\n AND l.link_id = cl.link_id "
		. "\n AND cl.main = 1 "
		.	( ($subcats) ? "\n AND cl.cat_id IN (" . $subcats . ") " : '')
		.	"ORDER BY (link_hits / ((UNIX_TIMESTAMP() - UNIX_TIMESTAMP(link_created))/86400)) DESC "
		.	"LIMIT $mt_fe_num_of_popularlisting" );
	$links = $database->loadObjectList();

	# Mambots
	applyMambots( $links );

	# Strip Tags
	stripTags( $links );

	# Pathway
	$pathWay = new mtPathWay();

	# Load custom template
	loadCustomTemplate( $cat_id, $savantConf);

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assignRef('pathway', $pathWay);
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);

	$savant->assignRef('links', $links);
	$savant->assign('reviews', getReviews($links));
	
	$savant->assign('max_chars', $mt_fe_num_of_chars);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->display( 'page_popular.tpl.php' );
}

function listmostrated( $cat_id, $option ) {
	global $cache, $database, $mainframe, $_MT_LANG;

	$database->setQuery( "SELECT cat_name FROM #__mt_cats WHERE cat_id = '".$cat_id."' LIMIT 1" );
	$cat_name = $database->loadResult();

	$mainframe->setPageTitle( $_MT_LANG->MOST_RATED_LISTING2 . $cat_name ); 

	$cache->call( 'listmostrated_cache', $cat_id, $option );
}

function listmostrated_cache( $cat_id, $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $mosConfig_offset;
	global $mt_fe_num_of_mostrated, $mt_fe_num_of_chars, $mt_min_votes_to_show_rating, $mt_show_email; 

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	# Retrieve Links
	$subcats = implode(", ",getSubCats_Recursive($cat_id));

	$database->setQuery( "SELECT * FROM (#__mt_links AS l, #__mt_cl AS cl) "
		. "WHERE link_published='1' && link_approved='1' "
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		. "\n AND l.link_id = cl.link_id "
		. "\n AND cl.main = 1 "
		.	( ($subcats) ? "\n AND cl.cat_id IN (" . $subcats . ") " : '')
		.	"ORDER BY link_votes DESC, link_rating DESC " 
		.	"LIMIT $mt_fe_num_of_mostrated" );
	$links = $database->loadObjectList();

	# Mambots
	applyMambots( $links );

	# Strip Tags
	stripTags( $links );

	# Pathway
	$pathWay = new mtPathWay();

	# Load custom template
	loadCustomTemplate( $cat_id, $savantConf);

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assignRef('pathway', $pathWay);
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('mt_show_email', $mt_show_email);

	$savant->assignRef('links', $links);
	$savant->assign('reviews', getReviews($links));
	
	$savant->assign('max_chars', $mt_fe_num_of_chars);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->display( 'page_mostRated.tpl.php' );
}

function listtoprated( $cat_id, $option ) {
	global $cache, $database, $mainframe, $_MT_LANG;

	$database->setQuery( "SELECT cat_name FROM #__mt_cats WHERE cat_id = '".$cat_id."' LIMIT 1" );
	$cat_name = $database->loadResult();

	$mainframe->setPageTitle( $_MT_LANG->TOP_RATED_LISTING2 . $cat_name ); 

	$cache->call( 'listtoprated_cache', $cat_id, $option );
}

function listtoprated_cache( $cat_id, $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $mosConfig_offset;
	global $mt_fe_num_of_toprated, $mt_min_votes_for_toprated, $mt_show_email, $mt_fe_num_of_chars, $mt_min_votes_to_show_rating;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	# Retrieve Links
	$subcats = implode(", ",getSubCats_Recursive($cat_id));

	$database->setQuery( "SELECT * FROM (#__mt_links AS l, #__mt_cl AS cl) "
		. "WHERE link_published='1' && link_approved='1' "
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		. "\n AND l.link_id = cl.link_id "
		. "\n AND cl.main = 1 "
		.	( ( isset($mt_min_votes_for_toprated) && $mt_min_votes_for_toprated >= 1 ) ? "\n AND l.link_votes >= ".$mt_min_votes_for_toprated." " : '' )
		.	( ($subcats) ? "\n AND cl.cat_id IN (" . $subcats . ") " : '')
		.	"ORDER BY link_rating DESC, link_votes DESC  " 
		.	"LIMIT $mt_fe_num_of_toprated" );
	$links = $database->loadObjectList();

	# Mambots
	applyMambots( $links );

	# Strip Tags
	stripTags( $links );

	# Pathway
	$pathWay = new mtPathWay();

	# Load custom template
	loadCustomTemplate( $cat_id, $savantConf);

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assignRef('pathway', $pathWay);
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('mt_show_email', $mt_show_email);

	$savant->assignRef('links', $links);
	$savant->assign('reviews', getReviews($links));
	
	$savant->assign('max_chars', $mt_fe_num_of_chars);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->display( 'page_topRated.tpl.php' );
}

function listmostreview( $cat_id, $option ) {
	global $cache, $database, $mainframe, $_MT_LANG;

	$database->setQuery( "SELECT cat_name FROM #__mt_cats WHERE cat_id = '".$cat_id."' LIMIT 1" );
	$cat_name = $database->loadResult();

	$mainframe->setPageTitle( $_MT_LANG->MOST_REVIEWED_LISTING2 . $cat_name ); 

	$cache->call( 'listmostreview_cache', $cat_id, $option );
}

function listmostreview_cache( $cat_id, $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $mosConfig_offset;
	global $mt_fe_num_of_mostreview, $mt_fe_num_of_chars, $mt_min_votes_to_show_rating,  $mt_show_email; 

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	# Retrieve Links
	$subcats = implode(", ",getSubCats_Recursive($cat_id));

	$database->setQuery( "SELECT COUNT(r.link_id) AS reviews, l.*, cl.cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) "
		.	"LEFT JOIN #__mt_reviews AS r ON r.link_id = l.link_id "
		.	"WHERE l.link_published='1' && l.link_approved='1' && r.rev_approved='1' "
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		. "\n AND l.link_id = cl.link_id "
		. "\n AND cl.main = 1 "
		.	( ($subcats) ? "\n AND cl.cat_id IN (" . $subcats . ") " : '')
		.	"GROUP BY r.link_id "
		.	"ORDER BY reviews DESC " 
		.	"LIMIT $mt_fe_num_of_mostreview" );
	$links = $database->loadObjectList();

	# Mambots
	applyMambots( $links );

	# Strip Tags
	stripTags( $links );

	# Pathway
	$pathWay = new mtPathWay();

	# Load custom template
	loadCustomTemplate( $cat_id, $savantConf);

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assignRef('pathway', $pathWay);
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('mt_show_email', $mt_show_email);

	$savant->assignRef('links', $links);
	$savant->assign('reviews', getReviews($links));

	$savant->assign('max_chars', $mt_fe_num_of_chars);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->display( 'page_mostReviewed.tpl.php' );
}

function listnew( $cat_id, $limitstart, $option ) {
	global $cache, $database, $mainframe, $_MT_LANG;

	$database->setQuery( "SELECT cat_name FROM #__mt_cats WHERE cat_id = '".$cat_id."' LIMIT 1" );
	$cat_name = $database->loadResult();

	$mainframe->setPageTitle( $_MT_LANG->NEW_LISTING2 . $cat_name ); 

	$cache->call( 'listnew_cache', $cat_id, $limitstart, $option );
}

function listnew_cache( $cat_id, $limitstart, $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $mosConfig_offset;
	global $mt_fe_num_of_newlisting, $mt_fe_total_newlisting, $mt_fe_num_of_chars, $mt_min_votes_to_show_rating, $mt_show_email;

	$cms    =& cmsInstance('CMSCore');
	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
	
	if ( ($limitstart + $mt_fe_num_of_newlisting) > $mt_fe_total_newlisting ) {
		$link_count = $mt_fe_total_newlisting - $limitstart;
	} else {
		$link_count = $mt_fe_num_of_newlisting;
	}

	# Retrieve Links
	$subcats = implode(", ",getSubCats_Recursive($cat_id));

	$database->setQuery( "SELECT * FROM (#__mt_links AS l, #__mt_cl AS cl) "
		. "WHERE link_published='1' && link_approved='1' "
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		. "\n AND l.link_id = cl.link_id "
		. "\n AND cl.main = 1 "
		.	( ($subcats) ? "\n AND cl.cat_id IN (" . $subcats . ") " : '')
		.	"ORDER BY link_created DESC " 
		.	"LIMIT $limitstart, $link_count" );
	$links = $database->loadObjectList();
	
	# Get the total available new listings
	$database->setQuery( "SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) "
		. "WHERE link_published='1' && link_approved='1' "
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		. "\n AND l.link_id = cl.link_id "
		. "\n AND cl.main = 1 "
		.	( ($subcats) ? "\n AND cl.cat_id IN (" . $subcats . ") " : '')
		);
	$total = $database->loadResult();
	
	if( $total > $mt_fe_total_newlisting ) {
		$total = $mt_fe_total_newlisting;
	}

	# Mambots
	applyMambots( $links );

	# Strip Tags
	stripTags( $links );

	# Pathway
	$pathWay = new mtPathWay();

	# Page Navigation
	require_once($cms->get_path('root')."/includes/pageNavigation.php");
	$pageNav = new mosPageNav( $total, $limitstart, count($links) );

	# Load custom template
	loadCustomTemplate( $cat_id, $savantConf);

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assignRef('pathway', $pathWay);
	$savant->assign('pageNav', $pageNav);
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('cat_id', $cat_id);
	$savant->assignRef('links', $links);
	$savant->assign('reviews', getReviews($links));
	$savant->assign('mt_show_email', $mt_show_email);

	$savant->assign('max_chars', $mt_fe_num_of_chars);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->display( 'page_new.tpl.php' );

}

function listfeatured( $cat_id, $limitstart, $option ) {
	global $cache, $database, $mainframe, $_MT_LANG;

	$database->setQuery( "SELECT cat_name FROM #__mt_cats WHERE cat_id = '".$cat_id."' LIMIT 1" );
	$cat_name = $database->loadResult();

	$mainframe->setPageTitle( $_MT_LANG->FEATURED_LISTING2 . $cat_name ); 

	$cache->call( 'listfeatured_cache', $cat_id, $limitstart, $option );
}

function listfeatured_cache( $cat_id, $limitstart, $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $mosConfig_offset;
	global $mt_fe_num_of_featured, $mt_show_email, $mt_fe_num_of_chars, $mt_min_votes_to_show_rating; 

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
	$cms    =& cmsInstance('CMSCore');
	
	# Retrieve Links
	$subcats = implode(", ",getSubCats_Recursive($cat_id));

	$database->setQuery( "SELECT * FROM (#__mt_links AS l, #__mt_cl AS cl) "
		. "WHERE link_published='1' && link_approved='1' && link_featured='1' "
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		. "\n AND l.link_id = cl.link_id "
		. "\n AND cl.main = 1 "
		.	( ($subcats) ? "\n AND cl.cat_id IN (" . $subcats . ") " : '')
		.	"ORDER BY link_name ASC " 
		.	"LIMIT $limitstart, $mt_fe_num_of_featured" );
	$links = $database->loadObjectList();

	# Mambots
	applyMambots( $links );

	# Strip Tags
	stripTags( $links );

	# Get total featured listing
	$database->setQuery( "SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) "
		. "WHERE link_published='1' && link_approved='1' && link_featured='1' "
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) " 
		. "\n AND l.link_id = cl.link_id "
		. "\n AND cl.main = 1 "
		.	( ($subcats) ? "\n AND cl.cat_id IN (" . $subcats . ") " : '')
		//.	"\n AND cat_id IN (" . $subcats . ") "
		);
	$total = $database->loadResult();

	# Pathway
	$pathWay = new mtPathWay();

	# Page Navigation
	require_once($cms->get_path('root')."/includes/pageNavigation.php");
	$pageNav = new mosPageNav( $total, $limitstart, $mt_fe_num_of_featured );

	# Load custom template
	loadCustomTemplate( $cat_id, $savantConf);

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assignRef('pathway', $pathWay);
	$savant->assign('pageNav', $pageNav);
	$savant->assign('option', $option);
	$savant->assign('cat_id', $cat_id);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('mt_show_email', $mt_show_email);

	$savant->assignRef('links', $links);
	$savant->assign('reviews', getReviews($links));

	$savant->assign('max_chars', $mt_fe_num_of_chars);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->display( 'page_featured.tpl.php' );

}

/***
* Visit URL
*/

function visit( $link_id ) {
	global $database, $my, $mosConfig_offset;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	$database->setQuery( "SELECT website FROM #__mt_links"
		.	"\n	WHERE link_published='1' AND link_approved > 0 AND link_id='".$link_id."' " 
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
	);

	$database->loadObject( $link );

	if (empty($link)) {
		
		echo _NOT_EXIST;

	} else {
		
		# User IP Address
		$vote_ip = getenv( 'REMOTE_ADDR' );
	
		# Check if this visit has been counted before
		if ( $my->id == 0 ) {
			$database->setQuery( "SELECT log_date FROM #__mt_log WHERE link_id ='".$link_id."' AND log_ip = '".$vote_ip."' AND log_type = 'visit'" );
		} else {
			$database->setQuery( "SELECT log_date FROM #__mt_log WHERE link_id ='".$link_id."' AND user_id = '".$my->id."' AND log_type = 'visit'" );
		}
		
		$counted = false;
		$counted = ($database->loadResult() <> '') ? true : false;
		
		if ( !$counted ) {

			# Update #__mt_log table
			$database->setQuery( "INSERT INTO #__mt_log "
				.	"( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id` )"
				.	"VALUES ( '$vote_ip', 'visit', '$my->id', '$now', '$link_id')");
			if (!$database->query()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}

			# Update #__mt_links table
			$database->setQuery( "UPDATE #__mt_links "
				.	" SET link_visited = link_visited + 1 "
				.	"WHERE link_id = '$link_id' ");
			if (!$database->query()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}

		}

		mosRedirect( $link->website );

	}

}

/***
* View Listing
*/
function viewlink( $link_id, $limitstart, $option ) {
	global $cache, $savantConf, $mosConfig_MetaTitle, $mosConfig_MetaAuthor, $mainframe;

	$link = loadLink( $link_id, $savantConf, $custom_fields, $params );

	# Set Page Title
	$mainframe->setPageTitle( $link->link_name ); 

	# Add META tags
	if ($mosConfig_MetaTitle=='1') {
		$mainframe->addMetaTag( 'title' , $link->link_name );
	}
	if ($mosConfig_MetaAuthor=='1') {
		$mainframe->addMetaTag( 'author' , $link->owner );
	}

	if ($link->metadesc <> '') $mainframe->prependMetaTag( 'description', $link->metadesc );
	if ($link->metakey <> '') $mainframe->prependMetaTag( 'keywords', $link->metakey );

	$cache->call( 'viewlink_cache', $link, $limitstart, $custom_fields, $params, $option );

}

function viewlink_cache( $link, $limitstart, $custom_fields, $params, $option ) {
	global $database, $mosConfig_live_site, $mosConfig_MetaTitle, $mosConfig_MetaAuthor, $mosConfig_offset, $my;
	global $_MT_LANG, $savantConf, $Itemid, $mt_fe_num_of_reviews, $mt_hit_lag, $mt_show_email, $mt_allow_html;
	global $mt_listing_image_dir, $mt_cat_image_dir, $mt_allow_html, $mt_min_votes_to_show_rating, $mt_min_votes_to_show_rating;

	$cms	=& cmsInstance('CMSCore');
	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
	//$link = loadLink( $link_id, $savantConf, $custom_fields, $params );
	$link_id = $link->link_id;

	if ( !isset($link->link_id) || $link->link_id <= 0 ) {
		echo _NOT_EXIST;
	} else {

		# Increase 1 hit
		$cookiename = "mtlink_hit$link->link_id";
		$visited = mosGetParam( $_COOKIE, $cookiename, '0' );
		
		if (!$visited) {
			$database->setQuery( "UPDATE #__mt_links SET link_hits=link_hits+1 WHERE link_id='".$link_id."'" );
			$database->query();
		}

		setcookie( $cookiename, '1', time()+$mt_hit_lag );
	
		# Get reviews
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reviews AS r"
			.	"\nWHERE link_id = '".$link_id."' AND r.rev_approved = 1"
			);
		$total_reviews = $database->loadResult();

		$database->setQuery( "SELECT r.*, u.username FROM #__mt_reviews AS r"
			.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"
			.	"\nWHERE link_id = '".$link_id."' AND r.rev_approved = 1 ORDER BY rev_date DESC" 
			.	"\nLIMIT $limitstart, $mt_fe_num_of_reviews"
			);
		$reviews = $database->loadObjectList();

		# Add <br /> to all new lines
		for( $i=0; $i<count($reviews); $i++ ) {
			$reviews[$i]->rev_text = nl2br($reviews[$i]->rev_text);
		}

		# Page Navigation
		require_once($cms->get_path('root')."/includes/pageNavigation.php");
		$pageNav = new mosPageNav( $total_reviews, $limitstart, $mt_fe_num_of_reviews );

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Mambots
		global $_MAMBOTS;
		$page = 0;

		# new line to HTML line break
		if (!$mt_allow_html) {
			$link->text = nl2br($link->text);
		}

		$savant = new Savant2($savantConf);

		$savant->_MT_LANG =& $_MT_LANG;
		$savant->assign('option', $option);
		$savant->assign('Itemid', $Itemid);
		$savant->assign('pathway', $pathWay);
		$savant->assign('pageNav', $pageNav);
		$savant->assign('link_id', $link_id);

		$savant->assign('link', $link);
		$savant->assign('my', $my);
		$savant->assign('custom_fields', $custom_fields);
		$savant->assign('reviews', $reviews);
		$savant->assign('total_reviews', ((isset($total_reviews)) ? $total_reviews : 0 ));
		$savant->assign('mt_show_email', $mt_show_email);
		$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

		$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);
		$savant->assign('listing_image_dir', $mosConfig_live_site.$mt_listing_image_dir);
		$savant->assign('cat_image_dir', $mosConfig_live_site.$mt_cat_image_dir);
		
		$savant->assign('mt_show_review', $params->get( 'show_review' ));
		$savant->assign('mt_show_rating', $params->get( 'show_rating' ));

		// mambots results
		$savant->assign('mambotAfterDisplayTitle', $_MAMBOTS->trigger( 'onAfterDisplayTitle', array( &$link, &$params, $page ) ));
		$savant->assign('mambotBeforeDisplayContent', $_MAMBOTS->trigger( 'onBeforeDisplayContent', array( &$link, &$params, $page ) ));
		$savant->assign('mambotAfterDisplayContent', $_MAMBOTS->trigger( 'onAfterDisplayContent', array( &$link, &$params, $page ) ));

		// Output
		$savant->display( 'page_listing.tpl.php' );

	}
}

function printlink( $link_id, $option ) {
	global $database, $_MAMBOTS, $_MT_LANG, $savantConf, $Itemid, $mt_fe_num_of_reviews, $mosConfig_live_site, $mt_listing_image_dir, $mosConfig_offset;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
	$link = loadLink( $link_id, $savantConf, $custom_fields, $params );

	if (empty($link)) {
		echo _NOT_EXIST;
	} else {

		$page = 0;

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);

		$savant->_MT_LANG =& $_MT_LANG;
		$savant->assign('link', $link);
		$savant->assign('custom_fields', $custom_fields);
		$savant->assign('listing_image_dir',$mosConfig_live_site.$mt_listing_image_dir);

		# mambots results
		$savant->assign('mambotAfterDisplayTitle', $_MAMBOTS->trigger( 'onAfterDisplayTitle', array( &$link, &$params, $page ) ));
		$savant->assign('mambotBeforeDisplayContent', $_MAMBOTS->trigger( 'onBeforeDisplayContent', array( &$link, &$params, $page ) ));
		$savant->assign('mambotAfterDisplayContent', $_MAMBOTS->trigger( 'onAfterDisplayContent', array( &$link, &$params, $page ) ));

		$savant->display( 'page_print.tpl.php' );

	}
}

/***
* Report Listing
*/
function report( $link_id, $option ) {
	global $cache, $_MT_LANG, $mainframe, $savantConf;

	$link = loadLink( $link_id, $savantConf, $custom_fields, $params );
	$mainframe->setPageTitle( $_MT_LANG->REPORT2 . $link->link_name );

	$cache->call( 'report_cache', $link, $custom_fields, $params, $option );
}

function report_cache( $link, $custom_fields, $params, $option ) {
	global $database, $savantConf;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $custom_fields, $pathWay, $params );

	if (empty($link)) {
		echo _NOT_EXIST;
	} else {
		$savant->display( 'page_report.tpl.php' );
	}

}

function send_report( $link_id, $option ) {
	global $database, $_MT_LANG, $Itemid, $my;
	global $mosConfig_live_site, $mosConfig_sitename, $mosConfig_offset;
	global $mt_show_report, $mt_admin_email;

	if ( $mt_show_report == 0 ) {
		
		echo _NOT_EXIST;

	} else {

		$link = new mtLinks( $database );
		$link->load( $link_id );

		$your_name = trim( mosGetParam( $_POST, 'your_name', '' ) );
		$report_type = trim( mosGetParam( $_POST, 'report_type', '' ) );
		$report_type2 = "REPORT_PROBLEM_".$report_type;

		$message = trim( mosGetParam( $_POST, 'message', '' ) );
		$text = sprintf( $_MT_LANG->REPORT_EMAIL, sefRelToAbs($mosConfig_live_site."/index.php?option=com_mtree&task=viewlink&link_id=$link_id&Itemid=$Itemid"), $your_name, $_MT_LANG->$report_type2, $link_id, $message);

		$subject = $_MT_LANG->REPORT." - ".$mosConfig_sitename;

		mosMailToAdmin( $subject, $text );

		if( $my->id > 0 )  {
			# User is logged on, store user ID
			$database->setQuery( "INSERT INTO #__mt_reports "
				.	"( `link_id` , `user_id` , `subject` , `comment`, created ) "
				.	"VALUES ($link_id, $my->id, '".$_MT_LANG->$report_type2."', '".$message."', '".date( 'Y-m-d H:i:s', time() + $mosConfig_offset * 60 * 60 )."')");
			
		} else {
			# User is not logged on, store Guest name
			$database->setQuery( "INSERT INTO #__mt_reports "
				.	"( `link_id` , `guest_name` , `subject` , `comment`, created ) "
				.	"VALUES ($link_id, '".$your_name."', '".$_MT_LANG->$report_type2."', '".$message."', '".date( 'Y-m-d H:i:s', time() + $mosConfig_offset * 60 * 60 )."')");

		}
		
		if (!$database->query()) {
			echo "<script> alert('".$database->stderr()."');</script>\n";
			exit();
		}

		mosRedirect( "index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid", $_MT_LANG->REPORT_HAVE_BEEN_SENT);
	}

}

/***
* Report Listing
*/
function claim( $link_id, $option ) {
	global $cache, $_MT_LANG, $mainframe, $savantConf;

	$link = loadLink( $link_id, $savantConf, $custom_fields, $params );
	$mainframe->setPageTitle( $_MT_LANG->CLAIM_LISTING .": ". $link->link_name );

	$cache->call( 'claim_cache', $link, $custom_fields, $params, $option );
}

function claim_cache( $link, $custom_fields, $params, $option ) {
	global $database, $_MAMBOTS, $_MT_LANG, $savantConf, $Itemid, $my, $mosConfig_live_site;
	global $mt_show_email, $mt_listing_image_dir, $mainframe, $mosConfig_offset, $mt_min_votes_to_show_rating;

	# Savant Template
	$savant = new Savant2($savantConf);

	$page = 0;
	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('pathway', $pathWay);
	$savant->assign('link_id', $link->link_id);
	$savant->assign('mt_show_email', $mt_show_email);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->assign('listing_image_dir', $mosConfig_live_site.$mt_listing_image_dir);
	$savant->assign('link', $link);
	$savant->assign('custom_fields', $custom_fields);
	$savant->assign('user_id',$my->id);

	// mambots results
	$savant->assign('mambotAfterDisplayTitle', $_MAMBOTS->trigger( 'onAfterDisplayTitle', array( &$link, &$params, $page ) ));
	$savant->assign('mambotBeforeDisplayContent', $_MAMBOTS->trigger( 'onBeforeDisplayContent', array( &$link, &$params, $page ) ));
	$savant->assign('mambotAfterDisplayContent', $_MAMBOTS->trigger( 'onAfterDisplayContent', array( &$link, &$params, $page ) ));

	if ( $my->id <= 0 ) {
		
		# User is not logged in
		$savant->assign('error_msg', $_MT_LANG->PLEASE_LOGIN_BEFORE_CLAIM);
		$savant->display( 'page_errorListing.tpl.php' );

	} else {

		if (empty($link)) {
			echo _NOT_EXIST;
		} else {
			$savant->display( 'page_claim.tpl.php' );
		}
	}
}

function send_claim( $link_id, $option ) {
	global $database, $_MT_LANG, $Itemid, $my;
	global $mosConfig_live_site, $mosConfig_sitename, $mosConfig_offset;
	global $mt_show_claim, $mt_admin_email;

	if ( $mt_show_claim == 0 || $my->id <= 0 ) {
		
		echo _NOT_EXIST;

	} else {

		$link = new mtLinks( $database );
		$link->load( $link_id );

		$message = trim( mosGetParam( $_POST, 'message', '' ) );
		$text = sprintf( $_MT_LANG->CLAIM_EMAIL, sefRelToAbs($mosConfig_live_site."/index.php?option=com_mtree&task=viewlink&link_id=$link_id&Itemid=$Itemid"), $link->link_name, $link_id, $message);

		$subject = $_MT_LANG->CLAIM." - ".$mosConfig_sitename;

		mosMailToAdmin( $subject, stripslashes ($text) );

		# User is logged on, store user ID
		$database->setQuery( "INSERT INTO #__mt_claims "
			.	"( `link_id` , `user_id` , `comment`, `created` ) "
			.	"VALUES ($link_id, $my->id, '".$message."', '".date( 'Y-m-d H:i:s', time() + $mosConfig_offset * 60 * 60 )."')");
		
		if (!$database->query()) {
			echo "<script> alert('".$database->stderr()."');</script>\n";
			exit();
		}

		mosRedirect( "index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid", $_MT_LANG->CLAIM_HAVE_BEEN_SENT );
	}

}

/***
* Delete Listing
*/
function deletelisting( $link_id, $option ) {
	global $database, $savantConf, $_MT_LANG, $mosConfig_offset, $Itemid, $mt_user_allowdelete, $my;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	$database->setQuery( "SELECT l.*, cl.cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE "
		.	"\n l.link_published='1' AND l.link_approved > 0 AND l.link_id='".$link_id."'" 
		.	"\n AND cl.link_id = l.link_id AND cl.main='1'"
		.	"\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		.	"\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		);
	$database->loadObject( $link );

	if ($mt_user_allowdelete && $my->id == $link->user_id && $my->id > 0 ) {

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);

		$savant->_MT_LANG =& $_MT_LANG;
		$savant->assign('option', $option);
		$savant->assign('Itemid', $Itemid);
		$savant->assign('link', $link);
		$savant->assign('pathway', $pathWay);
		$savant->display( 'page_confirmDelete.tpl.php' );

	} else {
		echo _NOT_EXIST;
	}

}

function confirmdelete( $link_id, $option ) {
	global $database, $mosConfig_offset, $my, $mt_user_allowdelete, $_MT_LANG, $mt_notifyadmin_delete, $mt_admin_email, $mosConfig_sitename;
	global $mosConfig_mailfrom, $mosConfig_fromname;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	$database->setQuery( "SELECT * FROM #__mt_links WHERE "
		.	"\n	link_published='1' AND link_approved > 0 AND link_id='".$link_id."'" 
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		);
	$database->loadObject( $link );

	if ($mt_user_allowdelete && $my->id == $link->user_id && $my->id > 0 ) {
		
		$link = new mtLinks( $database );
		$link->load( $link_id );

		if ( $mt_notifyadmin_delete == 1 ) {

			// Get owner's email
			$database->setQuery( "SELECT email FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
			$my_email = $database->loadResult();

			$subject = $_MT_LANG->ADMIN_NOTIFY_DELETE_SUBJECT;
			$body = sprintf($_MT_LANG->ADMIN_NOTIFY_DELETE_MSG, $link->link_name, $link->link_name, $link->link_id, $my->username, $my_email, $link->link_created);

			//mosMail( $mosConfig_mailfrom, $mosConfig_sitename, $mt_admin_email, $subject, wordwrap($body) );
			mosMailToAdmin( $subject, $body );
			
		}
		
		$link->updateLinkCount( -1 );
		$link->delLink();

		mosRedirect( "index.php?option=$option&task=mylisting&link_id=$link_id&Itemid=$Itemid", $_MT_LANG->LISTING_HAVE_BEEN_DELETED );

	} else {
		echo _NOT_EXIST;
	}

}

/***
* Review
*/
function writereview( $link_id, $option ) {
	global $cache, $_MT_LANG, $mainframe, $savantConf;

	$link = loadLink( $link_id, $savantConf, $custom_fields, $params );
	$mainframe->setPageTitle( $_MT_LANG->REVIEW ." ". $link->link_name );
	
	$cache->call( 'writereview_cache', $link, $custom_fields, $params, $option );
}

function writereview_cache( $link, $custom_fields, $params, $option ) {
	global $database, $_MAMBOTS, $_MT_LANG, $savantConf, $Itemid, $my, $mosConfig_offset, $mainframe;
	global $mt_user_review_once, $mt_user_review, $mt_show_email, $mosConfig_live_site, $mt_listing_image_dir, $mt_min_votes_to_show_rating;
	
	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
	//$link = loadLink( $link_id, $savantConf, $custom_fields, $params );
	//$link_id = $link->link_id;

	if (empty($link)) {
		echo _NOT_EXIST;
	} else {

		$page = 0;

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);

		$savant->_MT_LANG =& $_MT_LANG;
		$savant->assign('option', $option);
		$savant->assign('Itemid', $Itemid);
		$savant->assign('my', $my);
		$savant->assign('pathway', $pathWay);
		$savant->assign('link_id', $link->link_id);
		$savant->assign('mt_show_email', $mt_show_email);
		$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);
		$savant->assign('listing_image_dir', $mosConfig_live_site.$mt_listing_image_dir);
		$savant->assign('link', $link);
		$savant->assign('custom_fields', $custom_fields);

		// mambots results
		$savant->assign('mambotAfterDisplayTitle', $_MAMBOTS->trigger( 'onAfterDisplayTitle', array( &$link, &$params, $page ) ));
		$savant->assign('mambotBeforeDisplayContent', $_MAMBOTS->trigger( 'onBeforeDisplayContent', array( &$link, &$params, $page ) ));
		$savant->assign('mambotAfterDisplayContent', $_MAMBOTS->trigger( 'onAfterDisplayContent', array( &$link, &$params, $page ) ));

		# Check if this user has reviewed this listing previously
		$database->setQuery( "SELECT rev_id FROM #__mt_reviews WHERE link_id = '".$link->link_id."' AND user_id = '".$my->id."'" );
		$user_rev = $database->loadObjectList();

		if ( count($user_rev) > 1 &&  $mt_user_review_once == '1') {
			# This user has already reviewed this listing
			$savant->assign('error_msg', $_MT_LANG->YOU_CAN_ONLY_REVIEW_ONCE);
			$savant->display( 'page_errorListing.tpl.php' );
		} elseif ( $mt_user_review == 1 && $my->id < 1 ) {
			# User is not logged in
			$savant->assign('error_msg', $_MT_LANG->PLEASE_LOGIN_BEFORE_REVIEW);
			$savant->display( 'page_errorListing.tpl.php' );
		} else {
			# OK. User is allowed to review
			$savant->display( 'page_writeReview.tpl.php' );

		}

	}
}

function addreview( $link_id, $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $my, $mosConfig_offset, $mt_user_review, $mt_needapproval_addreview, $mt_notifyadmin_newreview;

	# Get the review text
	$rev_text = trim( mosGetParam( $_POST, 'rev_text', '' ) );
	$rev_title = trim( strip_tags(mosGetParam( $_POST, 'rev_title', '' )) );
	$guest_name = trim( strip_tags(mosGetParam( $_POST, 'guest_name', '' )) );
	
	$link = loadLink( $link_id, $savantConf, $custom_fields, $params );

	if (empty($link)) {
		# Link does not exists, or is not published
		echo _NOT_EXIST;

	} elseif ( $mt_user_review == 1 && $my->id  < 1) {
		# User is not logged in
		echo _NOT_EXIST;
	
	} elseif ( $rev_text == '' ) {
		# Review text is empty
		echo "<script> alert('".$_MT_LANG->PLEASE_FILL_IN_REVIEW."'); window.history.go(-1); </script>\n";
		exit();
		
	} elseif ( $rev_title == '' ) {
		# Review title is empty
		echo "<script> alert('".$_MT_LANG->PLEASE_FILL_IN_TITLE."'); window.history.go(-1); </script>\n";
		exit();
		
	} else {
		# Everything is ok, add the review
		$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
		
		if ( $mt_needapproval_addreview == 1 ) {
			$rev_approved = 0;
		} else {
			$rev_approved = 1;
		}

		if ( $my->id > 0 ) {

			# User is logged on, store user ID
			$database->setQuery( "INSERT INTO #__mt_reviews "
				.	"( `link_id` , `user_id` , `rev_title` , `rev_text` , `rev_date` , `rev_approved` ) "
				.	"VALUES ($link_id, $my->id, '".$rev_title."', '".$rev_text."', '$now', '$rev_approved')");
			
		} else {

			# User is not logged on, store Guest name
			$database->setQuery( "INSERT INTO #__mt_reviews "
				.	"( `link_id` , `guest_name` , `rev_title` , `rev_text` , `rev_date` , `rev_approved` ) "
				.	"VALUES ($link_id, '".$guest_name."', '".$rev_title."', '".$rev_text."', '$now', '$rev_approved')");

		}

		if (!$database->query()) {
			echo "<script> alert('".$database->stderr()."');</script>\n";
			exit();
		}

		# Notify Admin
		if ( $mt_notifyadmin_newreview == 1 ) {
			
			$database->setQuery( "SELECT * FROM #__mt_links WHERE link_id = '".$link_id."' LIMIT 1" );
			$database->loadObject($link);
			
			if ( $my->id > 0 ) {
				$database->setQuery( "SELECT name, username, email FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
				$database->loadObject( $author );
				$author_name = $author->name;
				$author_username = $author->username;
				$author_email = $author->email;
			} else {
				$author_name = $guest_name;
				$author_username = $_MT_LANG->GUEST;
				$author_email = '';
			}

			if ( $rev_approved == 0 ) {
				$subject = sprintf($_MT_LANG->NEW_REVIEW_EMAIL_SUBJECT_WAITING_APPROVAL, $link->link_name);
				$msg = sprintf($_MT_LANG->ADMIN_NEW_REVIEW_MSG_WAITING_APPROVAL, $link->link_name, $rev_title, $author_name, $author_username, $author_email, stripslashes(html_entity_decode($rev_text)));
			} else {
				$subject = sprintf($_MT_LANG->NEW_REVIEW_EMAIL_SUBJECT_APPROVED, $link->link_name);
				$msg = sprintf($_MT_LANG->ADMIN_NEW_REVIEW_MSG_APPROVED, $link->link_name, sefRelToAbs($mosConfig_live_site."index.php?option=com_mtree&task=viewlink&link_id=$row->link_id&Itemid=$Itemid"), $rev_title, $author_name, $author_username, $author_email, stripslashes(html_entity_decode($rev_text)));
			}

			mosMailToAdmin( $subject, $msg );

		}


		if ( $mt_needapproval_addreview == 1 ) {
			mosRedirect( "index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid", $_MT_LANG->REVIEW_WILL_BE_REVIEWED );

		} else {
			mosRedirect( "index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid", $_MT_LANG->REVIEW_HAVE_BEEN_SUCCESSFULLY_ADDED );
		}

	}
}

/***
* Rating
*/
function rate( $link_id, $option ) {
	global $cache, $mainframe, $_MT_LANG, $savantConf;

	$link = loadLink( $link_id, $savantConf, $custom_fields, $params );
	$mainframe->setPageTitle( $_MT_LANG->RATE . $link->link_name );

	$cache->call( 'rate_cache', $link, $custom_fields, $params, $option );

}

function rate_cache( $link, $custom_fields, $params, $option ) {
	global $database, $_MAMBOTS, $_MT_LANG, $savantConf, $Itemid, $my, $mainframe, $mosConfig_offset;
	global $mt_user_rating, $mt_rate_once, $mt_show_email, $mosConfig_live_site, $mt_listing_image_dir, $mt_min_votes_to_show_rating;
	global $REMOTE_ADDR;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	# User IP Address
	$vote_ip = getenv( 'REMOTE_ADDR' );

	if (empty($link)) {
		echo _NOT_EXIST;
	} else {

		$page = 0;

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);

		$savant->_MT_LANG =& $_MT_LANG;
		$savant->assign('option', $option);
		$savant->assign('Itemid', $Itemid);
		$savant->assign('pathway', $pathWay);
		$savant->assign('link_id', $link->link_id);
		$savant->assign('mt_show_email', $mt_show_email);
		$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);
		$savant->assign('listing_image_dir', $mosConfig_live_site.$mt_listing_image_dir);
		$savant->assign('link', $link);
		$savant->assign('custom_fields', $custom_fields);

		// mambots results
		$savant->assign('mambotAfterDisplayTitle', $_MAMBOTS->trigger( 'onAfterDisplayTitle', array( &$link, &$params, $page ) ));
		$savant->assign('mambotBeforeDisplayContent', $_MAMBOTS->trigger( 'onBeforeDisplayContent', array( &$link, &$params, $page ) ));
		$savant->assign('mambotAfterDisplayContent', $_MAMBOTS->trigger( 'onAfterDisplayContent', array( &$link, &$params, $page ) ));

		# Check if this user has voted before
		if ( $my->id == 0 ) {
			$database->setQuery( "SELECT log_date FROM #__mt_log WHERE link_id ='".$link->link_id."' AND log_ip = '".$vote_ip."' AND log_type = 'vote'" );
		} else {
			$database->setQuery( "SELECT log_date FROM #__mt_log WHERE link_id ='".$link->link_id."' AND user_id = '".$my->id."' AND log_type = 'vote'" );
		}
		
		$voted = false;
		$voted = ($database->loadResult() <> '') ? true : false;

		if ( $mt_user_rating == '1' && $my->id < 1) {
			# Error. Please login before you can vote
			$savant->assign('error_msg', $_MT_LANG->PLEASE_LOGIN_BEFORE_RATE);
			$savant->display( 'page_errorListing.tpl.php' );

		}	elseif ( $voted && $mt_rate_once == '1') {
			# This user has already voted this listing
			$savant->assign('error_msg', $_MT_LANG->YOU_CAN_ONLY_RATE_ONCE);
			$savant->display( 'page_errorListing.tpl.php' );

		} else {
			# OK. User is logged in
			$savant->display( 'page_rating.tpl.php' );

		}

	}
}

function addrating( $link_id, $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $my;
	global $mt_user_rating, $mt_rate_once, $mosConfig_offset;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	# Get the rating
	$rating = intval( mosGetParam( $_POST, 'rating', 0 ) );

	$database->setQuery( "SELECT * FROM #__mt_links WHERE "
		.	"\n	link_published='1' AND link_approved > 0 AND link_id='".$link_id."'" 
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		);
	$database->loadObject( $link );

	# User IP Address
	$vote_ip = getenv( 'REMOTE_ADDR' );

	if (empty($link)) {
		# Link does not exists, or is not published
		echo _NOT_EXIST;

	} elseif ( $mt_user_rating == '1' && $my->id < 1) {
		# User is not logged in
		echo _NOT_EXIST;
	
	} elseif ( $rating == 0 || $rating > 5 ) {
		# Invalid rating. User did not fill in rating, or attempt misuse
		echo "<script> alert('".$_MT_LANG->PLEASE_SELECT_A_RATING."'); window.history.go(-1); </script>\n";
		exit();
		
	} else {

		# Everything is ok, add the rating
		$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

		if ( $my->id < 1 ) $my->id = 0;

		# Check if this user has voted before
		if ( $my->id == 0 ) {
			$database->setQuery( "SELECT log_date FROM #__mt_log WHERE link_id ='".$link_id."' AND log_ip = '".$vote_ip."' AND log_type = 'vote'" );
		} else {
			$database->setQuery( "SELECT log_date FROM #__mt_log WHERE link_id ='".$link_id."' AND user_id = '".$my->id."' AND log_type = 'vote'" );
		}
		
		$voted = false;
		$voted = ($database->loadResult() <> '') ? true : false;
		
		if ( !$voted || ($voted && !$mt_rate_once) ) {

			# Update #__mt_log table
			$database->setQuery( "INSERT INTO #__mt_log "
				.	"( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id` )"
				.	"VALUES ( '$vote_ip', 'vote', '$my->id', '$now', '$link_id')");
			if (!$database->query()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}

			$new_rating = ((($link->link_rating * $link->link_votes) + $rating) / ++$link->link_votes);

			# Update #__mt_links table
			$database->setQuery( "UPDATE #__mt_links "
				.	" SET link_rating = '$new_rating', link_votes = '$link->link_votes' "
				.	"WHERE link_id = '$link_id' ");
			if (!$database->query()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}

			mosRedirect( "index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid", $_MT_LANG->RATING_HAVE_BEEN_SUCCESSFULLY_ADDED );

		} else {
			mosRedirect ( "index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid", $_MT_LANG->YOU_CAN_ONLY_RATE_ONCE );
		}

	}

}

/***
* Recommend to Friend
*/

function recommend( $link_id, $option ) {
	global $cache, $_MT_LANG, $mainframe, $savantConf;

	$link = loadLink( $link_id, $savantConf, $custom_fields, $params );
	$mainframe->setPageTitle( $_MT_LANG->RECOMMEND ." ". $link->link_name );

	$cache->call( 'recommend_cache', $link, $custom_fields, $params, $option );

}

function recommend_cache( $link, $custom_fields, $params, $option ) {
	global $database, $_MAMBOTS, $_MT_LANG, $savantConf, $Itemid, $my, $mainframe, $mosConfig_offset;
	global $mt_user_recommend, $mt_show_email, $mt_listing_image_dir, $mosConfig_live_site, $mt_min_votes_to_show_rating;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	$page = 0;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('pathway', $pathWay);
	$savant->assign('link_id', $link->link_id);
	$savant->assign('listing_image_dir', $mosConfig_live_site.$mt_listing_image_dir);
	$savant->assign('mt_show_email', $mt_show_email);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->assign('link', $link);
	$savant->assign('custom_fields', $custom_fields);

	// mambots results
	$savant->assign('mambotAfterDisplayTitle', $_MAMBOTS->trigger( 'onAfterDisplayTitle', array( &$link, &$params, $page ) ));
	$savant->assign('mambotBeforeDisplayContent', $_MAMBOTS->trigger( 'onBeforeDisplayContent', array( &$link, &$params, $page ) ));
	$savant->assign('mambotAfterDisplayContent', $_MAMBOTS->trigger( 'onAfterDisplayContent', array( &$link, &$params, $page ) ));

	if (empty($link)) {
		echo _NOT_EXIST;

	} elseif ( $mt_user_recommend == '1' && $my->id < 1 ) {
		# Error. Please login before you can recommend
		$savant->assign('error_msg', $_MT_LANG->PLEASE_LOGIN_BEFORE_RECOMMEND);
		$savant->display( 'page_errorListing.tpl.php' );

	} else {
		$savant->display( 'page_recommend.tpl.php' );
	}

}

function send_recommend( $link_id, $option ) {
	global $_MT_LANG, $Itemid, $my, $mosConfig_live_site, $mosConfig_sitename, $mt_user_recommend;

	if ( $mt_user_recommend == '1' && $my->id < 1 ) {
		echo _NOT_EXIST;

	} else {

		$your_name = trim( mosGetParam( $_POST, 'your_name', '' ) );
		$your_email = trim( mosGetParam( $_POST, 'your_email', '' ) );
		$friend_name = trim( mosGetParam( $_POST, 'friend_name', '' ) );
		$friend_email = trim( mosGetParam( $_POST, 'friend_email', '' ) );

		if (!$your_email || !$friend_email || (is_email($your_email)==false) || (is_email($friend_email)==false) ){
			echo "<script>alert (\"".$_MT_LANG->YOU_MUST_ENTER_VALID_EMAIL."\"); window.history.go(-1);</script>";
			exit(0);
		}

		$msg = sprintf( $_MT_LANG->RECOMMEND_MSG,
			$mosConfig_sitename,
			$your_name,
			$your_email,
			sefRelToAbs($mosConfig_live_site.'/index.php?option=com_mtree&task=viewlink&link_id='.$link_id.'&Itemid='.$Itemid)
			);

		$subject = sprintf($_MT_LANG->RECOMMEND_SUBJECT, $your_name);

		mosMail( $your_email, $your_name, $friend_email, $subject, wordwrap($msg) );

		mosRedirect( "index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid", sprintf($_MT_LANG->RECOMMEND_EMAIL_HAVE_BEEN_SENT, $friend_name) );
	}

}

/***
* Contact Owner
*/

function contact( $link_id, $option ) {
	global $cache, $_MT_LANG, $mainframe, $savantConf;

	$link = loadLink( $link_id, $savantConf, $custom_fields, $params );
	$mainframe->setPageTitle( $_MT_LANG->CONTACT2 . $link->link_name );

	$cache->call( 'contact_cache', $link, $custom_fields, $params, $option );

}

function contact_cache( $link, $custom_fields, $params, $option ) {
	global $database, $_MAMBOTS, $_MT_LANG, $savantConf, $Itemid, $my, $mosConfig_offset;
	global $mt_min_votes_to_show_rating, $mt_show_email, $mosConfig_live_site, $mt_listing_image_dir;

	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
	$page = 0;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('pathway', $pathWay);
	$savant->assign('link_id', $link->link_id);
	$savant->assign('mt_show_email', $mt_show_email);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);
	$savant->assign('listing_image_dir', $mosConfig_live_site.$mt_listing_image_dir);
	$savant->assign('link', $link);
	$savant->assign('custom_fields', $custom_fields);

	// mambots results
	$savant->assign('mambotAfterDisplayTitle', $_MAMBOTS->trigger( 'onAfterDisplayTitle', array( &$link, &$params, $page ) ));
	$savant->assign('mambotBeforeDisplayContent', $_MAMBOTS->trigger( 'onBeforeDisplayContent', array( &$link, &$params, $page ) ));
	$savant->assign('mambotAfterDisplayContent', $_MAMBOTS->trigger( 'onAfterDisplayContent', array( &$link, &$params, $page ) ));

	if (empty($link)) {
		echo _NOT_EXIST;

	} else {
		$savant->display( 'page_contactOwner.tpl.php' );
	}

}

function send_contact( $link_id, $option ) {
	global $database, $_MT_LANG, $Itemid, $my;
	global $mosConfig_live_site, $mosConfig_sitename;
	global $mt_show_contact;

	if ( $mt_show_contact == 0 ) {
		
		echo _NOT_EXIST;

	} else {

		$link = new mtLinks( $database );
		$link->load( $link_id );

		$your_name = trim( mosGetParam( $_POST, 'your_name', '' ) );
		$your_email = trim( mosGetParam( $_POST, 'your_email', '' ) );
		$message = trim( mosGetParam( $_POST, 'message', '' ) );

		if (!$your_email || (is_email($your_email)==false) ){
			echo "<script>alert (\"".$_MT_LANG->YOU_MUST_ENTER_VALID_EMAIL."\"); window.history.go(-1);</script>";
			exit(0);
		}

		$subject = sprintf($_MT_LANG->CONTACT_SUBJECT, $mosConfig_sitename);
		
		if( empty($link->email) ) {
			$database->setQuery( 'SELECT email FROM #__users WHERE id = '.$link->user_id.' LIMIT 1' );
			$email = $database->loadResult();
		} else {
			$email = $link->email;
		}

		mosMail( $your_email, $your_name, $email, $subject, wordwrap($message) );

		mosRedirect( "index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid", $_MT_LANG->CONTACT_EMAIL_HAVE_BEEN_SENT);
	}

}

function is_email($email){
	$rBool=false;

	if(preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $email)){
		$rBool=true;
	}
	return $rBool;
}

/***
* Edit Listing
*/
function editlisting( $link_id, $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $my, $mainframe;
	global $mt_user_addlisting, $mt_user_allowmodify, $mt_allow_imgupload, $mosConfig_live_site, $mt_listing_image_dir, $mt_user_addlisting, $mt_allow_listings_submission_in_root;

	$cms    =& cmsInstance('CMSCore');
	# Get cat_id if user is adding new listing. 
	$cat_id = intval( mosGetParam( $_REQUEST, 'cat_id', 0 ) );

	// This var retrieve the link_id for adding listing
	$link_id_passfromurl = intval( mosGetParam( $_REQUEST, 'link_id', 0 ) );

	if ( $link_id_passfromurl > 0 && $cat_id == 0 ) {
			$database->setQuery( "SELECT cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE l.link_id ='".$link_id_passfromurl."' AND cl.link_id = l.link_id" );
			$cat_id = $database->loadResult();
	}

	$link = new mtLinks( $database );

	# Do not allow Guest to edit listing
	if ( $link_id > 0 && $my->id <= 0 ) {
		$link->load( 0 );
	} else {
		$link->load( $link_id );
		if ( $link_id == 0 ) $link->website = "http://";
		$link->link_name = htmlspecialchars($link->link_name);
	}

	# Get current category's template
	$database->setQuery( "SELECT cat_name, cat_template, cat_image, metakey, metadesc FROM #__mt_cats WHERE cat_id='".$cat_id."' AND cat_published='1' LIMIT 1" );
	$database->loadObject( $cat );

	$mainframe->setPageTitle( sprintf($_MT_LANG->ADD_LISTING2, $cat->cat_name) );

	if ( isset($cat->cat_template) && $cat->cat_template <> '' ) {

		// Make sure the directory exists, otherwise fallback to default template
		$templateDir = $cms->get_path('root') . '/components/com_mtree/templates/' . $cat->cat_template;
		if ( is_dir( $templateDir ) ) {
			$savantConf["template_path"] = $templateDir;
		}

	}

	# Pathway
	$pathWay = new mtPathWay( $cat_id );
	$pw_cats = $pathWay->getPathWayWithCurrentCat( $cat_id );
	$pathWayToCurrentCat = '';
	$mtCats = new mtCats($database);
	$pathWayToCurrentCat = ' <a href="'.sefRelToAbs("index.php?option=com_mtree&task=listcats&Itemid=".$Itemid).'">'.$_MT_LANG->ROOT."</a>";
	foreach( $pw_cats AS $pw_cat ) {
		$pathWayToCurrentCat .= $_MT_LANG->ARROW .' <a href="'.sefRelToAbs("index.php?option=com_mtree&task=listcats&cat_id=".$pw_cat."&Itemid=".$Itemid).'">'.$mtCats->getName($pw_cat)."</a>";
	}

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('pathway', $pathWay);
	$savant->assign('pathWayToCurrentCat',$pathWayToCurrentCat);
	$savant->assign('cat_id', (($link_id == 0) ? $cat_id : $link->cat_id ) );
	$savant->assign('allow_upload', $mt_allow_imgupload);
	$savant->assign('mosConfig_live_site', $mosConfig_live_site);
	$savant->assign('mt_listing_image_dir', $mt_listing_image_dir);
	$savant->assignRef('link', $link);

	# Check permission
	if ( ($mt_user_addlisting == 1 && $my->id < 1) || ($link_id > 0 && $my->id == 0) ) {

		$savant->assign('error_msg', $_MT_LANG->PLEASE_LOGIN_BEFORE_ADDLISTING);
		$savant->display( 'page_error.tpl.php' );
	
	} elseif( ($link_id > 0 && $my->id <> $link->user_id) || $mt_user_addlisting == -1 ) {
		
		echo _NOT_EXIST;

	} else {
	# OK, you can edit

		// Get custom field's caption
		$database->setQuery( "SELECT name, value FROM #__mt_config WHERE name LIKE 'cust_%'" );
		$savant->assign('custom_fields', $database->loadObjectList('name'));

		// Get custom field's data
		foreach( $savant->custom_fields AS $cf ) {
			$database->setQuery( "SELECT $cf->name FROM #__mt_links WHERE link_id = '$link_id' LIMIT 1" );
			$val = $database->loadResult();
			if (isset($val)) {
				$savant->custom_data[$cf->name] = $val;
			} else {
				$savant->custom_data[$cf->name] = '';
			}
		}

		// Get category's tree
		getCatsSelectlist( 0, $cat_tree );
		$cat_options[] = mosHTML::makeOption( "default", "&nbsp" );
		if( $mt_allow_listings_submission_in_root ) {
			$cat_options[] = mosHTML::makeOption( "0", $_MT_LANG->ROOT );
		}
		foreach( $cat_tree AS $ct ) {
			if( $ct["cat_allow_submission"] == 1 ) {
				$cat_options[] = mosHTML::makeOption( $ct["cat_id"], str_repeat("&nbsp;",($ct["level"]*3)) .(($ct["level"]>0) ? " -":''). $ct["cat_name"] );
			} else {
				$cat_options[] = mosHTML::makeOption( ($ct["cat_id"]*-1), str_repeat("&nbsp;",($ct["level"]*3)) .(($ct["level"]>0) ? " -":''). "(".$ct["cat_name"].")" );
			}
		}
		$catlist = mosHTML::selectList( $cat_options, "new_cat_id", '', 'value', 'text', "default" );
		$savant->assignRef('catlist', $catlist );


		// Give warning is there is already a pending approval for modification.
		if ( $link_id > 0 ) {
			$database->setQuery( "SELECT link_id FROM #__mt_links WHERE link_approved = '".(-1*$link_id)."'" );
			if ( $database->loadResult() > 0 ) {
				$savant->assign('warn_duplicate', 1);
			} else {
				$savant->assign('warn_duplicate', 0);
			}
		}

		$savant->display( 'page_addListing.tpl.php' );
	}

}

function savelisting( $option ) {
	global $mt_user_addlisting, $mt_needapproval_addlisting, $mt_needapproval_modifylisting, $database, $_MT_LANG, $Itemid, $my;
	global $mt_notifyuser_newlisting, $mt_notifyadmin_newlisting, $mt_notifyuser_modifylisting, $mt_notifyadmin_modifylisting, $mt_allow_html, $mt_user_addlisting;

	global $mosConfig_mailfrom, $mosConfig_fromname, $mt_admin_email, $mosConfig_live_site, $mt_listing_image_dir, $mt_resize_method, $mt_resize_quality, $mt_resize_listing_size;

	$cms    =& cmsInstance('CMSCore');
	# Get cat_id / remove_image / link_image
	$cat_id = intval( mosGetParam( $_REQUEST, 'cat_id', 0 ) );
	$remove_image = mosGetParam( $_REQUEST, 'remove_image', 0 );
	$link_image = mosGetParam( $_FILES, 'link_image', null );
	$new_cat_id = intval( mosGetParam( $_POST, 'new_cat_id', 0 ) );

	# Check if any malicious user is trying to submit link
	if ( ($mt_user_addlisting == 1 && $my->id < 1) || $mt_user_addlisting == -1 ) {
		
		echo _NOT_EXIST;

	} else {
	# Allowed

		// Convert all checkbox value to comma delimited
		for($i=1; $i<=30; $i++) {
			if ( @is_array($_POST["cust_".$i]) ) {
				$_POST["cust_".$i] = implode(",",$_POST["cust_".$i]);
			}
		}

		$row = new mtLinks( $database );
		if (!@$row->bind( $_POST )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if ( $new_cat_id <> $row->cat_id AND $new_cat_id > 0 ) {
			$row->cat_id = $new_cat_id;
		}

		$isNew = ($row->link_id < 1) ? 1 : 0;

		# website's validation
		if ( $row->website == "http://" ) {
			$row->website = '';
		} elseif( !empty($row->website) && substr($row->website,0,7) != 'http://' ) {
			$row->website = 'http://'.$row->website;
		}

		# Assignment for new record
		if ($isNew) {

			$row->link_created = date( "Y-m-d H:i:s" );	
			$row->ordering = 999;

			if ( $my->id > 0) {
				$row->user_id = $my->id;
			} else {
				$database->setQuery( "SELECT id FROM #__users WHERE usertype = 'Super Administrator' LIMIT 1" );
				$row->user_id = $database->loadResult();
			}

			if (!$mt_allow_html) {
				$row->link_desc = strip_tags($row->link_desc);
			}

			// Approval for adding listing
			if ( $mt_needapproval_addlisting ) {
				$row->link_approved = '0';
			} else {
				$row->link_approved = 1;
				$row->link_published = 1;
				mosCache::cleanCache( 'com_mtree' );
			}

		# Modification to existing record
		} else {
			
			//print_r( $_POST );

			# Validate that this user is the rightful owner
			$database->setQuery( "SELECT user_id FROM #__mt_links WHERE link_id = '".$row->link_id."'" );
			$user_id = $database->loadResult();

			if (  $user_id <> $my->id ) {
				echo _NOT_EXIST;;
			} else {

				# Get the name of the old photo and last modified date
				$sql="SELECT link_id, link_image, link_modified, link_created FROM #__mt_links WHERE link_id='".$row->link_id."'";
				$database->setQuery($sql);
				$database->loadObject($old);

				$old_image = $old->link_image;

				# Remove previous old image
				/*
				if ( $remove_image || ($old_image <> '' && $link_image['tmp_name'] <> '') ) {
					$row->link_image = '-1';

					if(!unlink($cms->get_path('root').$mt_listing_image_dir.$old_image)) {
						echo "<script> alert('".$_MT_LANG->ERROR_DELETING_OLD_IMAGE."'); </script>\n";
					}
				}
				*/
				# Retrive last modified date
				$old_modified = $old->link_modified;
				$link_created = $old->link_created;

				$row->link_modified = date( "Y-m-d H:i:s" );	
				$row->link_published = 1;
				$row->user_id = $my->id;
				
				// Get other info from original listing
				$database->setQuery( "SELECT link_hits, link_votes, link_rating, link_featured, link_created, link_visited, link_image, ordering, publish_down, publish_up FROM #__mt_links WHERE link_id = '$row->link_id'" );
				$database->loadObject( $original );

				foreach( $original AS $k => $v ) {
					
					// Set link_image to "-1" to indicate image removal
					if ( $k == "link_image" && ( $remove_image || ($old_image <> '' && $link_image['tmp_name'] <> '') ) ) {
						if ( $mt_needapproval_modifylisting ) {
							$row->link_image = '-1';
						} else {
							if(!unlink($cms->get_path('root').$mt_listing_image_dir.$old_image)) {
								echo "<script> alert('".$_MT_LANG->ERROR_DELETING_OLD_IMAGE."'); </script>\n";
							} else {
								$row->link_image = '';
							}
						}
					} else {
						$row->$k = $v;
					}
				}

				// Remove any listing that is waiting for approval for this listing
				$database->setQuery( "DELETE FROM #__mt_links WHERE link_approved = '".(-1*$row->link_id)."'" );
				$database->query();
				
				// Approval for modify listing
				if ( $mt_needapproval_modifylisting ) {
					$row->link_approved = (-1 * $row->link_id);
					$row->link_id = null;
				} else {
					$row->link_approved = 1;
					mosCache::cleanCache( 'com_mtree' );
				}

			}

		} // End of $isNew

		# Create Thumbnail
		if ( $link_image['name'] <> '' ) {

			$mtImage = new mtImage( $link_image, $cms->get_path('root').$mt_listing_image_dir );
			$mtImage->setMethod( $mt_resize_method );
			$mtImage->setQuality( $mt_resize_quality );
			$mtImage->setSize( $mt_resize_listing_size );

			if ( $row->link_id > 0 ) {
				$mtImage->setName( $row->link_id."_".$link_image['name'] );
			}

			if ( $mtImage->check() ) {
				if ( $mtImage->resize() ) {
					if ( $row->link_id > 0 ) {
						$row->link_image = $row->link_id."_".$link_image['name'];
					} elseif( $isNew ) {
						// Do nothing yet. The record is not saved, therefore no link_id available.
					} else {
						$row->link_image = $old->link_id."_".$link_image['name'];
					}
				}
			} else {
				echo "<script> alert('".$mtImage->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}

		}

		# Strip HTML tags if admin does not allow it
		if (!$mt_allow_html) {
			$row->link_desc = strip_tags($row->link_desc);
		}
		

		# OK. Store new listing into database
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		} else {
			
			# If this is a newly submitted link and has image, rename the photo to listingID_photoName.jpg
			if ( $isNew && $link_image['error'] == 0 ) {

				// Get last inserted listing ID
				$mysql_last_insert_cl_id = $database->insertid();

				$database->setQuery( "SELECT link_id FROM #__mt_cl WHERE cl_id = ".$mysql_last_insert_cl_id );
				$mysql_last_insert_id = $database->loadResult();

				if ( $mysql_last_insert_id > 0 ) {

					if ( rename( $cms->get_path('root').$mt_listing_image_dir.$link_image['name'], $cms->get_path('root').$mt_listing_image_dir.$mysql_last_insert_id."_".$link_image['name'] ) ) {

						$database->setQuery( "UPDATE #__mt_links SET link_image = '".$mysql_last_insert_id."_".$link_image['name']."' WHERE link_id = '".$mysql_last_insert_id."' LIMIT 1" );
						$database->query();

					}
				}

			# This is modification to an existing listing
			} elseif( !$isNew && $link_image['error'] == 0 && $row->link_id > 0 ) {

				$database->setQuery( "SELECT LAST_INSERT_ID()" );
				$mysql_last_insert_id = $database->loadResult();

				if ( $mysql_last_insert_id > 0 ) {

					if ( rename( $cms->get_path('root').$mt_listing_image_dir.$link_image['name'], $cms->get_path('root').$mt_listing_image_dir.($row->link_approved * -1)."_".$link_image['name'] ) ) {
						
						$database->setQuery( "UPDATE #__mt_links SET link_image = '".($row->link_approved * -1)."_".$link_image['name']."' WHERE link_id = '".$mysql_last_insert_id."' LIMIT 1" );
						$database->query();

					}
				}

			}
		}

		# Send e-mail notification to user/admin upon adding a new listing

		// Get owner's email
		$database->setQuery( "SELECT email, name FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
		$database->loadObject( $author );

		if ( $isNew ) {

			# To User
			if ( $mt_notifyuser_newlisting == 1 && $my->id > 0 ) {
				
				if ( $row->link_approved == 0 ) {
					$subject = sprintf($_MT_LANG->NEW_LISTING_EMAIL_SUBJECT_WAITING_APPROVAL, $row->link_name);
					$msg = $_MT_LANG->NEW_LISTING_EMAIL_MSG_WAITING_APPROVAL;
				} else {
					$subject = sprintf($_MT_LANG->NEW_LISTING_EMAIL_SUBJECT_APPROVED, $row->link_name);
					$msg = sprintf($_MT_LANG->NEW_LISTING_EMAIL_MSG_APPROVED, $row->link_name, sefRelToAbs($mosConfig_live_site."/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id&Itemid=$Itemid"),$mosConfig_fromname);
				}

				mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $author->email, $subject, wordwrap($msg) );
			}

			# To Admin
			if ( $mt_notifyadmin_newlisting == 1 ) {
				
				if ( $row->link_approved == 0 ) {
					$subject = sprintf($_MT_LANG->NEW_LISTING_EMAIL_SUBJECT_WAITING_APPROVAL, $row->link_name);
					$msg = sprintf($_MT_LANG->ADMIN_NEW_LISTING_MSG_WAITING_APPROVAL, $row->link_name, $row->link_name, $row->link_id, $author->name, $my->username, $author->email);
				} else {
					$subject = sprintf($_MT_LANG->NEW_LISTING_EMAIL_SUBJECT_APPROVED, $row->link_name);
					$msg = sprintf($_MT_LANG->ADMIN_NEW_LISTING_MSG_APPROVED, $row->link_name, sefRelToAbs($mosConfig_live_site."/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id&Itemid=$Itemid"), $row->link_name, $row->link_id, $author->name, $my->username, $author->email);
				}

				//mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $mt_admin_email, $subject, wordwrap($msg) );
				mosMailToAdmin( $subject, $msg );

			}

		}

		# Send e-mail notification to user/admin upon modifying an existing listing
		else {

			# To User
			if ( $mt_notifyuser_modifylisting == 1 && $my->id > 0 ) {
				
				if ( $row->link_approved < 0 ) {
					$subject = sprintf($_MT_LANG->MODIFY_LISTING_EMAIL_SUBJECT_WAITING_APPROVAL, $row->link_name);
					$msg = sprintf($_MT_LANG->MODIFY_LISTING_EMAIL_MSG_WAITING_APPROVAL, $row->link_name, sefRelToAbs($mosConfig_live_site."/index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid") );
				} else {
					$subject = sprintf($_MT_LANG->MODIFY_LISTING_EMAIL_SUBJECT_APPROVED, $row->link_name);
					$msg = sprintf($_MT_LANG->MODIFY_LISTING_EMAIL_MSG_APPROVED, $row->link_name, sefRelToAbs($mosConfig_live_site."/index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid"),$mosConfig_fromname);
				}

				mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $author->email, $subject, wordwrap($msg) );
			}

			# To Admin
			if ( $mt_notifyadmin_modifylisting == 1 ) {
				
				if ( $row->link_approved < 0 ) {
					$subject = sprintf($_MT_LANG->MODIFY_LISTING_EMAIL_SUBJECT_WAITING_APPROVAL, $row->link_name);
					$msg = sprintf($_MT_LANG->ADMIN_MODIFY_LISTING_MSG_WAITING_APPROVAL, $row->link_name, sefRelToAbs($mosConfig_live_site."/index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid"), $row->link_name, $row->link_id, $author->name, $my->username, $author->email);
				} else {
					$subject = sprintf($_MT_LANG->MODIFY_LISTING_EMAIL_SUBJECT_APPROVED, $row->link_name);
					$msg = sprintf($_MT_LANG->ADMIN_MODIFY_LISTING_MSG_APPROVED, $row->link_name, sefRelToAbs($mosConfig_live_site."/index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid"), $row->link_name, $row->link_id, $author->name, $my->username, $author->email);
				}

				mosMailToAdmin( $subject, $msg );
			}

		}

		mosRedirect( "index.php?option=$option&task=listcats&cat_id=$cat_id&Itemid=$Itemid", ( ($isNew) ? ( ($mt_needapproval_addlisting) ? $_MT_LANG->LISTING_WILL_BE_REVIEWED : $_MT_LANG->LISTING_HAVE_BEEN_ADDED) : ( ($mt_needapproval_modifylisting) ? $_MT_LANG->LISTING_MODIFICATION_WILL_BE_REVIEWED : $_MT_LANG->LISTING_HAVE_BEEN_UPDATED ) ) );

	}
}

/***
* Add Category
*/
function addcategory( $option ) {
	global $database, $_MT_LANG, $savantConf, $Itemid, $my, $mainframe;
	global $mt_user_addcategory;
	
	# Get cat_id / link_id
	$cat_id = intval( mosGetParam( $_REQUEST, 'cat_id', 0 ) );
	$link_id = intval( mosGetParam( $_REQUEST, 'link_id', 0 ) );

	if ( $cat_id == 0 && $link_id > 0 ) {
		$database->setQuery( "SELECT cl.cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE l.link_id = cl.link_id AND cl.main = '1' AND link_id ='".$link_id."'" );
		$cat_parent = $database->loadResult();
	} else {
		$cat_parent = $cat_id;
	}

	$database->setQuery( "SELECT cat_name FROM #__mt_cats WHERE cat_id = '".$cat_parent."'" );
	$cat_name = $database->loadResult();

	$mainframe->setPageTitle( sprintf($_MT_LANG->ADD_CAT2, $cat_name) );

	# Pathway
	$pathWay = new mtPathWay( $cat_parent );

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('pathway', $pathWay);
	$savant->assign('cat_parent', $cat_parent);

	if ( $mt_user_addcategory == '1' && $my->id < 1 ) {
		# Error. Please login before you can recommend
		$savant->assign('error_msg', $_MT_LANG->PLEASE_LOGIN_BEFORE_ADDCATEGORY);
		$savant->display( 'page_error.tpl.php' );
	} else {
		# OK. User is allowed to add category
		$savant->display( 'page_addCategory.tpl.php' );
	}

}

function addcategory2( $option ) {
	global $mt_user_addcategory, $mt_needapproval_addcategory, $database, $_MT_LANG, $Itemid, $my;

	# Get cat_parent
	$cat_parent = intval( mosGetParam( $_REQUEST, 'cat_parent', 0 ) );

	# Check if any malicious user is trying to submit link
	if ( $mt_user_addcategory == 1 && $my->id <= 0 ) {
		echo _NOT_EXIST;

	} else {
	# Allowed

		$row = new mtCats( $database );
		if (!$row->bind( $_POST )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$isNew = $row->cat_id < 1;

		# Assignment for new record
		if ($isNew) {
			$row->cat_created = date( "Y-m-d H:i:s" );	

			// Required approval
			if ( $mt_needapproval_addcategory ) {
				$row->cat_approved = '0';
			} else {
				$row->cat_approved = 1;
				$row->cat_published = 1;
				mosCache::cleanCache( 'com_mtree' );
			}

		} else {
		# Assignment for exsiting record
			$row->cat_modified = date( "Y-m-d H:i:s" );	
		}

		# OK. Store new category into database
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $isNew && !$mt_needapproval_addcategory) {
			$row->updateLftRgt();
			$row->updateCatCount( 1 );
		}

		mosRedirect( "index.php?option=$option&task=listcats&cat_id=$cat_parent&Itemid=$Itemid", ( ($mt_needapproval_addcategory) ?  $_MT_LANG->CATEGORY_WILL_BE_REVIEWED : $_MT_LANG->CATEGORY_HAVE_BEEN_ADDED) );

	}
}

function mylisting( $limitstart, $option ) {
	global $database, $my, $_MT_LANG, $Itemid, $mosConfig_offset;
	global $mt_fe_num_of_links, $mt_show_email, $savantConf, $mt_fe_num_of_chars, $mt_min_votes_to_show_rating;

	$cms    =& cmsInstance('CMSCore');
	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );

	# Page Navigation
	$database->setQuery("SELECT COUNT(*) FROM #__mt_links WHERE "
		.	"\n	link_published='1' AND link_approved='1' AND user_id ='".$my->id."'"
		. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
		);
	$total_links = $database->loadResult();

	require_once($cms->get_path('root')."/includes/pageNavigation.php");
	$pageNav = new mosPageNav( $total_links, $limitstart, $mt_fe_num_of_links );

	if ( $my->id > 0 ) {
		# Retrieve Links
		$database->setQuery( "SELECT l.*, cl.cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) "
			.	"WHERE link_published='1' AND link_approved='1' AND user_id='".$my->id."' "
			. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
			. "\n AND l.link_id = cl.link_id AND cl.main = 1 "
			.	"ORDER BY link_featured DESC, ordering ASC "
			.	"LIMIT $limitstart, $mt_fe_num_of_links" );
		$links = $database->loadObjectList();
	} else {
		$links = array();
	};

	# Mambots
	applyMambots( $links );
	
	# Strip Tags
	stripTags( $links );

	# Pathway
	$pathWay = new mtPathWay();

	# Savant Template
	$savant = new Savant2($savantConf);

	$savant->_MT_LANG =& $_MT_LANG;
	$savant->assignRef('pageNav', $pageNav);
	$savant->assign('option', $option);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('mt_show_email', $mt_show_email);
	$savant->assignRef('pathway', $pathWay);
	$savant->assignRef('links', $links);
	$savant->assign('reviews', getReviews($links));

	$savant->assign('max_chars', $mt_fe_num_of_chars);
	$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

	$savant->display( 'page_myListing.tpl.php' );

}

function viewowner( $user_id, $limitstart, $option ) {
	global $cache, $database, $_MT_LANG, $mainframe;

	# Get owner's info
	$database->setQuery( "SELECT id, name, username, email FROM #__users WHERE id = '".$user_id."'" );
	$database->loadObject( $owner );
	
	if( count($owner) == 1 ) {
		$mainframe->setPageTitle( sprintf($_MT_LANG->LISTING_BY, $owner->name) );
		$cache->call( 'viewowner_cache', $owner, $limitstart, $option );
	} else {
		echo _NOT_EXIST;
	}

}

function viewowner_cache( $owner, $limitstart, $option ) {
	global $database, $my, $_MT_LANG, $Itemid, $mosConfig_offset;
	global $mt_show_email, $mt_fe_num_of_chars, $mt_fe_num_of_links, $savantConf, $mt_min_votes_to_show_rating;

	$cms    =& cmsInstance('CMSCore');
	$now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
	$user_id = $owner->id;

	if ( $owner ) {

		# Page Navigation
		$database->setQuery("SELECT COUNT(*) FROM #__mt_links WHERE "
			. "\n	link_published='1' AND link_approved='1' AND user_id ='".$user_id."'"
			. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
			);
		$total_links = $database->loadResult();

		require_once($cms->get_path('root')."/includes/pageNavigation.php");
		$pageNav = new mosPageNav( $total_links, $limitstart, $mt_fe_num_of_links );

		# Retrieve Links
		$database->setQuery( "SELECT l.*, cl.cat_id AS cat_id FROM (#__mt_links AS l, #__mt_cl AS cl)"
			.	"\n WHERE link_published='1' AND link_approved='1' AND user_id='".$user_id."' "
			. "\n AND l.link_id = cl.link_id AND cl.main = '1'"
			. "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= '$now' ) "
			.	"\n ORDER BY link_featured DESC, ordering ASC "
			.	"\n LIMIT $limitstart, $mt_fe_num_of_links" );
		$links = $database->loadObjectList();

		# Mambots
		applyMambots( $links );

		# Strip Tags
		stripTags( $links );

		# Pathway
		$pathWay = new mtPathWay();

		# Savant Template
		$savant = new Savant2($savantConf);

		$savant->_MT_LANG =& $_MT_LANG;
		$savant->assignRef('pageNav', $pageNav);
		$savant->assign('option', $option);
		$savant->assign('Itemid', $Itemid);
		$savant->assign('owner', $owner);
		$savant->assign('mt_show_email', $mt_show_email);
		$savant->assignRef('pathway', $pathWay);
		$savant->assignRef('links', $links);
		$savant->assign('reviews', getReviews($links));

		$savant->assign('max_chars', $mt_fe_num_of_chars);
		$savant->assign('min_votes_to_show_rating', $mt_min_votes_to_show_rating);

		$savant->display( 'page_ownerListing.tpl.php' );

	} else {
		echo _NOT_EXIST;
	}
}

function mosMailToAdmin( $subject, $body) {
	global $mt_admin_email, $mosConfig_fromname, $mosConfig_mailfrom;
	
	if ( strpos($mt_admin_email,',') === false ) {
		$recipient_emails = array($mt_admin_email);
	} else {
		$recipient_emails = explode(',', $mt_admin_email);
	}

	mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $recipient_emails, $subject, wordwrap($body) );

}

?>
