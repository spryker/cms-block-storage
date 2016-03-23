/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved. 
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file. 
 */

'use strict';

var SprykerAjax = require('./legacy/SprykerAjax');
var summernote = require('./summernote');

$(document).ready(function() {
    // editor
    $('.html-editor').summernote(summernote.getConfig());

    /** Draw data tables */
    $('.gui-table-data').dataTable();

    /** Draw data tables without search */
    $('.gui-table-data-no-search').dataTable({
        bFilter: false,
        bInfo: false
    });

    /** all elements with the same class will have the same height */
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

    /** trigger change status active|inactive with an ajax call when click on checkbox */
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
});
