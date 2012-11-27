<?php
(defined( '_VALID_MOS' ) or defined('_JEXEC')) or die( 'Restricted access' );

class JCMailQueue 
{
	var $_tablename;	# table name of the mail Q
	var $_maxburst;		# maximum email to send per session
	var $cms = null;
	
	function JCMailQueue(){
		$this->_tablename 	= "#__jomcomment_mailq";
		$this->_maxburst	= 10;
		
		$this->cms = & cmsInstance('CMSCore');
	}
	
	# Add the given email to mailQ. Mail queue will simply add it to its q list
	# table and make NO attempt to deliver it
	# @todo: validate the email		
	function mail($email, $subject, $body, $mode=0){
	    $data = array(
	    	'content' =>$body, 
			'email' => $email, 
			'subject' => $subject
			);
	    $this->cms->db->insert($this->_tablename, $strSQL);
	}
	
	# Read from the queue, and send out the email, '$_maxburst' number
	# of email at most
	function send(){
		global $mainframe;

		// Check if comment is published before sending
		$sql	= "SELECT a.id, a.title, a.email, a.content "
				. "FROM `#__jomcomment_mailq` AS a "
				. "INNER JOIN `#__jomcomment` AS b "
				. "ON a.commentid = b.id "
				. "AND a.status = '0' "
				. "AND b.published = '1' "
				. "LIMIT 0, " . $this->_maxburst;

		$this->cms->db->query($sql);
		$rows   = $this->cms->db->get_object_list();

		if($rows){
		    foreach($rows as $row){
		        $this->_mark_as_read($row->id);
		        jomMail($mainframe->getCfg('mailfrom'), $mainframe->getCfg('fromname'), $row->email, $row->title, $row->content);
			}
		}
		# Purge data older than 7-days
	}
	
	function _mark_as_read($id){
		$strSQL = "UPDATE $this->_tablename SET `status`='1' WHERE `id`='{$id}'";
		$this->cms->db->query($strSQL);
	}
}

?>
