<?php 

    Class Handler 
    {
        private $user;
        private $file;
        private $sns = [];
        private $port = null;

        public function __construct($user) {

            $this->user = $user;
            $this->file = '../data/' . $user . '.json';

            if ( file_exists("../data/".$user.".port") ) { // If the user has been on the network before
                $this->port = (int)file_get_contents("../data/".$user.".port"); // Grab the port for this user
            } else {
                die("User not found!");
            }
        }

        public function saveToFile() {            
            file_put_contents($this->file, json_encode($this->sns));
        }

        public function loadFromFile() {

            if ( file_exists($this->file) ) {
                $this->sns = json_decode(file_get_contents($this->file), true);
            } else {
                $this->sns = [
                    $this->port => [
                        "user" => $this->user,
                        "session" => [],
                        "version" => 0
                    ]
                ];
            }
        }

        public function addTransaction($sn, $action) {
            
            // Convert legacy string values to array
            if (gettype($this->sns[$this->port]['session']) == "string") {
                $this->sns[$this->port]['session'] = [];
            }
            
            if ( array_key_exists($sn, $this->sns[$this->port]['session']) ) { // Check for this SN on my port
                
                // Found on MY port!
                $this->sns[$this->port]['session'][$sn] = unserialize($this->sns[$this->port]['session'][$sn]);
                $this->sns[$this->port]['session'][$sn]->addBlock( new Block( $action ) );
                $this->sns[$this->port]['session'][$sn] = serialize($this->sns[$this->port]['session'][$sn]);
                
            } else { 
                
                // Check for this SN on other ports that Im aware of
                foreach ( $this->sns as $port => $data ) {
                    
                    // Convert legacy string values to array
                    if (gettype($this->sns[$port]['session']) == "string") {
                        $this->sns[$port]['session'] = [];
                    }
                    
                    if ( array_key_exists($sn, $this->sns[$port]['session']) ) { // Check for this SN on my port
                
                        // Found on another port
                        // Change user and port to record this new transaction against
                        $this->user = $this->sns[$port]['user'];
                        $this->port = $port;
                        
                        $this->sns[$port]['session'][$sn] = unserialize($this->sns[$port]['session'][$sn]);
                        $this->sns[$port]['session'][$sn]->addBlock( new Block( $action ) );
                        $this->sns[$port]['session'][$sn] = serialize($this->sns[$port]['session'][$sn]);
                    
                        $this->sns[$this->port]['version']++;
                        
                        return true;
                    }                    
                }
                
                $this->sns[$this->port]['session'][$sn] = serialize( new Blockchain( $action ) );
            }

            $this->sns[$this->port]['version']++;
            
            return true;
        }

        public function showAllTransactions( Blockchain $blockchain ) {
            
            echo "<h2>Showing all transactions</h2>";
            
            foreach ( $blockchain->getBlockchain() as $block ) {
                
                echo "<br />Printing next...<br />";
                
                $this->outputBlock( $block );
            }
        }
        
        public function showLastTransaction( Blockchain $blockchain ) {
            
            echo "<h2>Showing last transaction</h2>";
            
            $this->outputBlock( $blockchain->getLastBlock() );
        }
        
        private function outputBlock(Block $block) {
            
            $blockInfo = $block->getInfo(); // When was the block added, who added the block, curHash, prevHash, ...
            $transactionInfo = $blockInfo["data"]->getData(); // Array of who, what, when, why, where, how
            
            echo '
                <table border="1">
            ';
            
            foreach ( $transactionInfo as $index => $value ) {
                echo '
                    <tr>
                        <th>' . $index . '</th>
                        <td>' . $value . '</td>
                    </tr>
                ';
            }
            
            echo '
                </table>
            ';
        }
        
        public function setSns($sns) {
            $this->sns[$this->port]['session'] = $sns;
        }

        public function getSns() {

            return $this->sns[$this->port]['session'];
        }

        public function getSn($sn) {
            
            // Convert legacy string values to array
            if (gettype($this->sns[$this->port]['session']) == "string") {
                $this->sns[$this->port]['session'] = [];
            }
            
            if ( array_key_exists($sn, $this->sns[$this->port]['session']) ) {
                
                return unserialize( $this->sns[$this->port]['session'][$sn] );
            } else {
                
                foreach ( $this->sns as $port => $data ) {
                    if ( is_array($data['session']) && array_key_exists($sn, $data['session']) ) {
                        
                        return unserialize( $data['session'][$sn] );
                    }
                }
                
                echo "SN not found!";
            }
        }
    }
