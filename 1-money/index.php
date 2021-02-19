<?php 

    $db = 'data/balances.json';

    if (file_exists($db)) {
        $balances = json_decode(file_get_contents($db), true);
    } else {
        $balances = ['fabien' => 1000000];                  // Default user added on creation
        file_put_contents($db, json_encode($balances));     // Create .json file
    }

    $action = isset($_SERVER['PATH_INFO'])  ? strtolower($_SERVER['PATH_INFO']) : NULL;
    $user   = isset($_GET['user'])          ? strtolower($_GET['user'])         : NULL;
    $from   = isset($_GET['from'])          ? strtolower($_GET['from'])         : NULL;
    $to     = isset($_GET['to'])            ? strtolower($_GET['to'])           : NULL;
    $amount = isset($_GET['amount'])        ? strtolower($_GET['amount'])       : NULL;

    switch ($action) {
        case '/balance':    // Check balance of a user
            printf("User %s has %d fabcoins.", $user, $balances[$user] ?? 0);   //if is not set, default to 0
            break;

        case '/users':      // Create a new user
            if (array_key_exists($user, $balances)) {     // Check user doesnt already exist
                http_response_code(404);
                return;
            }
            $balances[$user] = 0; // Create new user with starting balance of 0
            file_put_contents($db, json_encode($balances)); // Update db
            print 'OK';
            break;

        case '/transfer':   // Transfer funds between users
            if (!array_key_exists($from, $balances)) {  // Check sender exists
                http_response_code(404);
                return;
            }

            if (!array_key_exists($to, $balances)) {  // Check receiver exists
                http_response_code(404);
                return;
            }

            if ($amount) {      // Check amount is set
                if ($amount > $balances[$from]) { // Check sender account has enough to send
                    http_response_code(404);
                    return;
                }

                //Checks completed, make the transfer
                $balances[$from]    -= $amount;
                $balances[$to]      += $amount;
                file_put_contents($db, json_encode($balances));
                print 'Transfer complete!';
            }
            break;

        case NULL:          // No command selected
            echo 'Select a command!';
            break;

        default:            // Illegal command selected
            die('Command not recognised!');
            break;
    };
