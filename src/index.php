<?php
require_once '../vendor/autoload.php';
require_once 'config/db.php';
$date = date('Y-m-d H:i:s');

/**
 * @return array
 */
function getRequestData()
{
    return [
        'method'  => $_SERVER['REQUEST_METHOD'],
        'headers' => getallheaders(),
        'body'    => file_get_contents('php://input'),
        'params'  => $_REQUEST,
        'uri'     => $_SERVER['REQUEST_URI']
    ];
}

/**
 * @throws Exception
 */
function logRequestToFile($data)
{
    global $date;
    try {
        $logDir = __DIR__.'/logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFileName = date('Y-m-d').'.log';
        $logFile = $logDir.'/'.$logFileName;
        $logEntry = "[".$date."] ".json_encode($data).PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    } catch (\Exception $e) {
        throw new \Exception($e->getMessage(), (int) $e->getCode());
    }
}

/**
 * @param $pdo
 * @param $data
 * @return void
 */
function logRequestToDatabase($pdo, $data)
{
    global $date;
    $sql = "INSERT INTO logs (method, headers, body, params, uri, created_at) VALUES (:method, :headers, :body, :params, :uri, :created_at)";
    $statement = $pdo->prepare($sql);
    $statement->execute([
        ':method'     => $data['method'],
        ':headers'    => json_encode($data['headers']),
        ':body'       => $data['body'],
        ':params'     => json_encode($data['params']),
        ':uri'        => $data['uri'],
        ':created_at' => $date
    ]);
}

try {
    header("Content-Type: application/json; charset=UTF-8");
    $response = new \App\Library\Response();
    $db = \App\Config\DB\getDatabaseConnection();
    $requestData = getRequestData();
    logRequestToFile($requestData);
    logRequestToDatabase($db, $requestData);
    $response->setStatus(true);
    $response->setStatusCode(200);
    $response->setMessage("Request logged.");
    $response->setDate($date);
    echo $response->toJson();
    exit();
} catch (\Exception $e) {
    throw new \Exception($e->getMessage(), (int) $e->getCode());
}