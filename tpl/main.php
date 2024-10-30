<?php
/**
 * Copyright 2012  content.de AG  (email: info[YEAR]@content.de (eg: info2012@content.de))
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

function contentdeTemplateMain($aParams) { ?>

<div class="wrap">
	<h2>content.de &Uuml;bersicht</h2>

	<?php contentdeLogic::showErrors(); ?>
	<?php contentdeLogic::showSuccesses(); ?>

	<?php if(CONTENTDE_HAS_LOGIN_DATA): ?>

	<?php

	$aProjects = array('ALL' => 'alle');

	foreach($aParams['projects'] as $sId => $sName)
	{
		$aProjects[$sId] = $sName;
	}

	$aStates = array(
		'all' => 'alle',
		'new' => 'neu',
		'processing' => 'in Bearbeitung',
		'waiting' => ' fertig zur Abnahme',
		'accepted' => 'akzeptiert',
		'post_processing' => 'in Nachbearbeitung',
		'canceled' => 'annulliert'
	);

	$aArchive = array(
		'ALL' => 'alle',
		'0' => 'nur nicht archivierte',
		'1' => 'nur archivierte'
	);
	
	$aPostTypes = contentdeHelper::getPostTypes();

	?>

	<form action="<?php echo contentdeHelper::getPageUrl(); ?>" method="get">

		<input type="hidden" name="page" value="contentde-main">

		<div class="tablenav top">
			Projekt:
			<?php echo contentdeHelper::buildSelect($aProjects, $aParams['selectedProject'], array('name' => 'project')); ?>

			Status:
			<?php echo contentdeHelper::buildSelect($aStates, $aParams['selectedStatus'], array('name' => 'status')); ?>

			Archiv:
			<?php echo contentdeHelper::buildSelect($aArchive, $aParams['selectedArchive'], array('name' => 'archive')); ?>

			<input type="submit" name="filter_orders" value="Auswahl einschr&auml;nken" class="button-secondary" />
		</div>
	</form>

	<table class="wp-list-table widefat fixed">
		<thead>
			<tr>
				<th>Id</th>
				<th>Titel</th>
				<th>Status</th>
				<th>bearbeiten</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($aParams['orderList']) > 0):  ?>
			<?php foreach($aParams['orderList'] as $aOrder): ?>
			<tr>
				<td><?php echo $aOrder['order_id']; ?></td>
				<th><?php echo $aOrder['order_title']; ?></th>
				<td><?php echo $aStates[$aOrder['order_status']]; ?></td>
				<td>
					<?php if($aOrder['order_status'] == 'accepted'): ?>

					<?php
					if(count($aPostTypes) > 2)
					{
						echo contentdeHelper::buildSelect(array('none' => 'Neuen ... erstellen') + $aPostTypes, 'none', array('id' => 'create_' . $aOrder['order_id']));
						?>
						<script type="text/javascript">
						jQuery(function () {
							jQuery('#create_<?php echo $aOrder['order_id']; ?>').change(function () {
								var type = jQuery(this).val();
								jQuery(this).val('none');
								if(type != 'none')
								{
									window.location.href = "<?php echo admin_url(sprintf('post-new.php?contentdeOrder=%s&post_type=', $aOrder['order_id'])) ?>" + type;
								}
							});
						});
						</script>
						<?php
					}
					else
					{
						$aLinks = array();
						foreach($aPostTypes as $sType => $sLabel)
						{
							$aLinks[] = sprintf('<a href="%s">%s</a>', admin_url(sprintf('post-new.php?post_type=%s&contentdeOrder=%s', $sType, $aOrder['order_id'])), $sLabel);
						}
						echo implode(' | ', $aLinks);
					}
					?>

					<?php elseif($aOrder['order_status'] == 'waiting'): ?>
						<a href="<?php echo contentdeHelper::getPageUrl('rateOrder', array('contentdeOrder' => $aOrder['order_id'])); ?>">Auftrag bewerten</a>
					<?php else: ?>
					<?php endif; ?>

					<?php if($aOrder['order_status'] == 'accepted' || $aOrder['order_status'] == 'canceled'): ?>
					<?php if($aOrder['order_status'] != 'canceled'): ?>
					|
					<?php endif; ?>
					<?php if(!((bool) $aOrder['order_archived'])): ?>
					<a href="<?php echo contentdeHelper::getPageUrl('main', array('noheader' => '1', 'doArchive' => $aOrder['order_id'], contentdePager::PAGE_PARAMETER => $aParams['pager']->getCurrentPage())) ?>">archivieren</a>
					<?php else: ?>
					<a href="<?php echo contentdeHelper::getPageUrl('main', array('noheader' => '1', 'doUnarchive' => $aOrder['order_id'], contentdePager::PAGE_PARAMETER => $aParams['pager']->getCurrentPage())) ?>">dearchivieren</a>
					<?php endif; ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>

			<?php if(isset($aParams['pager']) && $aParams['pager'] instanceof contentdePager && $aParams['pager']->hasPages()): ?>
			<tfoot>
				<tr>
					<th colspan="3">
						<?php echo $aParams['pager']->getDisplayNavigation(contentdeHelper::getPageUrl('main')); ?>
					</th>
				</tr>
			</tfoot>
			<?php endif; ?>

			<?php else: ?>
			<tr>
				<td colspan="3">
					Es wurden keine Auftr&auml;ge gefunden
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<?php endif; ?>
</div>

<?php } ?>