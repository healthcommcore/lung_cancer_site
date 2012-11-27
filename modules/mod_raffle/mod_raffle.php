<?php
/**
* @version 1.0 $
* @package Raffle
* @copyright (C) 2007 HCC
*/
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted Access.' );

require_once( JPATH_SITE .'/includes/hd2/siteFunctions.php' );
require_once( JPATH_SITE .'/includes/hd2/constants.php' );
require_once( JPATH_SITE .'/includes/hd2/user.php' );

global $userDB;
global $userMsqlDB;
global $user;

// echo $params->get( 'moduleclass_sfx' );

if (($user = initData()) == NULL) return;
mysql_close( $userMsqlDB);
$userMsqlDB = null;

$raffleDate =  $params->get( 'raffle_date' );

?>
<h3>Your Raffle Points</h3>

<p>You have <span class="highlight-bold"><?php echo number_format($user->points); ?></span> points!</p>

<p>Earn raffle points every time you track! <?php if ( ($raffleDate) && (strtotime($raffleDate) > time() )) { ?>
The next drawing will be on <?php echo date("F j, Y", strtotime($raffleDate)); ?>.
<?php } ?>
</p>
<p align="right" ><a href="index.php?option=com_content&amp;view=article&amp;id=21" title=""  class="helplink">Raffle Rules</a></p>
