
$(document).ready(function(){
    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();
    $('.autofocus').focus();

    $('ul.dropdown-menu.mega-dropdown').click(function(event){
        event.stopPropagation();
    });

    // Notification center event handler
    $("a.notification-popover").on("shown.bs.popover", function(){
        var seen = parseInt($(this).attr("notification-seen"));
        var id   = $(this).attr("notification-id");
        var nbHidden = $("input[name='nb-new-notification']");
        if(!seen){
            var query = new TQuery("/notification/seen");
            query.setCallback(function(){
                $("#notification-"+id+"-bullet").html("");
                var nb = parseInt(nbHidden.val());
                nb--;
                if(nb <= 0)
                    $("a#user-notification").removeClass("notif-animation");
                nbHidden.val(nb);
            });
            query.send({id:id});
        }
    });

    $("td.massAction_check, th.massAction_check").on("click", function(e){
        e.stopImmediatePropagation();
        var chk = $(this).find('input:checkbox:enabled').get(0);
        if(e.target != chk)
            chk.checked = !chk.checked;
        $(chk).trigger("change");
    });

    $("input.master-checkbox").on("change", function(e){
        var chk   = this;
        var table = $(chk)
            .parent()  // TH
            .parent()  // TR
            .parent()  // THEAD
            .parent(); // TABLE

        table.find("tbody tr td.massAction_check input[type='checkbox']").each(function(){
            this.checked = chk.checked;
        });
    });
});
