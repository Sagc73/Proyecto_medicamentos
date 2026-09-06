<?php
    session_start();
    require_once 'medicines_db.php';

    if(!isset($_SESSION['user_id'])){
        header('Location: login.php');
        exit;
    }

    $id_med = $_GET['id_med'] ?? '';

    if(empty($id_med)){
        header('Location: index.php');
        exit;
    }

    try{
        $db = new Database();
        $conn = $db->connect();
        $stmt = $conn->prepare("DELETE FROM medicamentos WHERE id_med = ?");
        $stmt->execute([$id_med]);
        header('Location: DashBoard.php?msg=Medicamento eliminado con éxito.!');
        exit;
    }catch(PDOException $e){
        header('Location: index.php?msg=Error en delete'. $e->getMessage());
        exit;
    }
?>
