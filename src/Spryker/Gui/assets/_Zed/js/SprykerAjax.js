'use strict';

function SprykerAjax() {
    var self = this;

    /** if ajax url is null, the action will be in the same page */
    self.url = null;

    self.dataType = 'json';

    /**
     * @param newUrl
     * @returns {SprykerAjax}
     */
    self.setUrl = function(newUrl){
        self.url = newUrl;
        return self;
    };

    /**
     * @param newDataType
     * @returns {SprykerAjax}
     */
    self.setDataType = function(newDataType){
        self.dataType = newDataType;
        return self;
    };

    /**
     * makes Ajax call and then call a callback function with the response as parameter
     *
     * @param json object options
     * @param callbackFunction
     */
    self.ajaxSubmit = function(options, callbackFunction, parameters) {
        $.ajax({
            url: this.url,
            type: 'post',
            dataType: this.dataType,
            data: options
        })
        .done(function(response){
            if (typeof callbackFunction === 'function') {
                return callbackFunction(response, parameters);
            } else if (typeof callbackFunction === 'string') {
                var call = new SprykerAjaxCallbacks();
                return call[callbackFunction](response, parameters);
            } else {
                return response;
            }
        });
    };

    /* change active  */
    self.changeActiveStatus = function(elementId) {
        var options = {
            id: elementId
        };
        self.ajaxSubmit(options, 'changeStatusMarkInGrid');
    };

}
