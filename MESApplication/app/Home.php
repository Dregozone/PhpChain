<?php 

    // Handle logging in
    $user = $_POST["username"] ?? false;

    // Main 
    if ( $user !== false ) { // User exists and is logged in
        
        echo "
            <div class=\"loggedInAs\">
                Logged in as: $user.
            </div>
        ";
        
        echo '
            <h1>
                PhpChain MES
            </h1>
        ';
        
        echo '
            <input type=text" id="user" style="display: none;" value="' . $user . '" />
            
            <div style="display: flex; flex-wrap: wrap;">
                <div style="width: 48%; margin: 1%;">
                    <h2>View SN</h2>
                    
                    <label for="sn">Serial number: </label>
                    <input type="text" class="form-control homeInput" name="sn" id="sn" placeholder="SN" />
                    
                    <br />
                    
                    <div style="text-align: center;">
                        <div class="btn btn-info moveLeft" onclick="searchSn()">
                            Search
                        </div>
                    </div>
                </div>

                <div style="width: 48%; margin: 1%;">
                    <h2>Add Transaction</h2>
                    
                    <label for="snAdd">SN: </label>
                    <input type="text" class="form-control homeInput" name="snAdd" id="snAdd" placeholder="Serial Number" />
                    
                    <br />
                    
                    <label for="transactionAdd">Action: </label>
                    <input type="text" class="form-control homeInput" name="" id="transactionAdd" placeholder="Transaction" />
                    
                    <br />
                    
                    <div style="text-align: center;">
                        <div class="btn btn-info moveLeft" onclick="addTransaction()">
                            Add transaction
                        </div>
                    </div>
                </div>
                
                <div style="width: 48%; margin: 1%;">
                    <h2>SN Results</h2>
                    
                    <div id="snResults"></div>
                </div>

                <div style="width: 48%; margin: 1%;">
                    <h2>Creating new transaction</h2>
                    
                    <div id="addTransaction"></div>
                </div>
            </div>
        ';
        
    }
