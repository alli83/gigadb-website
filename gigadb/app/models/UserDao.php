<?php

namespace GigaDB\models;

class UserDao
{
    public function findByEmail(string $email): ?\User
    {
        return \User::model()->find('email = :email', array(
            ':email' => $email
        ));
    }

}
