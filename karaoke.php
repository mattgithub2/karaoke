<html>
<?php
    
    $username = 'z2003465';
    $password = '2004Nov02';
error_reporting(E_ALL);
ini_set('display_errors', 1);
    try 
    { // if something goes wrong, an exception is thrown
        $dsn = "mysql:host=courses;dbname=z2003465";
        $pdo = new PDO($dsn, $username, $password);

        echo "
            <form action=karaoke.php method=POST>
            <label for=searchbar>Search for a song</label><br>
            <input id=searchbar type=text name=searched>
            <input type=submit value=submit>
            </form>";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['searched']) || isset($_POST['yes_for_sing'])) {
                $stmt = $pdo->prepare('select * from SONG where TITLE = :title');   
                $stmt->execute(['title' => $_POST['searched']]);
                $search_query = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$search_query) {
                    echo "No results found.";
                }
                else {
                    echo
                        "<form action=karaoke.php method=POST>
                        <label for=yes_play>Would you like to sing this song?</label><br>
                        <input id=yes_play type=submit value=yes name=yes_for_sing>
                        <input id=no_play type=submit value=No name=no_for_sing><br></form>";

    
                        var_dump($_POST);   
                        if (isset($_POST['yes_for_sing'])){
                            echo " <form action=karaoke.php method=POST>
                                <label for=yes_pay>Would you like to pay to enter the priority queue?</label><br>
                                <input id=yes_pay  type=button value=yes name=Yes>
                                <input id=no_pay type=button value=No name=No></form><br>";
                        }



                }
            }
        }


            
        $songs = "SELECT TITLE, ARTISTNAME, LNAME AS `CONTRIBUTOR` FROM SONG, CONTRIBUTE, CONTRIBUTOR WHERE SONG.SONGID = CONTRIBUTE.SONGID AND CONTRIBUTOR.CONTRIBUTORID = CONTRIBUTE.CONTRIBUTORID GROUP BY TITLE;";
        $res = $pdo->query($songs);

        echo "<h3>All Songs</h3><table border='1'><tr><th>Title</th><th>Artist Name</th><th>Contributor</th></tr>";
        $allRows = $res->fetchAll(PDO::FETCH_ASSOC);
        foreach ($allRows as $i)
        {
            echo "<tr>";
            foreach($i as $j)
            {
                echo "<td>$j</td>";
            }
            echo "</tr>";
        }
 
    }
    catch(PDOexception $e) 
    { // handle that exception
        echo "Connection to database failed: " . $e->getMessage();
    }


    
        
?>
</html>
