<?php 
    
    namespace app\View;

    Class Login 
    {
        private $model;
        private $controller;
        
        public function __construct($model, $controller) {
            $this->model = $model;
            $this->controller = $controller;
        }
        
        public function loginArea() {
            
            $html = '
                <div class="loginArea">
                    <form action="SampleMES?p=Login" method="POST" autocomplete="OFF">
                        <fieldset>
                            <legend>Login</legend>
                            
                            <input type="hidden" name="action" value="login" aria-label="Action selector" />

                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username" placeholder="Username" value="' . getenv('USER') . '" />
                            
                            <label for="sk">Scan private key:</label>
                            <input type="password" name="sk" id="sk" placeholder="Private key" />
                        
                            <input type="submit" value="Login" aria-label="Login button" />
                        </fieldset>
                    </form>
                </div>
            ';
            
            return $html;
        }
    }
