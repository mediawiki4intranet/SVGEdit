<?php
/**
 * SVGEdit extension: hooks
 * @copyright 2010 Brion Vibber <brion@pobox.com>
 */

class SVGEditHooks {
	/* Static Methods */

	/**
	 * WikiEditorAddModules hook handler
	 */
	public static function addButtonModule() {
		global $wgOut, $wgUser;
		if ( in_array( 'ext.wikiEditor.toolbar', $wgOut->getModules() ) &&
			 $wgUser->isAllowed( 'upload' ) ) {
			$wgOut->addModules( array( 'ext.svgedit.toolbar' ) );
		}
		return true;
	}

	/**
	 * BeforePageDisplay hook
	 *
	 * Adds the modules to the page
	 *
	 * @param $out OutputPage output page
	 * @param $skin Skin current skin
	 */
	public static function beforePageDisplay( $out, $skin ) {
		global $wgUser, $wgRequest, $wgSVGEditInline;
		$title = $out->getTitle();
		$modules = array();
		if( self::trigger( $title ) ) {
			$modules[] = 'ext.svgedit.editButton';
		}
		if ($wgSVGEditInline) {
			// Experimental inline edit trigger.
			// Potentially expensive and tricky as far as UI on article pages!
			if( $wgUser->isAllowed( 'upload' ) ) {
				$modules[] = 'ext.svgedit.inline';
			}
		}
		if ($wgRequest->getVal('action') == 'edit' || $wgRequest->getVal('action') == 'submit') {
			if( $wgUser->isAllowed( 'upload' ) && $wgUser->getOption( 'usebetatoolbar' ) ) {
				$modules[] = 'ext.svgedit.toolbar';
			}
		}
		if ($modules) {
			$out->addModules($modules);
		}
		return true;
	}

	/**
	 * MakeGlobalVariablesScript hook
	 *
	 * Exports a setting if necessary.
	 *
	 * @param $vars array of vars
	 */
	public static function makeGlobalVariablesScript( &$vars ) {
		global $wgTitle, $wgSVGEditEditor;
		#if( self::trigger( $wgTitle ) ) {
			$vars['wgSVGEditEditor'] = $wgSVGEditEditor;
		#}
		return true;
	}

	/**
	 * Should the editor links trigger on this page?
	 *
	 * @param Title $title
	 * @return boolean
	 */
	private static function trigger( $title ) {
		return $title && $title->getNamespace() == NS_FILE &&
			$title->userCan( 'edit' ) && $title->userCan( 'upload' );
	}

	/**
	 * UploadForm:initial hook, suggests creating non-existing SVG files with SVGEdit
	 */
	public static function uploadFormInitial( $upload ) {
		if ( strtolower( substr( $upload->mDesiredDestName, -4 ) ) == '.svg' ) {
			$title = Title::newFromText( $upload->mDesiredDestName, NS_FILE );
			if ( $title ) {
				$upload->uploadFormTextTop .= wfMsgNoTrans(
					'svgedit-suggest-create', $title->getFullUrl().'#!action=svgedit'
				);
			}
		}
		return true;
	}

}
