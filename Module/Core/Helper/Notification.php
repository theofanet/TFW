<?php

    /**
     * TFrameWork2
     *
     * User: theo
     * Date: 16/02/2017
     */
    class Core_Helper_Notification extends TFW_Helper{

        /**
         * @param string|array $title
         * @param string|array $content
         * @param Core_Model_User|int|array $users
         */
        public function sendNotification($title, $content, $users){
            if(!is_array($users))
                $users = array($users);

            if(!is_array($content))
                $content = array($content);

            if(!is_array($title))
                $title = array($title);

            $title[0]   = addslashes($title[0]);
            $content[0] = addslashes($content[0]);

            $finalTitle   = addslashes(json_encode($title));
            $finalContent = addslashes(json_encode($content));

            $adding_data  = array();
            $created_date = TFW_Registry::getHelper("Core/Time")->getDateTime();
            foreach($users as $user_id){
                if(!is_numeric($user_id) && $user_id instanceof Core_Model_User)
                    $user_id = $user_id->getId();
                else if(!is_numeric($user_id))
                    $user_id = false;

                if($user_id){
                    $adding_data[] = [
                        "id_recipient" => $user_id,
                        "title"        => $finalTitle,
                        "content"      => $finalContent,
                        "created_at"   => $created_date
                    ];
                }
            }

            /*
             * Saving to DB
             */
            $notification_model = TFW_Registry::getModel("Core/User_Notification");
            $notification_model->massCreate($adding_data);
        }

    }