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
            return json_encode(["status"=>false,"error"=>["code"=>$this->database->dbc->errorInfo()[0],"message"=>$this->database->dbc->errorInfo()[2]?$this->database->dbc->errorInfo()[2]:"user not created"]]);
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
        return $result;
        
    }
    public function getUser_json($id)
    {
        $user = $this->getUser($id);
        if($user)
        {
            return json_encode(["status"=>true,"error"=>null,"user"=>[
                "id"=>$user["id"],
                "firstname"=>$user["firstname"],
                "lastname"=>$user["lastname"],
                "active"=>$user["active"],
                "role"=>$user["role"]
                ]
            ]);
        }
        else {
            return json_encode(["status"=>false,"error"=>["code"=>"1","message"=>"user with id ".strval($id)." not exists"]]);
        }
        
    }

    public function editUser($id, $firstname, $lastname, $active, $role)
    {
        if($this->getUser($id))
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

        else {
            return json_encode(["status"=>false,"error"=>["code"=>"1","message"=>"user with id ".strval($id)." not exists"]]);
        }
    }

    public function deleteUser($id)
    {
        if($this->getUser($id))
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
        else {
            return json_encode(["status"=>false,"error"=>["code"=>"1","message"=>"user with id ".strval($id)." not exists"]]);
        }
    }

    public function updateUserStatus( $status, $ids)
    {
        $status=(int)$status;
        $idsins=[];
        $wrongids=[];
        foreach($ids as $id)
        {
            if($this->getUser($id))
            {
               // $idsins[]=$id;
            }
            else 
            {
                $wrongids[]=$id;
            }
        }
        if (count($wrongids)>0)
        {
            return json_encode(["status"=>false,"error"=>["code"=>"3","message"=>"users with ids ".implode(",",$wrongids)." not exists"]]);
        }
        else {

        
            $sqlInsert = "update `users` set active=".$status." WHERE id in(".implode(',',$ids).");";      
            $result = $this->database->runQuery($sqlInsert);
            if($result)  {

                return json_encode(["status"=>true,"error"=>null,"active"=>$status,"ids"=>$ids]);
            }
            else {
                return json_encode(["status"=>false,"error"=>["code"=>$this->database->dbc->errorInfo()[0],"message"=>$this->database->dbc->errorInfo()[2]]]);
            }
        }
    }

    public function deleteUsers( $ids)
    {

        $idsins=[];
        $wrongids=[];
        foreach($ids as $id)
        {
            if($this->getUser($id))
            {
               // $idsins[]=$id;
            }
            else 
            {
                $wrongids[]=$id;
            }
        }
        if (count($wrongids)>0)
        {
            return json_encode(["status"=>false,"error"=>["code"=>"3","message"=>"users with ids ".implode(",",$wrongids)." not exists"]]);
        }
        else 
        {
            $sqlInsert = "delete from `users` WHERE id in(".implode(',',$ids).");";           
            $result = $this->database->runQuery($sqlInsert);
            if($result)  {

                return json_encode(["status"=>true,"error"=>null,"ids"=>$ids]);
            }
            else {
                return json_encode(["status"=>false,"error"=>["code"=>$this->database->dbc->errorInfo()[0],"message"=>$this->database->dbc->errorInfo()[2]]]);
            }
        }
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
    echo  $u->getUser_json((int)$id);
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

    echo $u->updateUserStatus($status, $ids);
}

if($_POST['action']=='deleteusers')
{
    $ids = $_POST['ids'];
    echo  $u->deleteUsers($ids);
}