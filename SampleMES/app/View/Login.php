<?php 
    
    namespace app\View;

    Class Login extends AppView
    {
        protected $model;
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
                            <input class="form-control" type="text" name="username" id="username" placeholder="Username" value="' . getenv('USER') . '" />

                            <input class="btn btn-primary" type="submit" value="Login" aria-label="Login button" />
                        </fieldset>
                    </form>
                </div>

                <div style="border: 2px dashed darkorange; margin: 1% 25%; width: 50%; padding: 1%; font-size: 90%;">
                    <h3 style="margin: 0; padding: 0; font-size: 170%;">Logging in:</h3>
                    
                    <p>
                        <u><b>First time logging in:</b></u><br />
                            Enter username to join the application, this will generate and save a public/private key pair for you automatically.<br /> 
                            Keep these safe as they\'re used each time you need to verify your identity within the application.
                    </p>

                    <p>
                        <u><b>Returning user:</b></u><br />
                            Ensure you have your Private Key file available at "Communication/data/sk(user).json" for identity verification.<br />
                            Then enter your returning username for verified login to the application.
                    </p>

                </div>
            ';
            
            return $html;
        }
    }
