<?php
class NotFoundUsersClassException extends \Exception
{
    public function __construct($message = "Не найден класс User", $code = 500, $previous = null)
    {
        echo $message;
    }
}

if (class_exists('User')) {
    class ListOfUsers
    {
        /** @var array */
        public $ids;

        public function __construct($ids)
        {
            $this->ids = $ids;
        }
        // private static function getIds(): array
        // {
        //     $db = Db::getInstance();
        //     $result = $db->query(
        //         'SELECT id FROM users',
        //         []
        //     );

        //     $ids = [];

        //     foreach ($result as $id) {
        //         $ids[] = $id->id;
        //     }

        //     return $ids;
        // }

        public function getUsers(): array
        {
            $users = [];

            foreach ($this->ids as $id) {
                $data = User::getById($id);
                $users[] = new User((array)$data);
            }

            return $users;
        }

        public function delete(): void
        {
            $db = Db::getInstance();

            foreach ($this->ids as $id) {
                $db->query(
                    'DELETE FROM users `'  . '` WHERE id = :id',
                    [':id' => $id]
                );
            }
            $this->ids = null;
        }
    }
} else {
    throw new \NotFoundUsersClassException();
}
