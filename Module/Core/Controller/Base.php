<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    class Core_Controller_Base extends TFW_Controller{

        public function render404(){
            $this->render("Core/404");
        }

        public function setNotificationSeen(){
            $id_notification = $this->id;

            if($id_notification){
                $notification = TFW_Registry::getModel("Core/User_Notification");
                $notification->load($id_notification);

                if($notification->isLoaded()){
                    $notification->seen = 1;
                    $notification->save();
                    $this->TQuery_Result();
                }
                else
                    $this->TQuery_Error($this->__("Unable to load notification"));
            }
            else
                $this->TQuery_Error($this->__("Missing data"));
        }

    }