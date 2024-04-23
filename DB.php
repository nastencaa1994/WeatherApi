<?

class DB
{
    public $conn;
    public function __construct()
    {

        $serverName = 'serverName';
        $user = 'user';
        $password = 'password ';
        $dataBase = 'database ';
        $this->conn =  mysqli_connect(  $serverName,  $user, $password, $dataBase);

        if ($this->conn) {
            echo "Connection established.<br />";

        } else {
            echo "Connection could not be established.<br />";
            die(print_r(sqlsrv_errors(), true));
        }
    }
    public function ReadData($sql){
        $ret=[];

        $result = mysqli_query( $this->conn, $sql);
        if( $result === false ) {
            print_r($sql);
            die;
        }
        while($row = mysqli_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $ret['Items'][] = $row;
        }
        if (array_key_exists('Items', $ret)) {
            $ret['Total'] = count($ret['Items']);
        }
        else {
            $ret['Total'] = 0;
        }
        return $ret;
    }
}