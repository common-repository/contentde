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

function contentdeFilterMceButtons()
{
	return array('bold', 'italic', 'underline', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'formatselect', 'fontselect', 'fontsizeselect');
}

function contentdeFilterMceButtons2()
{
	return array('bullist', 'numlist', '|', 'outdent', 'indent', '|', 'undo', 'redo', 'cleanup');
}

function contentdeFilterMcePlugins($aPlugins)
{
	$aFilteredPlugins = array();

	foreach($aPlugins as $sPlugin)
	{
		if($sPlugin != 'wordpress' && strpos($sPlugin, 'wp') === false)
		{
			$aFilteredPlugins[] = $sPlugin;
		}
	}

	return $aFilteredPlugins;
}

function contentdeTemplateNewOrder($aParams) {

$aOrderTypes = array(
	'' => 'bitte w&auml;hlen',
	'oo' => 'Open Order',
	'go' => 'Group Order',
	'do' => 'Direct Order'
);

$aSelectPlaceholder = array('' => 'bitte w&auml;hlen');

$aFormData = $aParams['formData'];

$aLevels = $aSelectPlaceholder + $aParams['levels'];
$aGroups = $aSelectPlaceholder + $aParams['groups'];
$aContractors = $aSelectPlaceholder + $aParams['contractors'];
$aProjects = $aSelectPlaceholder + $aParams['projects'];
$aCategories = $aSelectPlaceholder + $aParams['categories'];
$aDurations = range(1, 10);
$aDurations = $aSelectPlaceholder + array_combine($aDurations, $aDurations);

$aBriefings = array();

foreach($aParams['briefings'] as $iTemplateId => $aTemplate)
{
	$aBriefings[$iTemplateId] = $aTemplate['title'];
}

$aBriefings = $aSelectPlaceholder + $aBriefings;

wp_enqueue_style('contentde-newOrder-css', contentdeHelper::getPluginUrl('css/contentdeNewOrder.css'));
wp_enqueue_script('contentde-newOrder-js', contentdeHelper::getPluginUrl('js/contentdeNewOrder.js'), array(), false, true);

add_filter('mce_buttons', 'contentdeFilterMceButtons');
add_filter('mce_buttons_2', 'contentdeFilterMceButtons2');
add_filter('tiny_mce_plugins', 'contentdeFilterMcePlugins');

?>

<script type="text/javascript">
var keywords = <?php echo json_encode(contentdeHelper::getValue($aFormData, 'keywords', array(''))); ?>;
var briefings = <?php echo json_encode($aParams['briefings']); ?>;
</script>

<div class="wrap">
	<h2>neuen content.de Auftrag erstellen</h2>

	<?php echo contentdeLogic::showErrors(); ?>
	<?php echo contentdeLogic::showSuccesses(); ?>

	<?php if(count($aParams['savedOrders']) > 0): ?>

	<div id="savedOrders">
		<h3>Zwischengespeicherte Auftr&auml;ge</h3>

		<table class="wp-list-table widefat fixed">
			<thead>
				<tr>
					<th>Titel</th>
					<th>bearbeiten</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($aParams['savedOrders'] as $iId => $aData): ?>
				<tr>
					<td>
						<?php echo contentdeHelper::getValue($aData, 'title', 'unbenannt') ?>

						<?php if($iId === $aParams['savedOrderId']): ?>
						(ausgew&auml;hlt)
						<?php endif; ?>
					</td>
					<td>
						<a href="<?php echo contentdeHelper::getPageUrl('newOrder', array('load_order' => $iId)) ?>">Auftrag laden</a>
						/
						<a href="<?php echo contentdeHelper::getPageUrl('newOrder', array('remove_saved_order' => $iId)) ?>">Auftrag l&ouml;schen</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	</div>

	<?php endif; ?>

	<form action="<?php echo contentdeHelper::getPageUrl('newOrder', array('noheader' => 1)); ?>" method="post">

		<?php if($aParams['savedOrderId'] !== null): ?>
		<input type="hidden" name="remove_saved_order" value="<?php echo $aParams['savedOrderId'] ?>" />
		<?php endif; ?>

		<div id="orderTypeSelect">
			<h3>Auftragstyp w&auml;hlen</h3>
			<table class="form-table">
				<tr>
					<th>Auftragstyp</th>
					<td>
						<?php echo contentdeHelper::buildSelect($aOrderTypes, contentdeHelper::getValue($aFormData, 'type'), array('name' => 'new_order[type]', 'class' => 'contentde-input-small')); ?>
					</td>
				</tr>
			</table>
		</div>

		<div id="orderDataOO" style="display: none;">
			<h3>Open Order Daten</h3>
			<table class="form-table">
				<tr>
					<th>Qualit&auml;tsniveau</th>
					<td>
						<?php echo contentdeHelper::buildSelect($aLevels, contentdeHelper::getValue($aFormData, 'oo_level'), array('name' => 'new_order[oo_level]', 'class' => 'contentde-input-small')); ?>
					</td>
				</tr>
			</table>
		</div>

		<div id="orderDataGO" style="display: none;">
			<h3>Group Order Daten</h3>
			<table class="form-table">
				<tr>
					<th>Qualit&auml;tsniveau</th>
					<td>
						<?php echo contentdeHelper::buildSelect($aLevels, contentdeHelper::getValue($aFormData, 'go_level'), array('name' => 'new_order[go_level]', 'class' => 'contentde-input-small')); ?>
					</td>
				</tr>
				<tr>
					<th>Gruppe</th>
					<td>
						<?php echo contentdeHelper::buildSelect($aGroups, contentdeHelper::getValue($aFormData, 'go_group'), array('name' => 'new_order[go_group]', 'class' => 'contentde-input')); ?>
					</td>
				</tr>
			</table>
		</div>

		<div id="orderDataDO" style="display: none;">
			<h3>Direct Order Daten</h3>
			<table class="form-table">
				<tr>
					<th>Autor</th>
					<td>
						<?php echo contentdeHelper::buildSelect($aContractors, contentdeHelper::getValue($aFormData, 'do_contractor'), array('name' => 'new_order[do_contractor]', 'class' => 'contentde-input')); ?>
					</td>
				</tr>
			</table>
		</div>

		<div id="orderData" style="display: none;">
			<h3>Generelle Auftragsdaten</h3>
			<table class="form-table">

				<tr>
					<th>Titel</th>
					<td style="width: 375px;">
						<input type="text" name="new_order[title]" value="<?php echo contentdeHelper::getValue($aFormData, 'title'); ?>" class="contentde-input" />
					</td>
					<td rowspan="<?php echo count($aBriefings) > 1 ? 8 : 7 ?>" valign="top">
						<fieldset id="budget">
							<legend>
								Ihr Budget
								<span id="loadingCosts" style="display: none;">
									<img src="<?php echo admin_url('/images/wpspin_light.gif') ?>" style="vertical-align: middle;" />
								</span>
							</legend>
							<table>
								<tr>
									<td>Verf&uuml;gbares Guthaben</td>
									<td><span id="avail_balance">0,00 Eur</span></td>
								</tr>
								<tr id="avail_budget_row" style="display: none;">
									<td>Verf&uuml;gbares Budget</td>
									<td><span id="avail_budget">0,00 Eur</span></td>
								</tr>
								<tr>
									<td>Kosten pro Wort</td>
									<td><span id="order_costs_per_word">0,00 Eur</span></td>
								</tr>
								<tr>
									<td>Kosten dieses Auftrags</td>
									<td><span id="order_costs">0,00 Eur</span></td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th>Projekt</th>
					<td>
						<?php echo contentdeHelper::buildSelect($aProjects, contentdeHelper::getValue($aFormData, 'project'), array('name' => 'new_order[project]', 'class' => 'contentde-input','id' => 'new_order_project')); ?>
					</td>
				</tr>

				<tr>
					<th>Kategorie</th>
					<td>
						<?php echo contentdeHelper::buildSelect($aCategories, contentdeHelper::getValue($aFormData, 'category'), array('name' => 'new_order[category]', 'class' => 'contentde-input')); ?>
					</td>
				</tr>

				<tr>
					<th>Bearbeitungsdauer</th>
					<td>
						<?php echo contentdeHelper::buildSelect($aDurations, contentdeHelper::getValue($aFormData, 'duration'), array('name' => 'new_order[duration]', 'class' => 'contentde-input-small')); ?> Tage
					</td>
				</tr>

				<tr>
					<th>W&ouml;rter</th>
					<td>
						<span class="field-align">
							min.: <input type="text" name="new_order[min_words]" value="<?php echo contentdeHelper::getValue($aFormData, 'min_words', 300); ?>" class="contentde-input-very-small" />
						</span>
						<span class="field-align">
							max.: <input type="text" name="new_order[max_words]" value="<?php echo contentdeHelper::getValue($aFormData, 'max_words', 400); ?>" class="contentde-input-very-small" />
						</span>
					</td>
				</tr>

				<tr>
					<th>Keyworddichte</th>
					<td>
						<span class="field-align">
							min.: <input type="text" name="new_order[min_keyword_density]" value="<?php echo contentdeHelper::getValue($aFormData, 'min_keyword_density', 0.1); ?>" class="contentde-input-very-small" /> %
						</span>
						<span class="field-align">
							max.: <input type="text" name="new_order[max_keyword_density]" value="<?php echo contentdeHelper::getValue($aFormData, 'max_keyword_density', 1.0); ?>" class="contentde-input-very-small" /> %
						</span>
					</td>
				</tr>

				<tr>
					<th>Keywords</th>
					<td>
						<div id="keywordContainer">
						</div>
					</td>
				</tr>

				<?php if(count($aBriefings) > 1): ?>
				<tr>
					<th>Briefingvorlage</th>
					<td>
						<?php echo contentdeHelper::buildSelect($aBriefings, '', array('class' => 'contentde-input', 'id' => 'new_order_briefing_template')); ?>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<th>Briefing</th>
					<td colspan="2">
						<?php wp_editor(contentdeHelper::getValue($aFormData, 'description', ''), 'new_order_description', array('media_buttons' => false, 'quicktags' => false, 'textarea_name' => 'new_order[description]', 'editor_height' => 600, 'textarea_rows' => 40)); ?>
					</td>
				</tr>

				<tr>
					<th>&nbsp;</th>
					<td colspan="2">
						<input type="submit" name="create_new_order" value="Auftrag anlegen" class="button-primary" />
						<input type="submit" name="save_new_order" value="Auftrag zwischenspeichern" class="button-primary" />
					</td>
				</tr>

			</table>
		</div>

	</form>

	<div id="keywordProtoContainer">
		<div class="keyword">
			<input type="text" name="new_order[keywords][]" value="" class="keyword contentde-input" />
			<span>
				<input type="button" value=" - " class="del_keyword button-secondary" />
				<input type="button" value=" + " class="add_keyword button-secondary" />
			</span>
		</div>
	</div>

</div>
<?php } ?>