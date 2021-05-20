<?php 

    namespace app\View;

    Class AppView 
    {

        public function loggedInAs($user) {

            $html = "
                <div class=\"loggedInAs\">
                    Logged in as: $user.
    
                    <a href=\"?p=login&logout=1\">
                        <img src=\"SampleMES/public/img/logout.png\" style=\"width: 2vmin;\" title=\"Logout\" alt=\"Logout\" />
                    </a>
                </div>
            ";

            return $html;
        }

        public function title($title) {

            $html = '
                <h1>
                    ' . $title . '
                </h1>
            ';

            return $html;
        }

        public function startContainer($user) {

            $html = '
                <input type=text" id="user" style="display: none;" value="' . $user . '" />
            
                <div style="display: flex; flex-direction: column; flex-wrap: wrap;">
            ';

            return $html;
        }

        public function endContainer() {

            $html = '
                </div>
            ';

            return $html;
        }

        public function startNav() {
            
            return '<nav>';
        }

        public function endNav() {

            return '</nav>';
        }

        public function button($text, $link) {

            $html = '
                <a href="?p=' . $link . '">
                    <div class="btn btn-info">
                        ' . $text . '
                    </div>
                </a>
            ';

            return $html;
        }

    }
