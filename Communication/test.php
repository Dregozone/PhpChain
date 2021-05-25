<?php 

    require_once("classes/Block.php");
    require_once("classes/Blockchain.php");

    // Set up blockchain
    $blockchain = new Blockchain( ["message" => "first block."] );

    // Add to blockchain
    $blockchain->addBlock( new Block( ["message" => "second block."] ) );

    echo "<hr /><pre>";
    
    foreach ( $blockchain->getBlockchain() as $block ) {
        
        //var_dump( $block->getInfo() );
        var_dump( $block->getData() );

    }

    echo "</pre><hr />";

    // Check that the hashes match etc... 
    var_dump( $blockchain->isValid() );
