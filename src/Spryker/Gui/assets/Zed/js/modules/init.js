/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var SprykerAjax = require('./legacy/SprykerAjax');
var editor = require('ZedGuiEditorConfiguration');
var Tabs = require('./libs/tabs');
var TranslationCopyFields = require('./libs/translation-copy-fields');
var Ibox = require('./libs/ibox');

$(document).ready(function() {
    // editor
    $('.html-editor').summernote(editor.getConfig());

    /* Draw data tables */
    $('.gui-table-data').dataTable({
        scrollX: 'auto',
        autoWidth: false
    });

    /* Draw data tables without search */
    $('.gui-table-data-no-search').dataTable({
        bFilter: false,
        bInfo: false,
        scrollX: 'auto',
        autoWidth: false
    });

    /* All elements with the same class will have the same height */
    $('.fix-height').sprykerFixHeight();

    $('.spryker-form-autocomplete').each(function(key, value) {
        var obj = $(value);
        if (obj.data('url') === 'undefined') {
            return;
        }
        obj.autocomplete({
            source: obj.data('url'),
            minLength: 3
        });
    });

    /* Trigger change status active|inactive with an ajax call when click on checkbox */
    $('.gui-table-data').on('click', '.active-checkbox', function() {
        var elementId = $(this).attr('id').replace('active-', '');
        spyAj.setUrl('/discount/voucher/status').changeActiveStatus(elementId);
    });

    $('.table-dependency tr').hover(
        function(){
            $(this).addClass('warning');
        },
        function(){
            $(this).removeClass('warning');
        }
    );
    $('.table-dependency .btn-xs').hover(
        function(){
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        },
        function(){
            $(this).addClass('btn-default');
            $(this).removeClass('btn-primary');
        }
    );

    $('.dropdown-toggle').dropdown();
    $('.spryker-form-select2combobox').select2();

    /* Init tabs */
    $('.tabs-container').each(function(index, item){
        new Tabs(item);
    });

    /* Init translation copy fields */
    new TranslationCopyFields();

    /* Init iboxes */
    new Ibox();
});
