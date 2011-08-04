<!doctype html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <?php
        $db = new Kohana_Isf();
        $db->DbConnect();
        $r = $db->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');
        $r = $r[1]['wartosc'];
        insert_log('admin.token', 'Uzytkownik '.$_SESSION['user'].' generuje tokeny dla uzytkownika '.$id);
        ?>
        <title>System RAND_TOKEN</title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>lib/css/style.css"/>
    </head>
    <body>
        <a href="#" onClick="window.print();"><img border="0" src="<?php echo URL::base() ?>lib/images/printer.png" alt="[drukuj]"/></a>
        <table class="przed" style="max-width: 80%">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <tr>
                    <?php
                    $randev = rand(0, 1000);
                    $sum = sha1(time() + $randev);
                    $s2 = md5(rand(1000,10000));
                    $ret = substr($sum, 0, 3).substr($s2, 0, 3);
                    $chk = $db->DbSelect('tokeny', array('*'), 'where token=\''.$ret.'\'');
                    $l = $db->DbSelect('uzytkownicy', array('*'), 'where uid=\''.$id.'\'');
                    if(count($l)==0){
                        echo 'Uzytkownik nie istnieje!';
                        exit;
                    }
                    $db->DbInsert('tokeny', array('login' => $l[1]['login'], 'token' => md5('plan'.$ret)));
                    ?>
                    <td>
                        <h1>RAND_TOKEN</h1>
                        <h3><?php echo $r; ?></h3>
                        <pre>
<b>LOGIN: </b><?php echo $l[1]['login']; ?><br/>
<b>TOKEN: </b><?php echo $ret; ?>
                        </pre>
                        Do korzystania z tokena, uprawniony jest wyłącznie jego właściciel
                    </td>
                    <?php
                    rand1:
                    $randev = rand(0, 1000);
                    $sum = sha1(time() + $randev);
                    $s2 = md5(rand(1000,10000));
                    $ret = substr($sum, 0, 3).substr($s2, 0, 3);
                    $l = $db->DbSelect('uzytkownicy', array('*'), 'where uid=\''.$id.'\'');
                    if(count($l)==0){
                        echo 'Uzytkownik nie istnieje!';
                        exit;
                    }
                    $db->DbInsert('tokeny', array('login' => $l[1]['login'], 'token' => md5('plan'.$ret)));
                    ?>
                    <td>
                        <h1>RAND_TOKEN</h1>
                        <h3><?php echo $r; ?></h3>
                        <pre>
<b>LOGIN: </b><?php echo $l[1]['login']; ?><br/>
<b>TOKEN: </b><?php echo $ret; ?>
                        </pre>
                        Do korzystania z tokena, uprawniony jest wyłącznie właściciel
                    </td>
                </tr>
            <?php endfor; ?>
        </table>
    </body>
</html>
