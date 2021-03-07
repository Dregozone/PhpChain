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
                    <form action="?p=Home" method="POST" autocomplete="OFF">
                        <fieldset>
                            <legend>Login</legend>
                        
                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username" placeholder="Username" />
                            
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
