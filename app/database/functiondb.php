<?php

session_start();

require('connectdb.php');

function tt($value){
    echo '<pre>';
    print_r($value);
    echo '<pre>';
    exit();
}

//Проверка выполнения запроса к БД
function dbChekError($query){
    $errInfo = $query -> errorInfo();

    if ($errInfo[0] !== PDO::ERR_NONE){
        echo $errInfo[2];
        exit();
    }
    return true;
}

//Запрос на получение данных с одной таблицы
function selectAll($table, $params=[]){
    global $pdo;
    $sql = "SELECT * FROM $table";   
    if (!empty($params)){
        $i = 0;
        foreach ($params as $key => $value){
            if (!is_numeric($value)){
                $value = "'".$value."'";
            }
            if ($i === 0){
                $sql = $sql . " WHERE $key = $value";
            } else {
                $sql = $sql . " AND $key = $value";
            }
            $i++;
        }
    }

    $query = $pdo -> prepare($sql);
    $query -> execute();
    dbChekError($query);
    return $query -> fetchAll();    
}


//Запрос на получение одной строки с выбранной таблицы
function selectOne($table, $params=[]){
    global $pdo;
    $sql = "SELECT * FROM $table";   
    if (!empty($params)){
        $i = 0;
        foreach ($params as $key => $value){
            if (!is_numeric($value)){
                $value = "'".$value."'";
            }
            if ($i === 0){
                $sql = $sql . " WHERE $key = $value";
            } else {
                $sql = $sql . " AND $key = $value";
            }
            $i++;
        }
    }
    $query = $pdo -> prepare($sql);
    $query -> execute();
    dbChekError($query);
    return $query -> fetch();
     
}


// Запись в таблицу БД
function insert($table, $params){
    global $pdo;
    $i = 0;
    $coll = '';
    $mask = '';
    foreach ($params as $key => $value){
        if ($i === 0 ) {
            $coll = $coll . "$key";
            $mask =  $mask . "'" . "$value" . "'" ;
        } else {
            $coll = $coll . ", $key";
            $mask =  $mask . ", '" . "$value" . "'";
    }
        $i++;
    }

    $sql = "INSERT INTO $table ($coll) VALUES ($mask)";
    $query = $pdo -> prepare($sql);
    $query -> execute($params);
    dbChekError($query);
    return $pdo -> lastInsertId();
}

//Загрузка изображений в таблицу
function insertimg($table, $value, $idc){
    global $pdo;
   
    $sql = "INSERT INTO $table (apart1, id_category) VALUES (:pimg, :idc)";
    $values = [
    'pimg' => $value,
    'idc' => $idc
];

    $query = $pdo -> prepare($sql);
    $query -> execute($values);
    dbChekError($query);
    return $pdo -> lastInsertId();
}



// Обновление данных в таблице
function update($table, $id, $params){
    global $pdo;
    $i = 0;
    $str = '';
    foreach ($params as $key => $value){
        if ($i === 0 ) {
            $str =  $str . $key . " = '" . $value . "'" ;
        } else {
            $str =  $str .", " . $key . " = '" . $value . "'";
    }
        $i++;
    }
    
    $sql = "UPDATE $table SET $str WHERE id = $id";
    $query = $pdo -> prepare($sql);
    $query -> execute($params);
    dbChekError($query);
}

// Выбор названия категории
function selectCategory($table1, $table2){
    global $pdo;
    $sql = "SELECT ap.title, rm.* FROM $table1 AS ap JOIN $table2 AS rm ON ap.id=rm.id_category";
    $query = $pdo -> prepare($sql);
    $query -> execute();
    dbChekError($query);
    return $query -> fetchAll();
}

// Выбор id категории
function selectIdCategory($table1, $category){
    global $pdo;
    $sql = "SELECT id FROM $table1 WHERE title='$category'";
    $query = $pdo -> prepare($sql);
    $query -> execute();
    dbChekError($query);
    return $query -> fetch();
}

// Удаление
function delete($table, $id){
    global $pdo;

    $sql = "DELETE FROM $table WHERE id =". $id ;
    $query = $pdo -> prepare($sql);
    $query -> execute();
    dbChekError($query);
}

// Поиск
function searchRooms($text, $table){
    $text = trim(strip_tags(stripcslashes(htmlspecialchars($text))));
    global $pdo;

    $sql = "SELECT * FROM $table WHERE title LIKE '%$text%' OR description LIKE '%$text%' ";
    $query = $pdo -> prepare($sql);
    $query -> execute();
    dbChekError($query);
    return $query -> fetchAll();
}
