/**
 * SVGEdit extension: add 'Edit drawing' button
 * @copyright 2010 Brion Vibber <brion@pobox.com>
 */

(function($, mw) {

$(document).ready(function() {
	// We probably should check http://www.w3.org/TR/SVG11/feature#SVG-dynamic
	// but Firefox is missing a couple random subfeatures.
	//
	// Chrome, Safari, Opera, and IE 9 preview all return true for it!
	//
	if (!document.implementation.hasFeature('http://www.w3.org/TR/SVG11/feature#Shape', '1.1')) {
		return;
	}

	if (mw.config.get('wgCanonicalNamespace') === 'File' &&
		mw.config.get('wgAction') === 'view' &&
		mw.config.get('wgTitle').match(/\.svg$/i)) {

		var trigger = function() {
			mw.svgedit.open({
				filename: mw.config.get('wgTitle'),
				replace: (mw.config.get('wgArticleId') ? '#file' : '#mw-imagepage-nofile'),
				onclose: function(filename) {
					if (filename) {
						// Saved! Refresh parent window...
						window.location.reload(true);
					}
				},
				leaveopen: true // Our reload will get rid of the UI.
			});
		};

		var tab = mw.util.addPortletLink('p-cactions',
			document.location + '#!action=svgedit',
			mw.msg('svgedit-edit-tab'),
			'ca-ext-svgedit',
			mw.msg('svgedit-edit-tab-tooltip'),
			'',
			document.getElementById('ca-edit'));

		$(tab).find('a').click(function(event) {
			trigger();
			event.preventDefault();
			return false;
		});

		var button = $('<button></button>')
			.text(mw.msg(mw.config.get('wgArticleId') ? 'svgedit-editbutton-edit' : 'svgedit-editbutton-create'))
			.click(function() {
				trigger();
			});
		$(wgArticleId ? '.fullMedia' : '#mw-imagepage-nofile').append(button);

		if (window.location.hash.indexOf('!action=svgedit') !== -1) {
			window.location.hash = '';
			trigger();
		}
	}
});

})(jQuery, mediaWiki);
