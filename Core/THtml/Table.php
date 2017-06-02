<?php

    /**
     * TFrameWork2
     *
     * TODO: Pagination
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    class THtml_Table extends THtml_Base{
        private $_title = false;
        private $_key = "";

        private $_headers = array();
        private $_lines   = array();

        private $_actions        = array();
        private $_actionsGrouped = true;
        private $_lineAction     = null;
        private $_current_line   = array();
        private $_custom_action  = null;

        private $_line_class_handler   = NULL;
        private $_line_options_handler = NULL;

        private static $_options_keyword = [
            "list_data",
            "cell_format",
            "cell_options",
            "format_args",
            "data_mask"
        ];

        /**
         * @var TDB_Model $_model
         */
        private $_model          = null;
        private $_headerPosition = array();
        private $_filters        = array();
        private $_massAction     = array();
        private $_sorted         = null;
        private $_sortBy         = [
            "key"   => false,
            "order" => "ASC"
        ];

        /**
         * THtml_Table constructor.
         *
         * @param string      $key
         * @param bool|string $id
         * @param bool|string $class
         */
        public function __construct($key = "default", $id = false, $class = "table-striped table-hover"){
            $class .= " table";
            parent::__construct($id, $class);

            if(!$key)
                $key = "default";

            $this->_key = $key;

            /*
             * Save filter data to sessions if form triggered
             */
            $form_triggered = TFW_Registry::getPostData("table_form_trigger");

            if($form_triggered && $form_triggered == $this->_key){
                $keywords = ["filter", "sort"];
                foreach(TFW_Registry::getPostArray() as $post_key => $post){
                    if($post_key != "table_form_trigger" && ($post !== false)){
                        $parts = explode(":", $post_key);

                        if(count($parts) > 1 && in_array($parts[0], $keywords)){
                            $data = array_shift($parts);
                            $name = implode(":", $parts);
                            TFW_Registry::setVar("table:$data:$this->_key:$name", $post);
                        }
                    }
                }
            }
        }

        /**
         * @param string $title
         *
         * @return $this
         */
        public function setTitle($title){
            $this->_title = $title;
            return $this;
        }

        /**
         * @param bool $b
         *
         * @return $this
         */
        public function setBordered($b = true){
            if($b)
                $this->addClass("table-bordered");
            else
                $this->removeClass("table-bordered");

            return $this;
        }

        /**
         * @param string $label
         * @param array  $options
         *
         * @return $this
         */
        public function pushHeader($label, $options = array()){
            $positionKey = false;

            if(isset($options["filter_data"]))
                $positionKey = $options["filter_data"];
            else if(isset($options["list_data"]) && $options["list_data"] != "THIS")
                $positionKey = $options["list_data"];

            if($positionKey)
                $this->_headerPosition[$positionKey] = count($this->_headers);

            $this->_headers[] = [$label, $options];

            return $this;
        }

        /**
         * @param \THtml_Table_Filter $filter
         *
         * @return $this
         */
        public function addFilter(THtml_Table_Filter $filter){
            if($this->_model && isset($this->_headerPosition[$filter->getName()])){
                $position = $this->_headerPosition[$filter->getName()];
                $filter->setPosition($position);

                $value = TFW_Registry::getVar("table:filter:".$this->_key.":".$filter->getName());
                if($value !== null && $value !== ""){
                    $this->_model->addWhere($filter->getName(), $filter->format($value));
                    $filter->setValue($value);
                }

                $this->_filters[$position] = $filter;
            }

            return $this;
        }

        /**
         * @return $this
         */
        public function setSorted(){
            $this->_sorted = true;

            $sort_key   = TFW_Registry::getVar("table:sort:$this->_key:key");
            $sort_order = TFW_Registry::getVar("table:sort:$this->_key:order");

            if($sort_key){
                $this->_sortBy["key"] = $sort_key;
                if($sort_order)
                    $this->_sortBy["order"] = $sort_order;
            }

            return $this;
        }

        /**
         * @param string $label
         * @param string $route
         *
         * @return $this
         */
        public function addMassAction($label, $route){
            $this->_massAction[] = [$label, $route];
            return $this;
        }

        /**
         * @param string $value
         * @param array  $options
         *
         * @return $this
         */
        public function addCell($value, $options = array()){
            $this->_current_line[] = [$value, $options];
            return $this;
        }

        /**
         * @param array $options
         *
         * @return $this
         */
        public function addCurrentLine($options = array()){
            $this->_lines[] = [$this->_current_line, $options];
            $this->_current_line = [];

            return $this;
        }

        /**
         * @param string $label
         * @param string $jsAction
         * @param string $class
         *
         * @return $this
         */
        public function addAction($label, $jsAction, $class = "default"){
            $this->_actions[] = [$label, $jsAction, $class, false];
            return $this;
        }

        /**
         * @param \THtml_Input_Button $button
         *
         * @return $this
         */
        public function addActionButton(THtml_Input_Button $button){
            $this->_actions[] = [false, false, false, $button];
            return $this;
        }

        /**
         * @param bool $g
         *
         * @return $this
         */
        public function setActionsGrouped($g){
            $this->_actionsGrouped = $g;
            return $this;
        }

        /**
         * @param string $js
         *
         * @return $this
         */
        public function setLineAction($js){
            $this->_lineAction = $js;
            return $this;
        }

        /**
         * @param callable $func
         *
         * @return $this
         */
        public function setLineClassFunction($func){
            $this->_line_class_handler = $func;
            return $this;
        }

        /**
         * @param callable $func
         *
         * @return $this
         */
        public function setLineOptionsFunction($func){
            $this->_line_options_handler = $func;
            return $this;
        }

        /**
         * @param string $action
         *
         * @return $this
         */
        public function setCustomHeaderAction($action){
            $this->_custom_action = $action;
            return $this;
        }

        /**
         * @param \TDB_Model $model
         *
         * @return $this
         */
        public function setModel(TDB_Model $model){
            $this->_model = $model;
            return $this;
        }

        /**
         * Construct table cells and row from model list
         */
        private function _handleModel(){
            $actualData = $this->_lines;
            $this->_lines = [];

            if($this->_model){
                if($this->_sorted && $this->_sortBy["key"])
                    $this->_model->setOrder($this->_sortBy["key"]." ".$this->_sortBy["order"]);

                $data = $this->_model->getList();

                /**
                 * @var TDB_Model $e
                 */
                foreach($data as $e){
                    /*
                     * Construct cells from header
                     */
                    foreach($this->_headers as list($label, $options)){
                        $cell_content = "";
                        $cell_options = [];

                        if(isset($options["list_data"])){
                            if($options["list_data"] != "THIS")
                                $cell_content = $e->{$options['list_data']};
                            else
                                $cell_content = "THIS";

                            // Cell format handle
                            if(isset($options["cell_format"]) && $options["cell_format"]){
                                $args = [];
                                if(isset($options["format_args"]))
                                    $args = $options["format_args"];

                                if($cell_content == "THIS")
                                    $cell_content = $e;

                                $cell_content = call_user_func_array(array($options["cell_format"], "format"), [$cell_content, $args]);
                                $style        = call_user_func_array(array($options["cell_format"], "getStyle"), [$cell_content, $args]);

                                if($style)
                                    $cell_options["style"] = $style;
                            }

                            // data_mask handle
                            if(isset($options["data_mask"]) && isset($options["data_mask"][$cell_content]))
                                $cell_content = $options["data_mask"][$cell_content];

                            // Cell options handle
                            if(isset($options["cell_options"]) && is_array($options["cell_options"]))
                                $cell_options = array_merge($cell_options, $options["cell_options"]);
                        }

                        $this->addCell($cell_content, $cell_options);
                    }

                    /*
                     * MassAction add
                     */
                    if(count($this->_massAction))
                        $this->addCell("<input type=\"checkbox\" name=\"elements[".$e->getId()."]\" value=\"".$e->getId()."\" />", ["class" => "massAction_check"]);

                    /*
                     * Add the line to table lines
                     */
                    $line_options = array();

                    /**
                     * @var callable $options_func
                     */
                    $options_func = $this->_line_options_handler;
                    if($options_func && is_callable($options_func))
                        $line_options = $options_func($e);

                    if($this->_lineAction && !empty($this->_lineAction)){
                        $action = $this->_lineAction;
                        $matches = array();
                        preg_match_all(TFW_Registry::VARIABLE_REGEX, $action, $matches);

                        if(count($matches) >= 2){
                            foreach($matches[0] as $ko => $match)
                                $action = str_replace($match, $e->{$matches[1][$ko]}, $action);
                        }

                        $line_options["onclick"] = $action;
                    }

                    /**
                     * @var callable $class_func
                     */
                    $class_func = $this->_line_class_handler;
                    if($class_func && is_callable($class_func))
                        $line_options["class"] = $class_func($e);

                    $this->addCurrentLine($line_options);
                }
            }

            $this->_lines = array_merge($this->_lines, $actualData);
        }

        /**
         * @return string
         */
        public function get(){
            if($this->_model)
                $this->_handleModel();

            $html = "<table".$this->_getAttributes().">";

            if(count($this->_massAction))
                $this->pushHeader("<input type=\"checkbox\" class=\"master-checkbox\" />", ["class" => "massAction_check"]);

            /*
             *  Header
             */
            $html .= "<thead>";

            // Title line
            if($this->_title)
                $html .= "<tr><th colspan=\"".count($this->_headers)."\"><h2 class=\"table-header-title\">$this->_title</h2></th></tr>";

            // Actions line
            if(count($this->_actions) || count($this->_massAction) || $this->_custom_action){
                $html .= "<tr><th colspan=\"".count($this->_headers)."\">";
                if($this->_actionsGrouped)
                    $html .= "<div class=\"btn-group\" role=\"group\">";
                foreach($this->_actions as list($label, $js, $class, $button)){
                    if($label)
                        $html .= "<button type=\"button\" class=\"btn btn-sm btn-$class\" onclick=\"$js\">$label</button> ";
                    else if($button)
                        $html .= $button->get();
                }
                if($this->_actionsGrouped)
                    $html .= "</div>";

                // Mass action case
                if(count($this->_massAction)){
                    $dropdown = new THtml_Input_Button_Dropdown();
                    $dropdown
                        ->addClass("pull-right")
                        ->setValue($this->__("For selection"));
                    foreach($this->_massAction as list($label, $route))
                        $dropdown->addElement($label, 'Table.massAction(\''.$route.'\', \''.$this->getId().'\');');
                    $html .= $dropdown->get();
                }

                if($this->_custom_action){
                    $div = new THtml_Div();
                    $html .= $div
                        ->addClass("pull-right")
                        ->setContent($this->_custom_action)
                        ->get();
                }

                $html .= "</th></tr>";
            }

            // Headers line
            $html .= "<tr>";
            foreach($this->_headers as list($label, $options)){
                // Sort data
                if($this->_sorted && $this->_model && isset($options["list_data"])){
                    if($this->_sortBy["key"] == $options["list_data"]){
                        $sort_icon = " <span class=\"pull-right glyphicon glyphicon-menu-"
                            .($this->_sortBy["order"] == "DESC" ? "down" : "up")
                            ."\" aria-hidden=\"true\"></span>";
                    }
                    else
                        $sort_icon = " <span class=\"glyphicon glyphicon-menu-up hidden pull-right\" aria-hidden=\"true\"></span>";

                    $label .= $sort_icon;
                    $options["onclick"] = 'Table.sort(\''.$options["list_data"].'\', \''
                        .($this->_sortBy["key"] == $options["list_data"] && $this->_sortBy["order"] == "ASC" ? "DESC" : "ASC")
                        .'\', \''.$this->_id.'\');';

                    if(!isset($options["class"]))
                        $options["class"] = "";

                    $options["class"] .= "sort "
                        .($this->_sortBy["key"] == $options["list_data"] ? "sort_active" : "");
                }

                $opt = "";
                foreach($options as $ok => $ov){
                    if(!in_array($ok, self::$_options_keyword))
                        $opt .= " $ok=\"$ov\"";
                }

                $html .= "<th$opt>$label</th>";
            }
            $html .= "</tr>";

            // Filter line
            if(count($this->_filters)){
                $html .= "<tr>";
                foreach($this->_headers as $position => $h){
                    if(isset($this->_filters[$position])){
                        /**
                         * @var THtml_Table_Filter $filter
                         */
                        $filter = $this->_filters[$position];
                        $filter->getElement()->addAttribute("onchange", "$('#table_form_".$this->getId()."').submit()");
                        $html .= "<th class='filter'>".$filter->get()."</th>";
                    }
                    else
                        $html .= "<th class='filter'></th>";
                }
                $html .= "</tr>";
            }

            $html .= "</thead>";

            /*
             *  Lines
             */
            $html .= "<tbody>";
            foreach($this->_lines as list($cells, $line_options)){
                $lopt = "";
                foreach($line_options as $ok => $ov)
                    $lopt .= " $ok=\"$ov\"";
                $html .= "<tr$lopt>";
                foreach($cells as list($value, $cell_options)){
                    $copt = "";
                    foreach($cell_options as $ok => $ov)
                        $copt .= " $ok=\"$ov\"";
                    $html .= "<td$copt>$value</td>";
                }
                $html .= "</tr>";
            }
            $html .= "</tbody>";

            /*
             *  TODO : Footer
             */

            $html .= "</table>";

            /*
             * Form if needed
             */
            if(count($this->_filters) || $this->_sorted || count($this->_massAction)){
                $hidden = "<input type=\"hidden\" name=\"table_form_trigger\" value=\"$this->_key\" />";

                if($this->_sorted){
                    $hidden .= "<input type=\"hidden\" id=\"table_sort_key_".$this->getId()."\" name=\"sort:key\" value=\"\" />";
                    $hidden .= "<input type=\"hidden\" id=\"table_sort_order_".$this->getId()."\" name=\"sort:order\" value=\"\" />";
                }

                $html = "<form id=\"table_form_".$this->getId()."\" method=\"POST\" action=\"\">"
                    .$hidden
                    .$html
                    ."</form>";
            }

            return $html;
        }
    }