<?php

class Banner {

    private static $visible = array();
    private static $countBanners;
    private static $mysqli;

    /*
     * Получаем список всех баннеров с БД
     */

    private static function takeBanners() {
        if (!isset($_SESSION['banners'])) {
            self::$mysqli = new mysqli(HOST, USER, PASS, DB);
            $result = self::$mysqli->query("SELECT * FROM banner ");
            $i = 0;

            while ($row = $result->fetch_assoc()) {


                $_SESSION['banners'][$i] = ['id' => $row['id'],
                    'text' => $row['text'],
                    'priority' => $row['priority'],
                    'show' => 0,
                    'available' => 1];


                $i++;
            }
            self::$mysqli->close();
        }
        return $_SESSION['banners'];
    }

    /*
     * Проверка баннера на доступноть к показу по ID
     */

    private static function getAvailableById($id) {
        $available = $_SESSION['banners'][$id - 1]['available'];
        return $available;
    }

    /*
     * Получаем достпуность всех баннеров в БД
     */

    private static function getAvailable() {
        $available = array();
        for ($i = 0; $i < count($_SESSION['banners']); $i++) {
            $available[$i] = $_SESSION['banners'][$i]['available'];
        }
        return $available;
    }

    /*
     * Получаем кол-во показов баннера из БД
     * если ID указано то получаем один баннер,
     * в ином случае получаем все.
     */

    private static function getShow($id = -1) {
        if ($id < 0) {
            for ($i = 0; $i < count($_SESSION['banners']); $i++) {
                $show[$i] = $_SESSION['banners'][$i]['show'];
            }
        } else {
            $show = $_SESSION['banners'][$id]['show'];
        }
        return $show;
    }

    /*
     * устанавливаем доступность баннера по ID,
     * если указать "1" то баннер будет доступен,
     * если указать "0" то недоступен.
     */

    private static function setAvailable($id, $bool = 1) {
        $_SESSION['banners'][$id - 1]['available'] = $bool;
    }

    /*
     * Меняем количество показа баннера по ID
     */

    private static function setShow($id, $value) {
        $_SESSION['banners'][$id - 1]['show'] = $value;
    }

    /*
     * Считаем сколько всего осталось доступных банеров,
     * если их меньше чем нужно для заполнения всех
     * баннерных позиций на странице, то возвращаем false,
     * если их больше, то возвращаем true
     */

    private static function countAvailable($countOnPage) {
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

    private static function updateShow($id, $banners) {
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

            $reset = ($countNotAvailable == self::$countBanners ) ? true : false;
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

    private static function resetShow($id) {
        $_SESSION['banners'][$id - 1]['show'] = 0; //уд
    }

    /*
     * Сверяем баннеры на приоритет, пока не найдем нужный.
     */

    private static function checkPriority($priority, $banners) {
        $exit = false;
        $countBanners = count($banners) - 1;
        while (!$exit) {
            $index = rand(0, $countBanners);
            if ($banners[$index]['priority'] >= $priority) {
                $exit = true;
            }
        }
        return $index;
    }

    /*
     * Проверяем есть ли уже такой баннер на странице по ID
     */

    private static function checkVisible($id) {
        if (in_array($id, self::$visible)) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * Проверяем доступен ли баннер по ID
     */

    private static function checkAvialable($id) {
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
        $show = false;
        $banners = self::takeBanners();
        self::$countBanners = count($banners);
        /*
         * Генерируем баннер с установленным приоритетом
         */
        $index = self::checkPriority($priority, $banners);
        //  var_dump($_SESSION['banners']);
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
                    // self::$mysqli->close();
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
                    //self::$mysqli->close();
                    return $banners[$index]['text'];
                } else {
                    $index = self::checkPriority($priority, $banners);
                }
            }
        }
    }

}
