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
        $stmt->execute(array('',$firstname,$lastname,$active,$role));
        return $stmt;
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
        $stmt->execute(array($firstname,$lastname,$active,$role,$id));
        
        return json_encode((bool) $stmt);
    }

    public function deleteUser($id)
    {

        $stmt = $this->database->dbc->prepare("delete from users WHERE id=?");
        $stmt->execute(array($id));
        
        return json_encode((bool) $stmt);
    }

    public function updateUserStatus( $status, $ids)
    {
        $status=(int)$status;
        $sqlInsert = "update `users` set active=".$status." WHERE id in(".implode(',',$ids).");";              // Insert/Update/Delete Statements:
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
    $u->adduser($firstname, $lastname,$active,$role);
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