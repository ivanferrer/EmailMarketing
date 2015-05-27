/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	/*	
var folder_raiz = 'template/script/ckeditor/plugins/kcfinder_v251/';

   config.filebrowserBrowseUrl = folder_raiz + 'browse.php?type=files';
	   config.filebrowserImageBrowseUrl = folder_raiz + 'browse.php?type=images';
	   config.filebrowserFlashBrowseUrl = folder_raiz + 'browse.php?type=flash';
	   config.filebrowserUploadUrl = folder_raiz + 'upload.php?type=files';
	   config.filebrowserImageUploadUrl = folder_raiz + 'upload.php?type=images';
	   config.filebrowserFlashUploadUrl = folder_raiz + 'upload.php?type=flash';
*/	   
config.toolbar = 'Full';
	
config.toolbar_Full =
[
    { name: 'document1', items : [ 'Source'] },
	{ name: 'document2', items : [ 'Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
	{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
	
	{ name: 'forms', items : [ 'Form', 'Checkbox', 'Radio','TextField', 'Textarea','Select', 'Button','ImageButton', 'HiddenField'] },
	{ name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] },
	
	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	{ name: 'paragraph1', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv'] },
	
	{ name: 'paragraph2', items : ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
	{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
	{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
	{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
	
	{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
	{ name: 'colors', items : [ 'TextColor','BGColor' ] }
	
];
 
config.toolbar_UltraBasic = {
                             ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
                             };
config.toolbar_Basic = {
                         ['Source'],['Bold','-','Italic','Font','FontSize', 'Format','Link','-','Unlink'],['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']
                         };

config.toolbar_Resume = {
                         ['Source'],['Bold','-','Italic','Font','FontSize', 'Format','Link','-','Unlink'],['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']
                         };

config.toolbar_ResumeImg = {
        ['Source'],['Bold','-','Italic','Font','FontSize', 'Format','Link','-','Unlink'],['Image'],['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']
        };

};
	