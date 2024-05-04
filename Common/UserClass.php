<?php

class User {
    private $userid;
    private $name;
    private $phone;
    private $password;
    
    public function __construct($userid, $name, $phone, $password) {
        $this->userid = $userid;
        $this->name = $name;
        $this->phone = $phone;
        $this->password = sha1($password);
    }

    public function save(){
        $result = "";
        $link = connect();
        if ($link){
            $query = "INSERT INTO user VALUES(?, ?, ?, ?)";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'ssss', $this->userid, $this->name, $this->phone, $this->password);
            if (!mysqli_stmt_execute($stmt)){
                $result = "The system is not available, try again later.";
            }
        }
        else {
            $result = "The system is not available, try again later.";
        }
        close($link);
        return $result;
    }
    
}
