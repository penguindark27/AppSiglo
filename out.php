<?
        session_start();
        unset($_SESSION);
        session_destroy();
        echo "<script>top.location='/AppSiglo/login.php';</script>";
?>



