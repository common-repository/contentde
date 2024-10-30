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

function contentdeTemplateLoadorderMessages($aParams) { ?>
<?php if(count($aParams['messages']) > 0): ?>
<?php krsort($aParams['messages']); ?>
<?php foreach($aParams['messages'] as $aMessage): ?>
<div class="message messageFrom<?php echo ucfirst($aMessage['from']) ?>">
	<div class="messageTitle">
		Am <?php echo date('d.m.Y \\u\\m H:i:s \\U\\h\\r', strtotime($aMessage['date'])); ?>
		<?php if($aMessage['from'] == 'client'): ?>
		schrieben Sie:
		<?php else: ?>
		schrieb der Autor:
		<?php endif; ?>
	</div>
	<div class="messageContent">
		<?php echo nl2br($aMessage['text']); ?>
	</div>
</div>
<?php endforeach; ?>
<?php else: ?>
keine Nachrichten gefunden
<?php endif; ?>
<?php } ?>