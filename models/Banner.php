<?php

class Banner {

    private static $visible = array();
    private static $countBanners;

    /*
     * Получаем список всех баннеров с БД
     */
    public static function takeBanners($priority = 0) {

        $banners = array();

        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
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

    /*
     * Проверка баннера на доступноть к показу по ID
     */
    public static function getAvailableById($id) {
        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        $result = mysqli_query($mysqli, "SELECT `available` FROM banner where id ='" . $id . "'");
        mysqli_close($mysqli);

        while ($row = mysqli_fetch_assoc($result)) {

            $available = $row['available'];
        }
        return $available;
    }

    /*
     * Получаем достпуность всех баннеров в БД
     */
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

    /*
     * Получаем кол-во показов баннера из БД
     * если ID указано то получаем один баннер,
     * в ином случае получаем все.
     */
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

    /*
     * устанавливаем доступность баннера по ID,
     * если указать "1" то баннер будет доступен,
     * если указать "0" то недоступен.
     */
    public static function setAvailable($id, $bool = 1) {
        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        mysqli_query($mysqli, "UPDATE banner SET available='" . $bool . "' WHERE id=" . $id);
        mysqli_close($mysqli);
    }

    /*
     * Меняем количество показа баннера по ID
     */
    public static function setShow($id, $value) {
        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        mysqli_query($mysqli, "UPDATE `banners`.`banner` "
                . "SET `show` = '" . $value . "' "
                . "WHERE `banner`.`id` = " . $id);
        mysqli_close($mysqli);
    }

    /*
     * Считаем сколько всего осталось доступных банеров,
     * если их меньше чем нужно для заполнения всех
     * баннерных позиций на странице, то возвращаем false,
     * если их больше, то возвращаем true
     */
    public static function countAvailable($countOnPage) {
        $i = 0;
        $countAvailable = 0;
        $available = self::getAvailable();
        while ($i < self::$countBanners) {
            if ($available[$i] == 1) {
                $countAvailable++;
            }
            $i++;
        }
        return ($countAvailable >= $countOnPage) ? true : false;
    }

    /*
     * Увеличиваем количество показов баннера в БД
     */
    public static function updateShow($id, $banners) {
        $reset = false;
        /*
         * проверяем банер на доступность,
         * если доступен то выводим его 
         * увеличивая кол-во показов и 
         * проверяем его на дальнейшую доступность
         */
        if (self::checkAvialable($id)) {
            $count = self::getShow()[$id - 1] + 1;
            self::setShow($id, $count);
            if ($count >= $banners[$id - 1]['priority']) {
                self::setAvailable($id, 0);
            }
        } 
        /*
         * если банер недоступен,
         * пытаемся найти хоть один
         * доступный банер,
         * если такового нет то
         * мы сбрасываем количество показов
         * у всех банеров и делаем их доступными
         */
        if (!self::checkAvialable($id)) {
            $i = 0;
            $countNotAvailable = 0;
            $available = self::getAvailable();
            while ($i < self::$countBanners) {
                if ($available[$i] == 0) {
                    $countNotAvailable++;
                }
                $i++;
            }
            $reset = ($countNotAvailable == self::$countBanners - 1) ? true : false;
        }
       
        if ($reset) {
            for ($i = 0; $i < self::$countBanners; $i++) {
                self::resetShow($banners[$i]['id']);
                self::setAvailable($banners[$i]['id']);
            }
        }
    }

    /*
     * Обнуляем количество показов баннера на странице по ID
     */
    public static function resetShow($id) {
        $mysqli = mysqli_connect(HOST, USER, PASS, DB);
        mysqli_query($mysqli, "UPDATE `banners`.`banner` "
                . "SET `show` = '0' "
                . "WHERE `banner`.`id` = " . $id);
        mysqli_close($mysqli);
    }

    /*
     * Сверяем баннеры на приоритет, пока не найдем нужный.
     */
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
    
    /*
     * Проверяем есть ли уже такой баннер на странице по ID
     */
    public static function checkVisible($id) {
        if (in_array($id, self::$visible)) {
            return false;
        } else {
            return true;
        }
    }
    
    /*
     * Проверяем доступен ли баннер по ID
     */
    public static function checkAvialable($id) {
        if (self::getAvailableById($id) == 1) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Показываем баннер на странице
     */
    public static function showBanner($priority = 0) {
        $text = array();
        $show = false;
        $banners = self::takeBanners();
        self::$countBanners = count($banners);
        /*
         * Генерируем баннер с установленным приоритетом
         */
        $index = self::checkPriority($priority, $banners);
        /*
         * Если доступно больше баннеров,
         * чем отображается на странице,
         * тогда мы проверяем их на уникальность,
         * в ином случае мы просто их выводим
         */
        if (self::countAvailable(3)) {// если доступно больше баннеров,чем отображается на странице
            /*
             * Наличие одинаковых банеров на странице
             */
            while (!$show) {
                if (self::checkVisible($banners[$index]['id']) && self::checkAvialable($banners[$index]['id'])) {
                    $show = true;
                    self::updateShow($banners[$index]['id'], $banners); //увеличиваем число показов
                    array_push(self::$visible, $banners[$index]['id']); //добавляем банер в список баннеров на странице
                    return $banners[$index]['text'];
                } else {
                    $index = self::checkPriority($priority, $banners); //выбираем новый банер
                }
            }
        } else {
            while (!$show) {
                if (self::checkAvialable($banners[$index]['id'])) {
                    self::updateShow($banners[$index]['id'], $banners); //увеличиваем число показов
                    array_push(self::$visible, $banners[$index]['id']); //добавляем банер в список баннеров на странице
                    return $banners[$index]['text'];
                } else {
                    $index = self::checkPriority($priority, $banners);
                }
            }
        }
    }

}
