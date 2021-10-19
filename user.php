<?php
 require_once('settings.config.php'); 
 require_once('DBConnection.php'); 
class User {
    private $database;
    public function __construct($dbconfig)
    {
        $this->database = new DBConnection($dbconfig);
    }
    public function adduser($firstname, $lastname,$active,$role)
    {
        $stmt = $this->database->dbc->prepare("insert into users (`id`, `firstname`, `lastname`, `active`, `role`) values(?,?,?,?,?)");
        $result = $stmt->execute(array('',$firstname,$lastname,$active,$role));
        
        if($result)  {

            return json_encode(["status"=>true,"error"=>null,"user"=>[
                "id"=>$this->database->dbc->lastInsertId(),
                "firstname"=>$firstname,
                "lastname"=>$lastname,
                "active"=>$active,
                "role"=>$role
                ]
            ]);
        }
        else {
            return json_encode(["status"=>false,"error"=>["code"=>$this->database->dbc->errorInfo()[0],"message"=>$this->database->dbc->errorInfo()[2]]]);
        }
        
    }

    public function getUsers()
    {

        $stmt = $this->database->dbc->prepare("select * from users");
        $stmt->execute();
        $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
        
    }

    public function getUser($id)
    {

        $stmt = $this->database->dbc->prepare("select * from users WHERE id=?");
        $stmt->execute(array($id));
        $result= $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode($result);
        
    }

    public function editUser($id, $firstname, $lastname, $active, $role)
    {

        $stmt = $this->database->dbc->prepare("update users set firstname=?,lastname=?,active=?,role=? WHERE id=?");
        $result = $stmt->execute(array($firstname,$lastname,$active,$role,$id));
        
        if($result)  {

            return json_encode(["status"=>true,"error"=>null,"user"=>[
                "id"=>$id,
                "firstname"=>$firstname,
                "lastname"=>$lastname,
                "active"=>$active,
                "role"=>$role
                ]
            ]);
        }
        else {
            return json_encode(["status"=>false,"error"=>["code"=>$this->database->dbc->errorInfo()[0],"message"=>$this->database->dbc->errorInfo()[2]]]);
        }
    }

    public function deleteUser($id)
    {

        $stmt = $this->database->dbc->prepare("delete from users WHERE id=?");
        $result = $stmt->execute(array($id));
        
        if($result)  {

            return json_encode(["status"=>true,"error"=>null,"user"=>[
                "id"=>$id
                ]
            ]);
        }
        else {
            return json_encode(["status"=>false,"error"=>["code"=>$this->database->dbc->errorInfo()[0],"message"=>$this->database->dbc->errorInfo()[2]]]);
        }
    }

    public function updateUserStatus( $status, $ids)
    {
        $status=(int)$status;
        $sqlInsert = "update `users` set active=".$status." WHERE id in(".implode(',',$ids).");";      
        $count = $this->database->runQuery($sqlInsert);
        return $count;
    }

    public function deleteUsers( $ids)
    {

        $sqlInsert = "delete from `users` WHERE id in(".implode(',',$ids).");";           
        $count = $this->database->runQuery($sqlInsert);
        return $count;
    }
}

$u = new User($dbconfig);
if($_POST['action']=='adduser')
{
    $firstname=$_POST['firstname'];
    $lastname=$_POST['lastname'];
    $active=$_POST['active'];
    $role=$_POST['role'];
    echo $u->adduser($firstname, $lastname,$active,$role);
}

if($_POST['action']=='getusers')
{
  echo  $u->getUsers();
}

if($_POST['action']=='getuser')
{
    $id = $_POST['userid'];
    echo  $u->getUser((int)$id);
}

if($_POST['action']=='edituser')
{
    $id=$_POST['userid'];
    $firstname=$_POST['firstname'];
    $lastname=$_POST['lastname'];
    $active=$_POST['active'];
    $role=$_POST['role'];
    echo  $u->editUser($id,$firstname,$lastname,$active,$role);
}

if($_POST['action']=='deleteuser')
{
    $id = $_POST['userid'];
    echo  $u->deleteUser((int)$id);
}

if($_POST['action']=='changeuserstatus')
{
    $ids = $_POST['ids'];

    $status = $_POST['status'];
    echo json_encode($ids);
    echo  $u->updateUserStatus($status, $ids);
}

if($_POST['action']=='deleteusers')
{
    $ids = $_POST['ids'];
    echo json_encode($ids);
    echo  $u->deleteUsers($ids);
}