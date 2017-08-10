<?php
require_once 'API.class.php';
require_once 'Models/ApiKey.class.php';
require_once 'Models/User.class.php';
class MyAPI extends API
{
    protected $User;

    public function __construct($request, $origin) {
        parent::__construct($request);

        //NOTE: Removed for simplicity. Will add in later version if necessary. 
        // // Abstracted out for example
        // $APIKey = new ApiKey();
        // $User = new User();

        // // print_r($this->request);
        // // var_dump($_SERVER);
        // // var_dump(file_get_contents('php://input'));

        // if (!array_key_exists('apiKey', $this->request)) {
        //     throw new Exception('No API Key provided');
        // } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
        //     throw new Exception('Invalid API Key');
        // } else if (array_key_exists('token', $this->request) &&
        //      !$User->get('token', $this->request['token'])) {

        //     throw new Exception('Invalid User Token');
        // }

        // $this->User = $User;
        $this->status = 200;
    }

    /**
     * Example of an Endpoint
     */
     protected function example() {
        if ($this->method == 'GET') {
            return "Your name is " . $this->User->name;
        } else {
            return "Only accepts GET requests";
        }
     }

    /*
    GET api/v1/generate/

    @return string swearbird
    */
     protected function generate() {
        $this->checkMethod("GET");
        
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);

        //get table counts
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM nouns WHERE archived != 1) AS nouns,
                    (SELECT COUNT(*) FROM cont_verbs WHERE archived != 1) AS cont_verbs,
                    (SELECT COUNT(*) FROM er_nouns WHERE archived != 1) AS er_nouns
                FROM dual";
        
        $st = $conn->prepare ($sql) or die($st->errorInfo());  
        $st->execute() or die(print_r($st->errorInfo()));

        if ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $counts = $row;
        } else {
            throw new Exception("Error Processing Request", 1);
        }

        //Generate randoms ids
        $rand_noun_1 = rand(1, $counts["nouns"]);
        $rand_noun_2 = rand(1, $counts["nouns"]);
        $rand_cont_verb = rand(1, $counts["cont_verbs"]);
        $rand_er_noun = rand(1, $counts["er_nouns"]);

        //get 'random' words from DB while none of the values are null -- TODO: Ehhh... not very efficient. Rework
        $words = array(
            "noun1" => "notempty", 
            "noun2" => "notempty", 
            "cont_verb" => "notempty", 
            "er_noun" => "notempty"
        );
        $tries = 5;
        do {
            $tries--;

            //re-random values if null
            if ($words["noun1"] == null) { $rand_noun_1 = rand(1, $counts["nouns"]); }
            if ($words["noun2"] == null) { $rand_noun_2 = rand(1, $counts["nouns"]); }
            if ($words["cont_verb"] == null) { $rand_cont_verb = rand(1, $counts["cont_verbs"]); }
            if ($words["er_noun"] == null) { $rand_er_noun = rand(1, $counts["er_nouns"]); }

            $sql = "SELECT 
                        (SELECT word FROM nouns WHERE id = $rand_noun_1 AND archived != 1 LIMIT 1) AS noun1,
                        (SELECT word FROM cont_verbs WHERE id = $rand_cont_verb AND archived != 1 LIMIT 1) AS cont_verb,
                        (SELECT word FROM nouns WHERE id = $rand_noun_2 AND archived != 1 LIMIT 1) AS noun2,
                        (SELECT word FROM er_nouns WHERE id = $rand_er_noun AND archived != 1 LIMIT 1) AS er_noun
                    FROM dual";
            // echo $sql;
            $st = $conn->prepare ($sql) or die($st->errorInfo());  
            $st->execute() or die(print_r($st->errorInfo()));

            if ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                $words = $row;
            } else {
                throw new Exception("Error Processing Request", 1);
            }
        } while ($tries < 0 && ($words["noun1"] != null || 
            $words["noun2"] != null || 
            $words["cont_verb"] != null || 
            $words["er_noun"] != null));

        $conn = null;
        if ($this->verb == "plain")
            return $words["noun1"]." ".$words["cont_verb"]." ".$words["noun2"]." ".$words["er_noun"].".";
        return ($words);
        // return "Your name is " . $this->User->name;
        
     }

    /*
    POST api/v1/add/

    @param word - word to add
    @param table - table in which word appears

    @error string message
    @return obj
    */
     protected function add() {
        $this->checkMethod("POST");

        $data = $this->request;
        
        //TODO: Check if word belings in table {future}
        //Check if word is in DB
            //$this->status = 409
            // return $data["word"]." already exists in ".$data["table"];
        //add word to DB

        // return "Added " . $data["word"] . " to " . $data["table"];
        return array("id" => "x", "word" => $data["word"], "table" => $data["table"], "archived" => "0");
        // $str = print_r($this->request, true) . '; ';
        // $str.= $this->verb . '; ';
        // $str.= $this->file . '; ';
        // return ($str);

        // last ID $this->id = $conn->lastInsertId();
     }

    /*
    PUT api/v1/archive/

    @param word - word to archive
    @param table - table in which word appears

    @return string message
    */
     protected function archive() {
        $this->checkMethod("PUT");

        // $data = $this->request;
        $data = $this->file;

        //check if word exists and archive
        //

        return "Archived " . $data["word"] . " in " . $data["table"];
     }
 }
 ?>