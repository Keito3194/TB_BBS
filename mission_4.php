<?php #データベース管理用

#データベース情報
$dsn = 'データベース名';
$user = 'ユーザ名';
$password = 'パスワード';
$pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

#テーブル情報
$sql = "CREATE TABLE IF NOT EXISTS keijibandb"
    ."("
    ."id INT AUTO_INCREMENT,"
    ."name char(32),"
    ."comment TEXT,"
    ."date datetime,"
    ."password char(30)"
    .");";
$stmt = $pdo->query($sql);

/*#テーブル情報表示確認用
$sql = 'SHOW CREATE TABLE keijibandb';
$result = $pdo -> query($sql);
foreach($result as $row){
    echo $row[1];
}
echo "<hr>";

#テーブル内容削除
$sql = 'TRUNCATE table keijibandb';
$result = $pdo -> query($sql);
#テーブル内容確認
/*$sql = 'SELECT * FROM keijibandb';
$result = $pdo -> query($sql);
foreach($result as $row){
    echo $row[0]." ";
    echo $row[1]." ";
    echo $row[2]." ";
    echo $row[3]." ";
    echo $row[4]."<br>";
}
echo "<hr>";*/
?>


<?php #掲示板管理用

#編集する投稿内容の取得 2-4
    if (isset($_POST['edit'])){
        $editno = $_POST['editno'];
        $login = $_POST['login_edi'];
        
        //空の場合
        if(empty($_POST['editno']) || empty($_POST['login_edi'])){
            $file = file($filename); //配列読み込み
            foreach($file as $lines){
                $output = explode("<>", $lines); //<>で分割
            }
        }

        //空でない場合
        else if (!empty($_POST['editno'])){    
            $file = file($filename);
            foreach($file as $lines){
                $output = explode("<>", $lines); //<>で分割
                if($output[0] == $editno){
                    if($output[4] == $login){
                        $edit_count = $output[0];
                        $edit_name = $output[1];
                        $edit_comment = $output[2];
                        $edit_password = $output[4];
                    }
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>簡易掲示板</title>
</head>
<body>
    <form action="mission_4.php" method="post">
        <table border="0">
            <tr>
                <td>　　　　名前：</td><td><input type="text" name="name" value="<?php echo $edit_name?>" placeholder="名前"></td>
            </tr>
            <tr>
                <td>　　コメント：</td><td><input type="text" name="comment" value="<?php echo $edit_comment?>" placeholder="コメント"></td>
            </tr>
            <tr>
                <td>　パスワード：</td><td><input type="password" name="password" value="<?php echo $edit_password?>" placeholder="パスワード"></td>
                <td><input type="submit" name="create" value="投稿"></td>
            </tr>
            <tr><td>　</td></tr>
            <tr>
                <td>削除対象番号：</td><td><input type="text" name="deleteno" value="<?php echo isset($_POST["deleteno"]) ? $_POST["deleteno"] : ''; ?>" placeholder="削除対象番号"></td>
            </tr>
            <tr>
                <td>　パスワード：</td><td><input type="password" name="login_del" value="" placeholder="パスワード"></td>
                <td><input type="submit" name="delete" value="削除"></td>
            </tr>
            <tr><td>　</td></tr>
            <tr>
                <td>編集対象番号：</td><td><input type="text" name="editno" value="<?php echo isset($_POST["editno"]) ? $_POST["editno"] : ''; ?>" placeholder="編集対象番号"></td>
            </tr>
            <tr>
                <td>　パスワード：</td><td><input type="password" name="login_edi" value="" placeholder="パスワード"></td>
                <td><input type="submit" name="edit" value="編集"></td>
                <td><input type="submit" name="editend" value="投稿"></td>
            </tr>
            <tr>
                <td>　　　　　　　</td><td><input type="hidden" name="edit2" value="<?php echo $edit_count?>" placeholder="編集番号"></td>
            </tr>
        </table>
    </form>
</body>
</html>

<?php
#新規投稿処理 2-1
    if (isset($_POST['create'])) {
        if (empty($_POST['name']) || empty($_POST["comment"]) || empty($_POST["password"])) {
            echo "投稿できないよ！　ちゃんと入力してね";
            exit();
        }
        
        $sql = $pdo -> prepare("INSERT INTO keijibandb (id,name,comment,date,password) VALUES (:id,:name,:comment,:date,:password)");
        $sql -> bindParam(':id',$id,PDO::PARAM_INT);
        $sql -> bindParam(':name',$name,PDO::PARAM_STR);
        $sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
        $sql -> bindParam(':date',$date,PDO::PARAM_STR);
        $sql -> bindParam(':password',$password,PDO::PARAM_STR);
        $id = NULL;
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $date = date('Y-m-d H:i:s');
        $password = $_POST["password"];
        $sql -> execute();

        //表示
        $sql ='SELECT*FROM keijibandb';
        $stmt = $pdo -> query($sql);
        $results = $stmt -> fetchAll();
        foreach($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['date'].'<br>';
        }
    }

#削除処理 2-3
    if (isset($_POST['delete'])) {
        $deleteno = $_POST['deleteno'];
        $login = $_POST['login_del'];
        
        //空の場合
        if(empty($_POST['deleteno']) || empty($_POST['login_del'])){
            echo "削除できないよ！　番号を入力してね"."<br>";
        }
        
        //空でない場合
        else if (!empty($_POST['deleteno'])){
            $sql ='SELECT*FROM keijibandb';
            $stmt = $pdo -> query($sql);
            $results = $stmt -> fetchAll();
            foreach($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                $password_del = $row['id'];
                if($login == $password_del){
                    $id = $row['id'];
                    $sql = 'delete from keijibandb where id = :id';
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(':id',$id, PDO::PARAM_INT);
                    $stmt->execute();
                }
                else{
                        echo "パスワードが一致しないよ！"."<br>";
                }
            }
        } 
    }   

#編集表示処理２                                           
    if (isset($_POST['edit'])){
        if(empty($_POST['editno']) || empty($_POST['login_edi'])){
            echo "編集できないよ！　番号を入力してね"."<br />";
        }
        else if (!empty($_POST['editno'])){
            $file = file($filename);
            foreach($file as $lines){
                $output = explode("<>", $lines); //<>で分割
                if($output[0] == $editno){
                    if($output[4] == $login){
                    }
                    else{
                        echo "パスワードが一致しないよ！"."<br>";
                    }
                }
            }
        }
    }
                                           
#編集実行処理
    if(!empty($_POST['edit2'])){
        $editno = $_POST['editno'];
        $file = file($filename);
        $fp = fopen($filename,'w');//テキストファイル初期化
        foreach($file as $lines){
            $output = explode("<>", $lines); //<>で分割
            if($output[0] != $editno){
                fwrite($fp,$lines);
            }
            else if($output[0] == $editno){
                $output[0] = $editno;
                $output[1] = $name;
                $output[2] = $comment;
                $output[4] = $password;
                $editData = "$output[0]"."<>". "$output[1]"."<>"."$output[2]"."<>"."$output[3]"."<>"."$output[4]"."<>"."\n";
                fwrite($fp, $editData);   
            }
        }
        fclose($fp);
    }                                            
?>