<?php 

    Class Transaction 
    {
        public $data = [];

        private $who;
        private $what;
        private $when;
        private $why;
        private $where;
        private $how;

        public function __construct($who, $what, $when, $why, $where, $how) {
            $this->who = $who;
            $this->what = $what;
            $this->when = $when;
            $this->why = $why;
            $this->where = $where;
            $this->how = $how;

            $this->data = [
                "Who" => $this->who,
                "What" => $this->what,
                "When" => $this->when,
                "Why" => $this->why,
                "Where" => $this->where,
                "How" => $this->how
            ];
        }

        public function getData() {

            return $this->data;
        }
    }

    Class Block 
    {
        private $sequence;
        private $datetime;
        private $data;
        private $prevHash;
        private $curHash;

        public function __construct($data, $prevHash = '') {
            
            $this->datetime = $this->getDateTime();
            $this->data = $data;
            $this->prevHash = $prevHash;
            $this->curHash = $this->calculateHash();
        }

        private function getDatetime() {

            $now = new \Datetime();
            
            return $now->format("Y-m-d H:i:s");
        }

        public function calculateHash() {
            
            return hash("sha256", $this->sequence . $this->datetime . $this->data . $this->prevHash);
        }

        public function setPrevHash($prevHash) {
            $this->prevHash = $prevHash;
        }

        public function setCurHash($curHash) {
            $this->curHash = $curHash;
        }

        public function setSequence($sequence) {
            $this->sequence = $sequence;
        }

        public function getPrevHash() {

            return $this->prevHash;
        }

        public function getCurHash() {
            
            return $this->curHash;
        }
    }

    Class Blockchain 
    {
        private $data;
        private $blockchain = []; // Array of blocks becomes the blockchain

        public function __construct(string $data) {
            $this->data = $data;
            $this->blockchain[] = $this->createGenesisBlock();
        }

        private function createGenesisBlock() { // Initialisation
            
            $genesis = new Block($this->data, "0");
            $genesis->setSequence(0);

            return $genesis;
        }

        public function addBlock(Block $block) {
            
            $block->setPrevHash($this->getLastBlock()->getCurHash());
            $block->setCurHash($block->calculateHash());

            $block->setSequence( sizeof( $this->blockchain ) );

            $this->blockchain[] = $block;
        }

        public function isValid() {

            for ( $i=1, $j=sizeof($this->blockchain); $i<$j; $i++ ) {
                
                $curBlock = $this->blockchain[$i];
                $prevBlock = $this->blockchain[$i-1];

                // Check that the current hash is valid to the values within the block
                if ( $curBlock->getCurHash() !== $curBlock->calculateHash() ) {

                    return false;
                }

                // Check that the chain is correctly sequenced by comparing previous hash with the previous blocks current hash
                if ( $curBlock->getPrevHash() !== $prevBlock->getCurHash() ) {
                    
                    return false;
                }
            }

            return true;
        }

        public function getLastBlock() {
            
            return $this->blockchain[(sizeof($this->blockchain)-1)];
        }

        public function getBlockchain() {

            return $this->blockchain;
        }
    }

    Class Handler 
    {
        private $user;
        private $file;
        private $sns = [];

        public function __construct($user) {

            $this->user = $user;
            $this->file = __DIR__ . '/data/' . $user . '.json';
        }

        public function saveToFile() {
            file_put_contents($this->file, json_encode($this->sns));
        }

        public function loadFromFile() {

            if ( file_exists($this->file) ) {
                $this->sns = json_decode(file_get_contents($this->file), true);
            } else {
                $this->sns = [];
            }
        }

        public function addTransaction($sn, $action) {

            // This is the first time the SN is transacted against
            if ( !array_key_exists($sn, $this->sns) ) {
                $this->sns[$sn] = serialize( new Blockchain( $action ) );
            } else {
                $this->sns[$sn] = unserialize($this->sns[$sn]);
                $this->sns[$sn]->addBlock( new Block( $action ) );
                $this->sns[$sn] = serialize($this->sns[$sn]);
            }

            return true;
        }

        public function setSns($sns) {
            $this->sns = $sns;
        }

        public function getSns() {

            return $this->sns;
        }

        public function getSn($sn) {
            
            return unserialize( $this->sns[$sn] );
        }
    }

    //echo "Is valid? ";
    //var_dump( $sn["001"]->isValid() );

    //var_dump( $sns );

    /** addTransaction.php = php addTransaction.php SN001 emma Initialisation
     *  Get current array $sn = Handler::loadFromFile();
     *  Search for SN
     *  If missing, initialise into new blockchain with genesis block of Transaction
     *  Else, addBlock(Transaction)
     *  Save back to file and gossip Handler::saveToFile(); (There will never be 2 transactions added to the same blockchain at the same time)
     *  Display confirmation message
     */

    /** viewSn.php = php viewSn.php SN001
     *  Get current array $sn = Handler::loadFromFile();
     *  Search for SN
     *  If found and valid, dump the SN details
     *  Else, display error message
     * 
     */

    $handler = new Handler("anders"); // Logged in user

    $handler->loadFromFile(); // Pull in this users latest values

        $handler->addTransaction("SN001", "AOI"); // SN, What

    $handler->saveToFile(); // Save back to file for gossiping

    var_dump( $handler->getSn("SN002") );
