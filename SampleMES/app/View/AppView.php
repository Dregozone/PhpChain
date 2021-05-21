<?php 

    namespace app\View;

    Class AppView 
    {

        public function loggedInAs($user) {

            $html = "
                <div class=\"loggedInAs\">
                    Logged in as: $user.
    
                    <a href=\"?p=login&action=logout\">
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

        public function startMain() {

            return '<main>';
        }

        public function endMain() {

            return '</main>';
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

        public function notices() {

            if ( sizeof( $this->model->getNotices() ) == 0 ) { // Nothing to display

                return;
            }

            $html = '
                <div class="notices">
                    <h2>Notices:</h2>
            ';

            foreach ( $this->model->getNotices() as $msg ) {
                $html .= '
                    <div class="notice">
                        ' . $msg . '
                    </div>
                ';
            }

            $html .= '
                </div>
            ';

            return $html;
        }

        public function errors() {

            if ( sizeof( $this->model->getErrors() ) == 0 ) { // Nothing to display

                return;
            }

            $html = '
                <div class="errors">
                    <h2>Errors:</h2>
            ';

            foreach ( $this->model->getErrors() as $msg ) {
                $html .= '
                    <div class="error">
                        ' . $msg . '
                    </div>
                ';
            }

            $html .= '
                </div>
            ';

            return $html;
        }

        public function warnings() {

            if ( sizeof( $this->model->getWarnings() ) == 0 ) { // Nothing to display

                return;
            }

            $html = '
                <div class="warnings">
                    <h2>Warnings:</h2>
            ';

            foreach ( $this->model->getWarnings() as $msg ) {
                $html .= '
                    <div class="warning">
                        ' . $msg . '
                    </div>
                ';
            }

            $html .= '
                </div>
            ';

            return $html;
        }

    }
