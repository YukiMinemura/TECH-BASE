<?php
	//4-1データベース連携
	$dsn ='データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	
	//4-2テーブル作成
	$sql = "CREATE TABLE IF NOT EXISTS mission_5"
	." ("
	."id INT AUTO_INCREMENT PRIMARY KEY,"
	."name char(32),"
	."comment TEXT,"
	."date DATETIME,"
	."pass TEXT"
	." );";
	$stmt = $pdo -> query($sql);
?>
<?php
	//編集処理
	$edit_num      = "";
	$edit_name    = "名前";
	$edit_comment = "コメント";
	
	if ( !(empty($_POST["edit_num"]) || empty($_POST["pass"]))  ) {
		global $pdo;
		
		$sql = 'SELECT * FROM mission_5 where id=:id AND pass = :pass';
		$stmt = $pdo->prepare($sql);
		
		$stmt->bindParam(':id',  $id,  PDO::PARAM_INT);
		$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
		
		$id  = $_POST["edit_num"];
		$pass = $_POST["edit_pass"];
		
		$stmt  -> execute();
		$Data   = $stmt-> fetch();
		
		if (isset($Data)){
			$edit_No      = $_POST["edit_num"];
			if ( isset($Data["name"]) && isset($Data["comment"]) ) {
				$edit_name    = $Data["name"];
				$edit_comment = $Data["comment"];
			}
		}
	}
?>

<!DOQTYPE html>
<html>
	<head>
		<meta charset = "utf-8">
	</head>
	<body>
<!--mission3-1のフォームづくり-->


		<p>投稿フォーム</p>
		<form action = "mission_3-5.php" method = "post">
			<p>名前:</p> <input type = "text" name = "name" value = "<?php echo $edit_name; ?>"><br>
			<p>コメント</p> <input type = "text" name = "comment" value = "<?php echo $edit_comment; ?>"><br>
			<p>pass word</p> <input type = "password" name = "pass" required><br>
			<!--隠し入力フォームに編集番号を入れる-->
			<input type = "hidden" name = "edited_num" value = "<?php echo $edit_num; ?>">
			<input type = "submit" value = "送信">
		</form>

		<p>削除フォーム</p>
		<form action = "mission_3-5.php" method = "post">
			<p>削除対象番号</p> <input type = "text" name = "delete"><br>
			<p>パスワード</p> <input type = "password" name = "del_pass" required><br>
			<input type = "submit" value = "削除"><br>
		</form>
		
		<p>編集用番号指定フォーム</p>
		<form action = "mission_3-5.php" method = "post">
			<p>編集対象番号</p> <input type = "text" name = "edit">
			<p>パスワード</p> <input type = "password" name = "edit_pass" required>
			<input type = "submit" value = "編集">
		</form>
	</body>
</html>
<?php
	//編集
	if(!empty($_POST["edited_num"])) {
		edit();
	}
	
	//新規送信
	elseif(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])) {
		send();
	}
	
	//削除
	elseif(!empty($_POST["delete"]) && !empty($_POST["del_pass"])) {
		delete();
	}
	
	//表示
	display();
?>

<?php
	//編集part
	function edit() {
		
		global $pdo;
		$id = $_POST["edited_num"];
		$name = $_POST["name"];
		$comment = $_POST["comment"];
		$sql = 'update mission_5 set name=:name,comment=:comment where id=:id';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':name', $name, PDO::PARAM_STR);
			$stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt -> bindParam(':id', $id, PDO::PARAM_INT);
			$stmt -> execute();
	}
	
	//新規送信part
	function send() {
		
		global $pdo;
		$sql = $pdo -> prepare("INSERT INTO mission_5 (name,comment,date,pass) VALUES (:name,:comment,:date,:pass)");
			$stmt -> bindParam(':name', $name, PDO::PARAM_STR);
			$stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt -> bindParam(':date', $date, PDO::PARAM_INT);
			$stmt -> bindParam(':pass', $pass, PDO::PARAM_INT);
			
			$name = $_POST["name"];
			$comment = $_POST["comment"];
			$date = date("Y/m/d H:i:s");
			$pass = $_POST["pass"];
		$sql -> execute();
	}
	
	//削除part
	function delete() {
		
		global $pdo;
		$id =  $_POST["delete"];
		$del_pass = $_POST["del_pass"];
		$sql = 'SELECT * FROM mission_5';
		$stmt = $pdo -> query($sql);
		$results = $stmt -> fetchAll();
		foreach ($results as $row) {
			if($id===$row['id'] && $del_pass===$row['pass']) {
				$sql = 'delete from mission_5 where id=:id';
				$stmt = $pdo -> prepare($sql);
				$stmt -> bindParam(':id', $id, PDO::PARAM_INT);
				$stmt -> execute();
			}else{
			echo "パスワードと投稿番号が一致しません";
			}
		}
	}
	
	//表示part
	function display() {
	
		global $pdo;
		$sql = 'SELECT * FROM mission_5';
		$stmt = $pdo -> query($sql);
		$results = $stmt -> fetchAll();
		foreach ($results as $row) {
			echo $row['id'] . $row['name'] . $row['comment'] . $row['date'] . "<br>";
		}
	}
?>