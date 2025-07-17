<?php

if(isset($_POST['login'])){
    $aname = $_POST['aname'];
    $apass = $_POST['apass'];

    $qry = "SELECT * FROM alogin WHERE aname='$aname' AND apass='$apass'";
    $run= mysqli_query($con,$qry);
    $row = mysqli_num_rows($run);
    if($row < 1){
        ?>
        <script>
            alert('Invalid admin name or password....');
            window.open('admin.php','_self');
        </script>
        <?php
    }
    else{
        header('location:admin/dashboard.php');
    }
}

?>