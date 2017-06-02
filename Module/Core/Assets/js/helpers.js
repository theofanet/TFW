(function($) {
    if (!$.exist) {
        $.extend({
            exist: function(elm) {
                if (typeof elm == "undefined") return false;
                if (typeof elm != "object") elm = $(elm);
                return elm.length ? true : false;
            }
        });
        $.fn.extend({
            exist: function() {
                return $.exist($(this));
            }
        });
    }
})(jQuery);

(function($) {
    $.fn.onEnter = function(func) {
        this.bind('keypress', function(e) {
            e.stopImmediatePropagation();
            if (e.keyCode == 13) func.apply(this, [this, e]);
        });
        return this;
    };
    $.fn.onTab = function(func) {
        this.bind('keypress', function(e) {
            e.stopImmediatePropagation();
            if (e.keyCode == 9) func.apply(this, [this, e]);
        });
        return this;
    };
    $.fn.onUpdate = function(func) {
        this.on('input', function(e) {
            e.stopImmediatePropagation();
            func.apply(this, [this, e]);
        });
        return this;
    };
})(jQuery);

// Select "placeholder" fake
function selectPlaceholderCheck(el){
    el = $(el);
    el.val() == "" ? el.addClass("select-not-selected") : el.removeClass("select-not-selected");
}

jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
            .indexOf(m[3].toUpperCase()) >= 0;
};

Array.prototype.contains = function(elem) {
    for (var i in this){
        if (this[i] == elem) return true;
    }
    return false;
};


/**
 * Core Helper Module
 */
var Core = {
    openUrl: function(url, newTab){
        if(typeof newTab === "undefined")
            newTab = false;

        if(newTab){
            var win = window.open(url, "_blank");
            win.focus();
        }
        else
            location.href = url;
    },

    reloadPage: function(){
        window.location = window.location.href;
    },

    toggleOverlay: function(message) {
        var overlay = $("#app-page-overlay");
        if(typeof message === "undefined")
            message = "";

        if(overlay.exist()){
            overlay.toggle();

            var h = overlay.css("height");
            if(h == "0px" || h == "0%"){
                $("#overlay-additional-content").html(message);
                h = "100%";
            }
            else{
                $("#overlay-additional-content").html("");
                h = "0%";
            }

            overlay.css("height", h);
        }
    },

    showOverlay: function(message){
        var overlay = $("#app-page-overlay");
        if(typeof message === "undefined")
            message = "";

        if(overlay.exist()){
            overlay.show();
            var h = overlay.css("height");
            if(h == "0px" || h == "0%"){
                $("#overlay-additional-content").html(message);
                h = "100%";
            }
            overlay.css("height", h);
        }
    },

    hideOverlay: function(){
        var overlay = $("#app-page-overlay");
        if(overlay.exist()){
            overlay.hide();
            var h = overlay.css("height");
            if(h != "0px" && h != "0%"){
                $("#overlay-additional-content").html("");
                h = "0%";
            }
            overlay.css("height", h);
        }
    },

    performAction: function(route, data, callback) {
        var q = new TQuery(route);
        if (typeof callback === "undefined")
            q.setCallback(function () {Core.reloadPage()});
        else if(callback !== false)
            q.setCallback(callback);
        if (typeof data === "undefined")
            data = {};
        q.send(data);
    },

    updateModule: function(key){
        Core.toggleOverlay("Updating ...");
        Core.performAction("/settings/module/update", {module_key:key});
    },

    updateCore: function(){
        Core.toggleOverlay("Updating ...");
        Core.performAction("/settings/core/update");
    },

    checkFileImport: function(id, delimiter, route){
        var csv = document.getElementById(id);

        if(csv.files.length) {
            var file   = csv.files[0];
            var reader = new FileReader();
            reader.onloadstart = function () {
                Core.showOverlay();
            };
            reader.onload = function() {
                var query = new TQuery(route);
                query.setCallback(function(data) {
                    $("#check_result").html(data);
                });
                query.send({data: reader.result, delimiter:delimiter});
            };
            reader.onerror = function() {
                Core.hideOverlay();
            };
            reader.readAsText(file);
        }
    },

    removeImportLine: function(id, downgrade){
        if(typeof downgrade === "undefined")
            downgrade = true;

        $("#" + id).remove();
        if(downgrade){
            var c = $("#import_lines_count");
            var cc = parseInt(c.html());
            c.html(cc - 1);
        }
    }
};


/**
 Modal Helper Module
 */
var Modal = {

    toggle: function(id){
        var win = $("#" + id);
        if(win.exist())
            win.modal("toggle");
    },

    load: function(id, route, args){
        var _w = $("#" + id);
        if(_w.exist())
            _w.remove();
        if(typeof args === "undefined")
            args = {};
        var q = new TQuery(route);
        q.setCallback(function(w){$('body').append(w)});
        q.send(args);
    }

};


/**
 Forms Helper Module
 */
var Form = {

    validate: function(id){
        var form  = $("#" + id);
        var valid = true;

        form.find(".form-group[required='true']").each(function(){
            $(this).removeClass("has-error");

            var input_list = $(this).find(".form-control");
            var line_valid = true;

            input_list.each(function(){
                var input    = $(this);
                var regex    = input.attr("regex-validation");
                var regexObj = new RegExp("^" + regex + "$", 'g');

                if(!regexObj.test(input.val()))
                    line_valid = false;
            });

            if(!line_valid){
                valid = false;
                $(this).addClass("has-error");
            }
        });

        return valid;
    }

};

/**
 Tables Helper Module
 */
var Table = {

    sort: function(key, order, id){
        var form   = $("#table_form_" + id);
        var keyH   = $("input#table_sort_key_" + id);
        var orderH = $("input#table_sort_order_" + id);

        keyH.val(key);
        orderH.val(order);
        form.submit();
    },

    massAction: function(route, id){
        var form = $("#table_form_" + id);
        form.attr("action", route);
        form.submit();
    }

};



var substringMatcher = function(strs) {
    return function findMatches(q, cb) {
        var matches, substringRegex;

        // an array that will be populated with substring matches
        matches = [];

        // regex used to determine if a string contains the substring `q`
        substrRegex = new RegExp(q, 'i');

        // iterate through the pool of strings and for any string that
        // contains the substring `q`, add it to the `matches` array
        $.each(strs, function(i, str) {
            if (substrRegex.test(str)) {
                matches.push(str);
            }
        });

        cb(matches);
    };
};