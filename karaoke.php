<?php
    session_start();

    $username = 'z2003465';
    $password = '2004Nov02';

try {
    $dsn = "mysql:host=courses;dbname=z2003465";
    $pdo = new PDO($dsn, $username, $password);

    echo "
    <form action='karaoke.php' method='POST'>
        <label for='search'>Search for a Song:</label>
        <input type='text' id='search' name='search_query' placeholder='Enter your search...' required>

        <label for='criteria'>Search By:</label>
        <select id='criteria' name='search_criteria'>
            <option value='title'>Title</option>
            <option value='artist'>Artist</option>
            <option value='contributor'>Contributor</option>
        </select>

        <button type='submit'>Search</button>
    </form>";


    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['search_query'])) {
        $song = $pdo->prepare('SELECT * FROM SONG WHERE TITLE = :title GROUP BY TITLE');
        $song->execute(['title' => $_POST['search_query']]);
        $search_query = $song->fetchAll(PDO::FETCH_ASSOC);


        //switch case
        switch ($_POST['search_criteria']) {
            case 'title':
                $song = $pdo->prepare('SELECT * FROM SONG WHERE TITLE = :title GROUP BY TITLE');
                $song->execute(['title' => $_POST['search_query']]);
                $search_query = $song->fetchAll(PDO::FETCH_ASSOC);

                if(empty($search_query)) {
                    echo "Song Not Available!";
                }
                else {
                    $song_id = $search_criteria[0]['SONGID'];
                    $_SESSION['song_id'] = $song_id;

                    echo "
                        <form action='karaoke.php' method='POST'>
                        <label>Would You Like to Sing this song?</label><br>
                        <input type='hidden' name='song_id' value='$song_id'>
                        <input type='submit' value='Yes' name='yes_for_sing'>
                        <input type='submit' value='No' name='no_for_sing'>
                        </form>";
                    $_SESSION['yes_for_sing'] = $_POST['yes_for_sing'];
                    }
                break;
            case 'artist':
                $artist = $pdo->prepare(
                'SELECT SONG.*, LNAME
                    FROM SONG,CONTRIBUTOR, CONTRIBUTE
                    WHERE SONG.SONGID = CONTRIBUTE.SONGID
                    AND CONTRIBUTOR.CONTRIBUTORID = CONTRIBUTE.CONTRIBUTORID
                    AND SONG.ARTIST = :artist');
                $artist->execute(['artist' => $_POST['search_query']]);
                $search_query = $artist->fetchAll(PDO::FETCH_ASSOC);

                if (empty($search_query)) {
                    echo "Artist Not Available!";
                } else {
                    // Display the songs by the artist
                    $artist = htmlspecialchars($_POST['search_query']);
                    echo "<h3>Songs by $artist</h3>";
                    echo "<form action='karaoke.php' method='POST'>";
                    echo "<table border='1'>";
                    echo "<tr><th>Song Title</th><th>Contributor</th><th>Action</th></tr>";

                    foreach ($search_query as $song) {
                    $song_id = $song['SONGID'];
                    $song_title = $song['TITLE'];
                    $contributor = $song['LNAME'];
                    echo "
                        <tr>
                            <td>$song_title</td>
                            <td>$contributor</td>
                            <td>
                            <button type='submit' name='selected_song' value='$song_id'>Select</button>
                            </td>
                        </tr>";
                }

                echo "</table>";
                echo "</form>";
                }
                break;
            case 'contributor':

                $contributor = $pdo->prepare(
                'SELECT SONG.*
                    FROM SONG
                    JOIN CONTRIBUTE ON SONG.SONGID = CONTRIBUTE.SONGID
                    JOIN CONTRIBUTOR ON CONTRIBUTE.CONTRIBUTORID = CONTRIBUTOR.CONTRIBUTORID
                    WHERE CONTRIBUTOR.LNAME LIKE :contributor');
                $contributor->execute(['contributor' => $_POST['search_query']]);
                $search_query = $contributor->fetchAll(PDO::FETCH_ASSOC);

                if (empty($search_query)) {
                    echo "Contributor Not Available!";
                } else {
                    // Display the songs by the artist
                    $artist = htmlspecialchars($_POST['search_query']);
                    echo "<h3>Songs Contributed by $artist</h3>";
                    echo "<form action='karaoke.php' method='POST'>";
                    echo "<table border='1'>";
                    echo "<tr><th>Song Title</th><th>Artist</th><th>Action</th></tr>";

                    foreach ($search_query as $song) {
                    $song_id = $song['SONGID'];
                    $song_title = $song['TITLE'];
                    $artist = $song['ARTIST'];

                    echo "
                        <tr>
                            <td>$song_title</td>
                            <td>$artist</td>
                            <td>
                            <button type='submit' name='selected_song' value='$song_id'>Select</button>
                            </td>
                        </tr>";
                }

                echo "</table>";
                echo "</form>";
                }
                break;
            default:
                break;

        }
    }

    var_dump($_SESSION);

    if (isset($_POST['yes_for_sing'])) {
        $song_id = $_SESSION['song_id'];
               $song = $pdo->prepare(
                'SELECT TITLE, ARTIST, VERSION
                from KARAOKEFILE
                Join SONG on SONG.SONGID = KARAOKEFILE.SONGID
                WHERE KARAOKEFILE.SONGID = $song_id;');
        $song->execute();
        $search_query = $song->fetchAll(PDO::FETCH_ASSOC);
        var_dump($search_query);

        if (empty($search_query)) {
            echo "Contributor Not Available!";
        }
        else {
            // Display the songs by the artist
            $artist = htmlspecialchars($_POST['search_query']);
            echo "<h3>Songs Contributed by $artist</h3>";
            echo "<form action='karaoke.php' method='POST'>";
            echo "<table border='1'>";
            echo "<tr><th>Song Title</th><th>Artist</th><th>Action</th></tr>";

            foreach ($search_query as $song) {
                $song_id = $song['SONGID'];
                $song_title = $song['TITLE'];
                $artist = $song['ARTIST'];
                $version = $song['VERSION'];

                echo "
                    <tr>
                        <td>$song_title</td>
                        <td>$artist</td>
                        <td>$version</td>
                        <td>
                        <button type='submit' name='selected_song' value='$song_id'>Select</button>
                        </td>
                    </tr>";
            }

            echo "</table>";
            echo "</form>";
        }

        echo "
            <form action='karaoke.php' method='POST'>
            <label for='fname'>First Name:</label>
            <input type='text' id='name' name='fname' required>
            <label for='lname'>Last Name:</label>
            <input type='text' id='name' name='lname' required>
            <button type='submit'>Submit</button>
            </form>";


    }
    if (isset($_POST['fname'])) {
        echo "
            <form action='karaoke.php' method='POST'>
            <input type='hidden' name='song_id' value='$song_id'>
            <label>Would you like to pay for the priority queue?</label><br>
            <input type='submit' name='pay' value='Yes'>
            <input type='submit' name='pay' value='No'>
            </form>";

    }
    if (isset($_POST['pay'])) {
        $song_id = $_POST['song_id'];
        $user_id = 1; // Replace with session data for real implementation

        if ($_POST['pay'] == 'Yes') {
            echo "Processing for the song to play in the priority queue: $song_id";
            $stmt = $pdo->prepare("INSERT INTO PICK (QUEUETYPE, USERID, SONGID, PAID, TIME) VALUES ('P', :user_id, :song_id, 10.00, NOW())");
            $stmt->execute(['user_id' => $user_id, 'song_id' => $song_id]);
        } else {
            echo "Processing for the song to play in the free queue: $song_id";
            $stmt = $pdo->prepare("INSERT INTO PICK (QUEUETYPE, USERID, SONGID, PAID, TIME) VALUES ('F', :user_id, :song_id, 0.00, NOW())");
            $stmt->execute(['user_id' => $user_id, 'song_id' => $song_id]);
        }


    }

    $songs = "SELECT DISTINCT TITLE, ARTIST, LNAME AS CONTRIBUTOR
              FROM SONG
              JOIN CONTRIBUTE ON SONG.SONGID = CONTRIBUTE.SONGID
              JOIN CONTRIBUTOR ON CONTRIBUTE.CONTRIBUTORID = CONTRIBUTOR.CONTRIBUTORID";
    $res = $pdo->query($songs);

    echo "<h3>All Songs</h3><table border='1'><tr><th>Title</th><th>Artist Name</th><th>Contributor</th></tr>";
    $allRows = $res->fetchAll(PDO::FETCH_ASSOC);
    foreach ($allRows as $i) {
        echo "<tr>";
        foreach ($i as $j) {
            echo "<td>$j</td>";
        }
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "Connection to database failed: " . $e->getMessage();
}
?>
