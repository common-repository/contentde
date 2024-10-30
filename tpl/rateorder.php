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

function contentdeTemplateRateOrder($aParams) { ?>

<?php

wp_enqueue_style('contentde-rateOrder-css', contentdeHelper::getPluginUrl('css/contentdeRateOrder.css'));
wp_enqueue_script('jquery-domwindow-js', contentdeHelper::getPluginUrl('js/jquery.domwindow.js'));
wp_enqueue_script('contentde-rateOrder-js', contentdeHelper::getPluginUrl('js/contentdeRateOrder.js'));

$aTypes = array(
	'open_order' => 'Open Order',
	'group_order' => 'Group Order',
	'direct_order' => 'Direct Order'
);

?>

<script type="text/javascript">
var contentdeOrder = "<?php echo $aParams['order']['order_id'] ?>";
</script>

<div class="wrap">
	<h2>content.de Auftrag bewerten</h2>

	<?php contentdeLogic::showErrors(); ?>
	<?php contentdeLogic::showSuccesses(); ?>

	<?php if($aParams['order']['status'] == 'waiting'): ?>

	<div class="orderInfo">
		<span>Typ</span>
		<?php echo $aTypes[$aParams['order']['type']] ?>
	</div>

	<div class="orderInfo">
		<span>Auftragstitel</span>
		<?php echo $aParams['order']['order_title'] ?>
	</div>

	<div class="orderInfo">
		<span>Kosten</span>
		<?php echo contentdeHelper::formatNumber($aParams['order']['costs'], 'Eur'); ?>
	</div>

	<div class="orderInfo">
		<span>Autor</span>
		<?php echo $aParams['order']['contractor_name']; ?>
		(<?php echo $aParams['order']['contractor_id']; ?>)
	</div>

	<div class="orderInfo">
		<span>Bearbeitungszeit</span>
		<?php echo $aParams['order']['processing_time'] ?> Tage
	</div>

	<div class="orderInfo">
		<span>W&ouml;rter</span>
		<?php echo $aParams['order']['min_words'] ?>
		-
		<?php echo $aParams['order']['max_words'] ?>
		(<?php echo $aParams['order']['final_words'] ?>)
	</div>

	<?php if(count($aParams['order']['keywords']) > 0): ?>
	<table class="keywordTable">
		<thead>
			<tr>
				<th>Keyword</th>
				<td>Dichte</td>
				<td>Anzahl</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach($aParams['order']['keywords'] as $aKeyword): ?>
			<tr>
				<td><?php echo $aKeyword['title'] ?></td>
				<td>
					<?php echo contentdeHelper::formatNumber($aKeyword['final_density'], '%'); ?>
				</td>
				<td>
					<?php echo $aKeyword['final_count'] ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>

	<div id="communications">
		<div id="communicationTitle">
			Kommunikation mit dem Autor
		</div>
		<div id="messages">
		</div>
		<div id="writeMessage">
			<div id="writeMessageTitle">Nachricht verfassen</div>
			<div>
				<textarea id="newOrderMessage"></textarea><br />
				<input type="button" id="submitNewOrderMessage" value="abschicken" class="button-primary" />
				<span id="messageLoading" style="display: none;">
					<img src="<?php echo admin_url('/images/wpspin_light.gif') ?>" style="vertical-align: middle;" />
				</span>
			</div>
		</div>
	</div>

	<div class="contentde-textpreview">
		<div class="contentde-title">
			<h3><?php echo $aParams['order']['title']; ?></h3>
		</div>
		<div class="contentde-text">
			<?php if($aParams['order']['contains_html']): ?>
			<?php echo $aParams['order']['text_highlighted']; ?>
			<?php else: ?>
			<?php echo nl2br($aParams['order']['text_highlighted']); ?>
			<?php endif; ?>
		</div>
	</div>

	<div>
		<input type="button" id="button-accept" value="Annehmen" class="button-primary" />
		<input type="button" id="button-revise" value="Verbessern lassen" class="button-primary" />
	</div>

	<div style="display: none;">
		<div id="acceptBox">
			<div>
				<form action="<?php echo contentdeHelper::getPageUrl('rateOrder', array('contentdeOrder' => $aParams['order']['order_id'], 'noheader' => 1)); ?>" method="post">
					<div>
						<h3>Text annehmen</h3>
						<p>
							Der voreingestellt Mittelwert bedeutet "genau wie erwartet".
							Positve oder negative Abweichungen k&ouml;nnen in 2 Stufen
							angegeben werden.
						</p>
					</div>
					<div class="rateOrderRatingSpacer">
						<div class="rateOrderRating">
							<span class="rateOrderRatingTitle">Inhalt &amp; Vorgabenerf&uuml;llung</span>
							<input type="radio" name="ratingContent" class="ratingContent" value="-2" />
							<input type="radio" name="ratingContent" class="ratingContent" value="-1" />
							<input type="radio" name="ratingContent" class="ratingContent" value="0" checked="checked" />
							<input type="radio" name="ratingContent" class="ratingContent" value="1" />
							<input type="radio" name="ratingContent" class="ratingContent" value="2" />
						</div>
						<div class="rateOrderRating">
							<span class="rateOrderRatingTitle">Rechtschreibung &amp; Grammatik</span>
							<input type="radio" name="ratingForm" class="ratingForm" value="-2" />
							<input type="radio" name="ratingForm" class="ratingForm" value="-1" />
							<input type="radio" name="ratingForm" class="ratingForm" value="0" checked="checked" />
							<input type="radio" name="ratingForm" class="ratingForm" value="1" />
							<input type="radio" name="ratingForm" class="ratingForm" value="2" />
						</div>
						<div class="rateOrderRating">
							<span class="rateOrderRatingTitle">Ausdrucksform &amp; Lesbarkeit</span>
							<input type="radio" name="ratingReadability" class="ratingReadability" value="-2" />
							<input type="radio" name="ratingReadability" class="ratingReadability" value="-1" />
							<input type="radio" name="ratingReadability" class="ratingReadability" value="0" checked="checked" />
							<input type="radio" name="ratingReadability" class="ratingReadability" value="1" />
							<input type="radio" name="ratingReadability" class="ratingReadability" value="2" />
						</div>
						<div class="rateOrderRating">
							<span class="rateOrderRatingTitle">Kommunikation &amp; Termintreue</span>
							<input type="radio" name="ratingCommunication" class="ratingCommunication" value="-2" />
							<input type="radio" name="ratingCommunication" class="ratingCommunication" value="-1" />
							<input type="radio" name="ratingCommunication" class="ratingCommunication" value="0" checked="checked" />
							<input type="radio" name="ratingCommunication" class="ratingCommunication" value="1" />
							<input type="radio" name="ratingCommunication" class="ratingCommunication" value="2" />
						</div>
					</div>
					<div class="rateOrderReview">
						<span class="rateOrderReviewTitle">Rezension</span>
						<textarea name="acceptOrderReview"></textarea>
					</div>
					<div>
						<input type="submit" name="accept_order_button" class="button-primary" value="Annehmen" />
						<input type="button" class="button-primary closeDomWindow" value="Abbrechen" />
					</div>
				</form>
			</div>
		</div>
		<div id="reviseBox">
			<div>
				<form action="<?php echo contentdeHelper::getPageUrl('rateOrder', array('contentdeOrder' => $aParams['order']['order_id'], 'noheader' => 1)); ?>" method="post">
					<div>
						<h3>Text verbessern lassen</h3>
					</div>
					<div class="rateOrderReview">
						<span class="rateOrderReviewTitle">Begr&uuml;ndung f&uuml;r Verbesserung</span>
						<textarea name="reviseOrderReview"></textarea>
					</div>
					<div>
						<input type="submit" name="revise_order_button" class="button-primary" value="Verbessern lassen" />
						<input type="button" class="button-primary closeDomWindow" value="Abbrechen" />
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php endif; ?>
</div>
<?php } ?>