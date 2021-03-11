<?php     

    echo "$page<br /><br />";

    //var_dump($_POST);////

    // Handle logging in
    $user = $_POST["username"] ?? false;

    // Main 
    if ( $user !== false ) { // User exists and is logged in
        
        echo "Logged in as: $user.";
        
        //$output = shell_exec('ls -la');
        
        //$output = exec('USER=central ../BlockchainDecentralisation/gossip.sh &');
        
        //echo "<pre>$output</pre>";
        
        
        
        
        echo '
            <input type=text" id="user" style="display: none;" value="' . $user . '" />
            
            <div style="display: flex; flex-wrap: wrap;">
                <div style="width: 48%; margin: 1%;">
                    <h1>View SN</h1>
                    
                    <label for="sn">Serial number: </label>
                    <input type="text" name="" id="sn" placeholder="sn" />
                    <br />
                    
                    <div style="text-align: center;">
                        <div class="btn btn-info moveLeft" onclick="searchSn()">
                            Search
                        </div>
                    </div>
                </div>

                <div style="width: 48%; margin: 1%;">
                    <h1>Add Transaction</h1>
                    
                    <label for="snAdd">SN: </label>
                    <input type="text" name="snAdd" id="snAdd" placeholder="Serial Number" />
                    <br />
                    
                    <label for="transactionAdd">Action: </label>
                    <input type="text" name="" id="transactionAdd" placeholder="Transaction" />
                    <br />
                    
                    <div style="text-align: center;">
                        <div class="btn btn-info moveLeft" onclick="addTransaction()">
                            Add transaction
                        </div>
                    </div>
                </div>
                
                <div style="width: 48%; margin: 1%;">
                    <h1>SN Results</h1>
                    
                    <div id="snResults">none</div>
                </div>

                <div style="width: 48%; margin: 1%;">
                    <h1>Creating new transaction</h1>
                    
                    <div id="addTransaction">none</div>
                </div>
            </div>
        ';
        
    }
