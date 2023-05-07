<?php

class User
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $surname;

    /** @var string */
    public $birthday;

    /** @var boolean */
    public $gender;

    /** @var string */
    public $city;

    public function __construct($data)
    {
        $validated = $this->validate($data);

        if (!empty($validated)) {
            foreach ($validated as $error) {
                echo '<div>' .  $error . '</div>';
            }
            throw new \ValidateError();
        }

        $result = User::getById($data['id']);

        if (!empty($result)) {
            $user = $result;
        } else {
            $this->insert($data);

            $result = User::getById($data['id']);

            $user = $result;
        }

        $this->id = $user->id;
        $this->name = $user->name;
        $this->surname = $user->surname;
        $this->birthday = $user->birthday;
        $this->gender = $user->gender;
        $this->city = $user->city;
    }

    private function insert($data): void
    {
        $columns = [];
        $params = [];
        $values = [];

        $db = Db::getInstance();

        foreach ($data as $key => $value) {
            $columns[] = '`' . $key . '`';
            $paramName = ':' . $key;
            $values[] = $paramName;
            $params[$paramName] = $value;
        }

        $columns = implode(', ', $columns);
        $values = implode(', ', $values);

        $sql = 'INSERT INTO users ' .  ' (' . $columns . ') VALUES (' . $values . ');';

        $db->query($sql, $params);
    }

    private function validate($data): array
    {
        $errors = [];

        if (empty($data)) {
            $errors[] = 'Данных нет';
        }

        if (!isset($data['id'])) {
            $errors[] = 'Поле id должно быть заполнено!';
        }
        if (!isset($data['name']) || $data['name'] === '') {
            $errors[] = 'Поле name должно быть заполнено!';
        }
        if (!isset($data['gender'])) {
            $errors[] = 'Поле gender должно быть заполнено!';
        }
        if (!isset($data['surname']) || $data['surname'] === '') {
            $errors[] = 'Поле surname должно быть заполнено!';
        }
        if (!isset($data['birthday']) || $data['birthday'] === '') {
            $errors[] = 'Поле birthday должно быть заполнено!';
        }
        if (!isset($data['city']) || $data['city'] === '') {
            $errors[] = 'Поле city должно быть заполнено!';
        }

        if (gettype($data) != 'array') {
            $errors[] = 'Неверный тип данных!';
        }
        if (gettype($data['id']) != 'integer') {
            $errors[] = 'Id должен быть числом!';
        }
        if (gettype($data['name']) != 'string') {
            $errors[] = 'Имя должно быть строкой!';
        }
        if (gettype($data['surname']) != 'string') {
            $errors[] = ' Фамилия должна быть строкой!';
        }
        if (gettype($data['birthday']) != 'string') {
            $errors[] = 'Дата рождения должна быть строкой!';
        }
        if (gettype($data['city']) != 'string') {
            $errors[] = 'Город должен быть строкой!';
        }
        if ($data['gender'] != 1 && $data['gender'] != 0) {
            $errors[] = 'Гендер должен быть буллевым значением';
        }

        return $errors;
    }

    public function delete(): void
    {
        $db = Db::getInstance();

        $db->query(
            'DELETE FROM users `'  . '` WHERE id = :id',
            [':id' => $this->id]
        );
        $this->id = null;
    }
    public static function getById($id): ?stdClass
    {
        $db = Db::getInstance();
        $result = $db->query(
            'SELECT * FROM users WHERE id=:id;',
            [':id' => $id]
        );

        return $result[0] ?? $result;
    }

    private static function getGender($gender): string
    {
        return $gender === 0 ? 'Женщина' : 'Мужчина';
    }

    private static function getAge($birthday): int
    {
        return date_diff(new DateTime(), new DateTime($birthday))->y;
    }

    public function formatUser(): stdClass
    {
        $user = new stdClass();

        $user->id = $this->id;
        $user->name = $this->name;
        $user->surname = $this->surname;
        $user->age = User::getAge($this->birthday);
        $user->gender = User::getGender($this->gender);
        $user->city = $this->city;

        return $user;
    }
}
