<?php
class User {
    private $userId;
    private $name;
    private $phone;
    
    private $messages;
    
    public function __construct($userId, $name, $phone)
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->phone = $phone;
        
        $this->messages = array();
    }
    
    public function getUserId() {
        return $this->userId;
    }

    public function getName() {
        return $this->name;
    }

    public function getPhone() {
        return $this->phone;
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

