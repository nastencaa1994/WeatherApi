<?php
include_once  'DB.php';


class WeatherApi
{

    public $latitude = '55.7522';
    public $longitude = '37.6156';
    public $queryArr;
    public $curl;


    function __construct()
    {
        $this->curl = curl_init();
        $this->queryArr = [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'daily' => [
                'temperature_2m_max',//температура макс
                'temperature_2m_min',//температура min
                'precipitation_sum', //осадки
                'rain_sum',//Сумма суточного дождя
                'snowfall_sum',//Сумма суточного снегопада
                'wind_speed_10m_max',//скороть ветра мах
                'wind_direction_10m_dominant',// напровление ветра
            ],

        ];
    }

    public function addTableArchiveOneDay($date, $type = 'archive')
    {
        // Y-m-d //forecast
        switch ($type) {
            case 'archive':
                $url = "https://archive-api.open-meteo.com/v1/era5";
                $this->queryArr['start_date'] = $date;
                $this->queryArr['end_date'] = $date;
                break;
            case 'forecast':
                $url = "https://api.open-meteo.com/v1/forecast";//прогноз
                break;
        }
        $this->queryArr = http_build_query($this->queryArr);
        curl_setopt_array($this->curl, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_POST => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url . "?" . $this->queryArr,
        ));
        $result = curl_exec($this->curl);
        curl_close($this->curl);
        $result = json_decode($result, true);
        switch ($type) {
            case 'archive':
               return $this->addDbArchive($result['daily'],$date);
                break;
            case 'forecast':
                return $result['daily'];
                break;
        }

    }

    public function addDbArchive($daily,$date)
    {
        $db = new DB;//клас для работы с базой данных
        $sql ="SELECT *
               FROM Weather
               WHERE Date = '".$date."'";

        $res = $db->ReadData($sql);
        if($res['Total']>0) return 'запись за '.$date.' уже есть';


        $sql = "insert into Weather (Date,apparent_temperature_max, apparent_temperature_min, precipitation_sum, rain_sum, snowfall_sum, wind_speed_max, wind_direction_dominant)
        values
                        ";
        foreach ($daily['time'] as $index => $item) {
            $sql .= "('" . $item . "', 
    '" . $daily['temperature_2m_max'][$index] . "',  
    '" . $daily['temperature_2m_min'][$index] . "',  
    '" . $daily['precipitation_sum'][$index] . "', '" . $daily['rain_sum'][$index] . "', 
    '" . $daily['snowfall_sum'][$index] . "','" . $daily['wind_speed_10m_max'][$index] . "',
    '" . $daily['wind_direction_10m_dominant'][$index] . "')";
            if (count($daily['time']) != ($index + 1)) {
                $sql .= ", ";
            }
        }

        $res = $db->ReadData($sql);
        return $res;

    }
}



