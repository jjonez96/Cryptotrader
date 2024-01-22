<?php
include_once 'config/db_config.php';
$conn = mysqli_connect($server, $user, $pswd, $db);

class Block {
    public $index;
    public $previousHash;
    public $timestamp;
    public $data;
    public $hash;
}

class Blockchain {
    private $chain = [];

    public function __construct() {
        $this->createGenesisBlock();
    }

    private function createGenesisBlock() {
        $genesisBlock = new Block();
        $genesisBlock->index = 0;
        $genesisBlock->previousHash = '0';
        $genesisBlock->timestamp = time();
        $genesisBlock->data = 'Genesis Block';
        $genesisBlock->hash = $this->calculateHash($genesisBlock);

        $this->chain[] = $genesisBlock;
    }

    private function calculateHash($block) {
        $data = $block->index . $block->previousHash . $block->timestamp . $block->data;
        return hash('sha256', $data);
    }

    public function addBlock($data) {
        $block = new Block();
        $block->index = end($this->chain)->index + 1;
        $block->previousHash = end($this->chain)->hash;
        $block->timestamp = time();
        $block->data = $data;
        $block->hash = $this->calculateHash($block);

        $this->chain[] = $block;
    }

    public function isValid() {
        for ($i = 1; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i - 1];

            // Check if the stored hash matches the calculated hash
            if ($currentBlock->hash !== $this->calculateHash($currentBlock)) {
                return false;
            }

            // Check if the previousHash matches the hash of the previous block
            if ($currentBlock->previousHash !== $previousBlock->hash) {
                return false;
            }
        }

        return true;
    }

    public function addToDatabase($sender, $receiver, $amount, $crypto) {
        $hash = $this->calculateHash(end($this->chain));

        $sql = "INSERT INTO transactions (sender, receiver, amount, timestamp, hash, crypto) VALUES ('$sender', '$receiver', $amount, NOW(), '$hash', '$crypto')";

        global $conn;

        if ($conn->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $sender = $_POST["sender"];
    $receiver = $_POST["receiver"];
    $amount = $_POST["amount"];
    $crypto = $_POST["crypto"];
    $timestamp = date("Y-m-d H:i:s");

    $blockchain = new Blockchain();

    // Check if the blockchain is valid
    if (!$blockchain->isValid()) {
        echo "Error: Blockchain is not valid.";
        exit();
    }
    
    // Add a block to the blockchain
    $blockchain->addBlock($timestamp . $sender . $amount . $crypto . $receiver);

    // Insert the transaction into the database
    if ($blockchain->addToDatabase($sender, $receiver, $amount, $crypto)) {
        echo "<center><h1>Transaction successful! Redirecting...</h1></center>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.html';
                }, 3000);
              </script>";
        exit(); 
    } else {
        echo "Error: Database insertion failed.";
    }
}
// Close the database connection
$conn->close();
?>
