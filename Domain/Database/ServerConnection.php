<?php

namespace Domain\Database {
    trait ServerConnection
    {
        private string $server = "localhost";
        private string $user = "root";
        private string $password = "";
        private string $database = "classes";
        private string $charset = "utf8mb4";
        private int $port = 3307;
    }
}
