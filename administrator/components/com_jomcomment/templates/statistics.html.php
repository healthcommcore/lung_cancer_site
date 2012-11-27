<table width="100%" border="0" cellspacing="2" cellpadding="2" class="mytable">
	<tr>
		<td width="50%">
			<table width="100%" border="0" cellspacing="2" cellpadding="2" class="adminform">
				<th colspan="2">Basic stats</th>
				<tr>
					<td width="35%"><strong>Total Comments:</strong></td>
					<td width="65%">{totalComments} comments </td>
				</tr>
				<tr>
					<td width="35%"><strong>Total comments last 30 days. </strong></td>
					<td width="65%">{commentThisMonth} comments </td>
				</tr>
				<tr>
					<td width="35%" valign="top">
						<p><strong>Top 20 Commented content:</strong></p>
						<p>{topContent}</p>
					</td>
					<td width="65%" valign="top">
						<p><strong>Top 20 member:</strong></p>
						<p>{topMember}</p>
					</td>
				</tr>
			</table>
			<p>
			<input type="button" class="CommonTextButtonSmall" value="Export all emails to csv file" onclick="document.location='index2.php?option=com_jomcomment&task=exportEmail'"/>
			</p>
		</td>
	</tr>
</table>
