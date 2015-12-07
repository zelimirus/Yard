/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
    KCFINDER_BASE = '/golive_theme/js/kcfinder';
    config.filebrowserBrowseUrl = KCFINDER_BASE + '/browse.php?type=files';
    config.filebrowserImageBrowseUrl = KCFINDER_BASE + '/browse.php?type=images';
    config.filebrowserFlashBrowseUrl = KCFINDER_BASE + '/browse.php?type=flash';
    config.filebrowserUploadUrl = KCFINDER_BASE + '/upload.php?type=files';
    config.filebrowserImageUploadUrl = KCFINDER_BASE + '/upload.php?type=images';
    config.filebrowserFlashUploadUrl = KCFINDER_BASE + '/upload.php?type=flash';
};