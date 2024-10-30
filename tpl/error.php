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

function contentdeTemplateError($aParams) { ?>
<?php
if(contentdeController::getRequest()->hasParam('noheader'))
{
	require_once(ABSPATH . 'wp-admin/admin-header.php');
}
?>
<div class="wrap">
	<h2>Es ist ein Fehler aufgetreten</h2>

	<?php contentdeLogic::showErrors(); ?>
	<?php contentdeLogic::showSuccesses(); ?>
</div>
<?php } ?>