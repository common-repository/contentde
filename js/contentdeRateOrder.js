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

(function ($) {
	
	var loadMessages = function (showLoading) {

		if(showLoading)
		{
			$('#communications #messages').html('<div id="messagesLoading">Nachrichten werden geladen ...</div>');
		}

		$.post(
			ajaxurl,
			{
				'action': 'contentde-loadOrderMessages',
				'contentdeOrder': contentdeOrder
			},
			function (result) {
				$('#communications #messages').html(result);
				
				$('#messageLoading').hide();
			},
			'html'
		);
	};
	
	$(document).ready(function () {
		$('#button-accept').openDOMWindow({
			eventType: 'click',
			windowSource: 'inline',
			windowSourceID: '#acceptBox',
			width: 600
		});

		$('#button-revise').openDOMWindow({
			eventType: 'click',
			windowSource: 'inline',
			windowSourceID: '#reviseBox',
			width: 600
		});

		$('.closeDomWindow').closeDOMWindow({eventType:'click'});

		loadMessages(true);

		$('#submitNewOrderMessage').click(function () {
			$(this).attr('disabled', 'disabled');
			$('#messageLoading').show();
			$.post(
				ajaxurl,
				{
					'action': 'contentde-writeOrderMessage',
					'contentdeOrder': contentdeOrder,
					'contentdeMessage': $('#newOrderMessage').val()
				},
				function () {
					$('#submitNewOrderMessage').removeAttr('disabled');
					$('#newOrderMessage').val('');
					
					loadMessages(false);
				}
			);
		});
	});
})(jQuery);