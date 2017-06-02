<?php
    /**
     * Created by Theo.
     */
    if(isset($notification)){

        $titles   = json_decode($notification->title, false);
        $titles[] = 'Notifications';

        $contents   = json_decode($notification->content, false);
        $contents[] = 'Notifications';

        $title   = call_user_func_array(array($this, '___'), $titles);
        $content = call_user_func_array(array($this, '___'), $contents);
        $content .= "<div class=\'notification-date\'><small><em>".TFW_Registry::getHelper("Core/Time")->formatDate($notification->created_at)."</em></small></div>";
        
        echo "<li class=\"user-notification-element\">"
            ."<a href=\"javascript:void\" class=\"notification-popover\" data-toggle=\"popover\" data-trigger=\"focus\" data-html=\"true\" title=\"$title\" "
            ."data-content=\"$content\" data-placement=\"left\" role=\"button\" style=\"padding:0\" notification-id=\"$notification->id\" notification-seen=\"$notification->seen\">"
            ."<table style=\"width:100%;\" class=\"notification-table\">"
            ."<tr>"
            ."<td id=\"notification-$notification->id-bullet\" class=\"notification-bullet\">"
            .(!$notification->seen ? "&bullet;" : " ")
            ."</td>"
            ."<td class=\"notification-date\"><span class=\"label label-warning\">"
            .TFW_Registry::getHelper("Core/Time")->getExplicitDate($notification->created_at)
            ."</span></td>"
            ."<td class=\"notification-title\">$title</td>"
            ."</tr>"
            ."</table></a></li>";
    }
