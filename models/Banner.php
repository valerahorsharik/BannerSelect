<?php

class Banner {

    private static $visible = array();

    public static function takeBanners($priority = 0) {
        $banners = array();
        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        // $result = mysqli_query($mysqli, "SELECT * FROM banner WHERE priority >= '" . $priority."'");
        $result = mysqli_query($mysqli, "SELECT * FROM banner ");
        mysqli_close($mysqli);
        $i = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $banners[$i]['id'] = $row['id'];
            $banners[$i]['text'] = $row['text'];
            $banners[$i]['priority'] = $row['priority'];

            $i++;
        }

        return $banners;
    }

    public static function getAvailableById($id) {
        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        $result = mysqli_query($mysqli, "SELECT `available` FROM banner where id ='" . $id . "'");
        mysqli_close($mysqli);

        while ($row = mysqli_fetch_assoc($result)) {

            $available = $row['available'];
        }
        return $available;
    }

    public static function getAvailable() {
        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        $available = array();
        $result = mysqli_query($mysqli, "SELECT `available` FROM banner ");

        mysqli_close($mysqli);
        $i = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $available[$i] = $row['available'];
            $i++;
        }
        return $available;
    }

    public static function getShow($id = -1) {
        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        if ($id < 0) {
            $result = mysqli_query($mysqli, "SELECT `show` FROM banner ");
        } else {
            $result = mysqli_query($mysqli, "SELECT `show` FROM banner where id ='" . $id . "'");
        }

        mysqli_close($mysqli);
        $i = 0;

        while ($row = mysqli_fetch_assoc($result)) {


            $show[$i] = $row['show'];

            $i++;
        }
        return $show;
    }

    public static function setAvailable($id, $bool = 1) {
        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        // $result = mysqli_query($mysqli, "SELECT * FROM banner WHERE priority >= '" . $priority."'");
        mysqli_query($mysqli, "UPDATE banner SET available='" . $bool . "' WHERE id=" . $id);
        mysqli_close($mysqli);
    }

    public static function setShow($id, $value) {

        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        mysqli_query($mysqli, "UPDATE `banners`.`banner` "
                . "SET `show` = '" . $value . "' "
                . "WHERE `banner`.`id` = " . $id);
        mysqli_close($mysqli);
    }

    public static function updateShow($id, $banners) {
        $reset = false;
        if (self::checkAvialable($id)) {
            $count = self::getShow()[$id - 1] + 1;
            self::setShow($id, $count);
            if ($count >= $banners[$id - 1]['priority']) {
                self::setAvailable($id, 0);
            }
        } else {
            $i = 0;
            $countNotAvailable = 0;
            $available = self::getAvailable();

            while ($i < count($banners)) {
                if ($available[$i] == 0) {
                    $countNotAvailable++;
                }
                $i++;
            }
            $reset = ($countNotAvailable == count($banners) - 1) ? true : false;
        }

        var_dump(count($banners));
        var_dump($reset);
        if ($reset) {
            for ($i = 0; $i < count($banners); $i++) {
                self::resetShow($banners[$i]['id']);
                self::setAvailable($banners[$i]['id']);
            }
        }
    }

    public static function resetShow($id) {
        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        mysqli_query($mysqli, "UPDATE `banners`.`banner` "
                . "SET `show` = '0' "
                . "WHERE `banner`.`id` = " . $id);
        mysqli_close($mysqli);
    }

    public static function checkPriority($priority, $banners) {
        $exit = false;
        while (!$exit) {
            $index = rand(0, count($banners) - 1);
            if ($banners[$index]['priority'] >= $priority) {
                $exit = true;
            }
        }
        return $index;
    }

    public static function checkVisible($id) {
        if (in_array($id, self::$visible)) {
            return false;
        } else {
            return true;
        }
    }

    public static function checkAvialable($id) {
        if (self::getAvailableById($id) == 1) {
            echo "Y ";
            return true;
        } else {
            echo "F ";
            return false;
        }
    }

    public static function showBanner($priority = 0) {
        $text = array();
        $show = false;
        $banners = self::takeBanners();

        /*
         * раскоментировать для выборки из БД с условием приоритета
         * $index = rand(0,count($banners)-1);
         * for ($i = 0; $i < count($banners); $i++) {
         * $text[$i] = $banners[$i]['text'];
         * }
         */
        /*
         * Генерирую баннер с установленным приоритетом
         */
        $index = self::checkPriority($priority, $banners);

        /*
         * Наличие одинаковых банеров на странице
         */
        while (!$show) {
            // if (self::checkAvialable($banners[$index]['id'])) {
            if (self::checkVisible($banners[$index]['id'])) {
                $show = true;
                // self::checkAvialable($banners[$index]['id']); //проверка доступности
                self::updateShow($banners[$index]['id'], $banners); //увеличиваем число показов
                array_push(self::$visible, $banners[$index]['id']); //добавляем банер в список баннеров на странице
                return $banners[$index]['text'];
                //  }
            } else {
                $index = self::checkPriority($priority, $banners); //выбираем новый банер
            }
        }
    }

}
