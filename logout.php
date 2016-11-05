<?php
	session_start();
	session_destroy();
		//redirect('index.php');
		echo "<script>
				window.location.href = 'index.php';
			</script>";
		exit();
?>