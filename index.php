<?php
echo '<pre>';

require __DIR__ . '/User.php';
require __DIR__ . '/List.php';

class Db
{
    /** @var \PDO */
    private $pdo;

    private static $instance;

    private function __construct()
    {
        $dbOptions = [
            'host' => 'test_db',
            'port' => '3306',
            'dbname' => 'test',
            'user' => 'root',
            'password' => 'root',
        ];
        try {
            $this->pdo = new \PDO(
                'mysql:host=' . $dbOptions['host'] .
                    ';port=' . $dbOptions['port'] .
                    ';dbname=' . $dbOptions['dbname'],
                $dbOptions['user'],
                $dbOptions['password']
            );
            $this->pdo->exec('SET NAMES UTF8');
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function query(string $sql, array $params = [], string $className = 'stdClass'): ?array
    {
        $sth = $this->pdo->prepare($sql);
        $result = $sth->execute($params);

        if (false === $result) {
            return null;
        }

        return $sth->fetchAll(\PDO::FETCH_CLASS, $className);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getLastInsertId(): int
    {
        return (int)$this->pdo->lastInsertId();
    }
}

class ValidateError extends \Exception
{
    public function __construct($message = "Ошибка валидации", $code = 500, $previous = null)
    {
        echo $message;
    }
}


$data = [
    'id' => 1,
    'name' => 'Ivan',
    'surname' => 'Ivanov',
    'birthday' => '2001-01-01',
    'gender' => 1,
    'city' => 'Minsk',
];


try {
    $user = new User($data);

    // echo json_encode($user);

    // $formatterUser = $user->formatUser();

    // echo json_encode($formatterUser);

    // $user->delete();

} catch (\ValidateError $e) {
}





try {
    $list = new ListOfUsers([1, 2]);

    $users = $list->getUsers();

    echo json_encode($users);
    // $list->delete()

} catch (\NotFoundUsersClassException $th) {
}
