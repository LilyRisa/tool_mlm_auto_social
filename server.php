<?php
header('Content-Type: application/json; charset=utf-8');

$servername = "localhost";
$datatable = "datatable";
$username = "username";
$password = "password";

$conn = mysqli_connect($servername, $username, $password, $datatable);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}


if(isset($_GET['type'])){
    switch($_GET['type']){
        case 'access_token': 
            echo json_encode(getaccess());
            break;
        case 'insert_value':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $page_id = !empty($_POST['page_id']) ? $_POST['page_id'] : null;
                $content = !empty($_POST['content']) ? $_POST['content'] : null;
                $type = !empty($_POST['type']) ? $_POST['type'] : null;
                if(!empty($page_id) && !empty($content) && !empty($type)){
                    if(insert_value($page_id, $content, $type)){
                        echo json_encode(['status' => true]);
                    }else{
                        echo json_encode(['status' => false]);
                    }
                }else{
                    echo json_encode(['status' => false]);
                }
                
            } else {
                echo json_encode(['status' => false, 'mess' => 'method post only']);
            }
            break;
        
        case 'get_value':
            $page_id = !empty($_POST['page_id']) ? $_POST['page_id'] : null;
            $type = !empty($_POST['type']) ? $_POST['type'] : null;
            $data = check_value($page_id, $type, true);
            if($data){
                echo json_encode($data);
            }else{
               echo json_encode([]); 
            }
            
            break;
            
        case 'update_access_token': 
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if(update_acctoken($_POST['access_token'])){
                    echo json_encode(['status' => true]);
                }else{
                    echo json_encode(['status' => false]);
                }
            }else{
                echo json_encode(['status' => false, 'mess' => 'method post only']);
            }
            break;
            
        default:
            echo json_encode(['status' => false, 'mess' => 'controller not found']);
            break;
    }
}else{
    echo json_encode(['status' => false, 'mess' => 'controller abstract not found']);
}
die();

function getaccess(){
    global $conn;
    $sql = "SELECT * FROM acctoken where id = 1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
        // Hiển thị mảng kết quả
        // return $data;
    } else {
        return null;
    }
}

function update_acctoken($acctoken){
    global $conn;
    $sql = "UPDATE acctoken SET content = '$acctoken' WHERE id = 1";
    if (mysqli_query($conn, $sql)) {
        return true;
    } 
    return false;
}

function check_value($page_id, $type = null, $return = false){
    global $conn;
    $sql = "SELECT * FROM save where page_id = '$page_id'";
    if($type){
        $sql .= " AND type='$type'";
    }
    $result = mysqli_query($conn, $sql);
    
    if($return){
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    return false;
    
}



function insert_value($page_id, $content, $type){
    global $conn;
    $check = check_value($page_id);
    $content = mysqli_real_escape_string($conn, $content);
    if(!$check){
        $sql = "INSERT INTO save (page_id, content, type) VALUES ('".$page_id."', '".$content."', '".$type."')";
        
    }else{
        $sql = "UPDATE save SET content = '$content', type = '$type' WHERE page_id = '$page_id'";
    }
    echo $sql;
    if (mysqli_query($conn, $sql)) {
        return true;
    } 
    return false;
    
}


die();