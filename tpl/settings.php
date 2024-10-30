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

function contentdeTemplateSettings($aParams) {

	$aPerPage = array(
		5 => 5,
		10 => 10,
		20 => 20,
		30 => 30,
		50 => 50,
		75 => 75,
		100 => 100,
	);
?>

<div class="wrap">

	<h2>content.de Zugangsdaten</h2>

	<?php echo contentdeLogic::showErrors(); ?>
	<?php echo contentdeLogic::showSuccesses(); ?>

	<form action="<?php echo contentdeHelper::getPageUrl('settings', array('noheader' => 1)); ?>" method="post">
		<table class="form-table">
			<tr>
				<th>E-Mail / API-Key</th>
				<td><input type="text" name="clogin" class="regular-text contentde-input" value="<?php echo CONTENTDE_LOGIN ?>" /></td>
			</tr>
			<tr>
				<th>Passwort</th>
				<td><input type="password" name="cpassword" class="regular-text contentde-input" value="<?php echo CONTENTDE_PASSWORD ?>" /></td>
			</tr>
			<tr>
				<th></th>
				<td>
					<?php if(CONTENTDE_HAS_LOGIN_DATA): ?>
					<input type="submit" name="save_login_data" value="&Auml;ndern" class="button-primary" />
					<input type="submit" name="clear_login_data" value="Zugangsdaten entfernen" class="button-primary" />
					<?php else: ?>
					<input type="submit" name="save_login_data" value="Speichern" class="button-primary" />
					<?php endif; ?>
				</td>
			</tr>
		</table>
	</form>

	<h2>Pager</h2>

	<form action="<?php echo contentdeHelper::getPageUrl('settings', array('noheader' => 1)); ?>" method="post">
		<table class="form-table">
			<tr>
				<th>Aufträge pro Seite</th>
				<td><?php echo contentdeHelper::buildSelect($aPerPage, CONTENTDE_PAGER_PER_PAGE, array('name' => 'perPage')); ?></td>
			</tr>
			<tr>
				<th></th>
				<td>
					<input type="submit" name="save_per_page" value="Speichern" class="button-primary" />
				</td>
			</tr>
		</table>
	</form>

	<h2>Erweitert</h2>

	<form action="<?php echo contentdeHelper::getPageUrl('settings', array('noheader' => 1)); ?>" method="post">
		<table class="form-table">
			<tr>
				<th>bei Klick "Seite/Beitrag erstellen" Order automatisch archivieren</th>
				<td><input type="checkbox" value="1" name="param_post_and_archive" <?php echo (CONTENTDE_POST_AND_ARCHIVE == 1)? 'checked="checked"': ""?>/></td>
			</tr>
			<tr>
				<th></th>
				<td>
					<input type="submit" name="save_post_and_archive" value="Speichern" class="button-primary" />
				</td>
			</tr>
		</table>
	</form>
</div>

<?php } ?>